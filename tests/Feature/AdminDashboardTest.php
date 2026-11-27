<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Club;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_event_and_club_participation_metrics(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $student = User::factory()->create();
        $club = Club::create([
            'name' => 'Robotics Club',
            'acronym' => 'ROBO',
            'description' => 'A student club.',
            'email' => 'robotics@example.test',
        ]);
        $category = Category::create(['name' => 'Technology']);
        $event = Event::create([
            'title' => 'Robot showcase',
            'description' => 'Student projects.',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(2),
            'location' => 'Main hall',
            'capacity' => 30,
            'category_id' => $category->id,
            'club_id' => $club->id,
            'created_by' => $admin->id,
        ]);
        Registration::create(['user_id' => $student->id, 'event_id' => $event->id]);
        $club->members()->attach($student->id);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Analytics dashboard')
            ->assertSee('Robot showcase')
            ->assertSee('Robotics Club')
            ->assertViewHas('eventCount', 1)
            ->assertViewHas('upcomingEventCount', 1)
            ->assertViewHas('registrationCount', 1)
            ->assertViewHas('clubCount', 1)
            ->assertViewHas('clubMemberCount', 1)
            ->assertViewHas('monthlyActivity', fn ($months) => $months->count() === 6
                && $months->last()['registrations'] === 1
                && $months->last()['memberships'] === 1);

        $this->get(route('admin.analytics.events'))
            ->assertOk()
            ->assertSee('Events ranked by participants')
            ->assertSee('Most popular event')
            ->assertSee('Monthly participant registrations')
            ->assertSee((string) now()->year);

        $this->get(route('admin.analytics.clubs'))
            ->assertOk()
            ->assertSee('Clubs ranked by membership')
            ->assertSee('Robotics Club')
            ->assertSee('Monthly club growth and event participation');
    }

    public function test_dashboard_is_not_available_to_students(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
