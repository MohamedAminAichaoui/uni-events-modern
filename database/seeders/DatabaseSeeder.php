<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Categories ----
        $categories = collect([
            'Job Fair', 'Career Talk', 'Workshop', 'Hackathon',
            'Conference', 'Competition', 'Social', 'Sports',
        ])->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name])]);

        // ---- Clubs ----
        $ieee = Club::create([
            'name' => 'IEEE Student Branch', 'acronym' => 'IEEE',
            'description' => 'Technical workshops, talks and projects in engineering, electronics and computing.',
            'email' => 'ieee@univ.test',
        ]);
        $enactus = Club::create([
            'name' => 'Enactus', 'acronym' => 'ENACTUS',
            'description' => 'Students building entrepreneurial projects with a social and economic impact.',
            'email' => 'enactus@univ.test',
        ]);
        $gdg = Club::create([
            'name' => 'Google Developer Group', 'acronym' => 'GDG',
            'description' => 'Study jams, hackathons and developer community events.',
            'email' => 'gdg@univ.test',
        ]);
        Club::create([
            'name' => 'Rotaract Club', 'acronym' => 'ROTARACT',
            'description' => 'Community service, charity drives and leadership development.',
            'email' => 'rotaract@univ.test',
        ]);

        // ---- Users (password for all: "password") ----
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@univ.test',
            'password' => 'password', 'role' => User::ROLE_ADMIN,
        ]);
        $ieeeManager = User::create([
            'name' => 'IEEE Manager', 'email' => 'ieee.manager@univ.test',
            'password' => 'password', 'role' => User::ROLE_CLUB_MANAGER, 'club_id' => $ieee->id,
        ]);
        $enactusManager = User::create([
            'name' => 'Enactus Manager', 'email' => 'enactus.manager@univ.test',
            'password' => 'password', 'role' => User::ROLE_CLUB_MANAGER, 'club_id' => $enactus->id,
        ]);
        $student = User::create([
            'name' => 'Student One', 'email' => 'student1@univ.test', 'password' => 'password',
        ]);
        User::create([
            'name' => 'Student Two', 'email' => 'student2@univ.test', 'password' => 'password',
        ]);

        $student->joinedClubs()->attach([$ieee->id, $enactus->id]);

        // ---- Events ----
        $at = fn (int $days, int $hour) => now()->addDays($days)->setTime($hour, 0);

        $fair = Event::create([
            'title' => 'Spring Job Fair', 'category_id' => $categories['Job Fair']->id,
            'club_id' => null, 'created_by' => $admin->id,
            'description' => 'Meet recruiters from more than 30 companies, drop off your CV and book interviews.',
            'start_date' => $at(14, 9), 'end_date' => $at(14, 17),
            'location' => 'Main Hall', 'capacity' => 500,
        ]);
        Event::create([
            'title' => 'Alumni Career Talk', 'category_id' => $categories['Career Talk']->id,
            'club_id' => null, 'created_by' => $admin->id,
            'description' => 'Former students share how they found their first job and what they would do differently.',
            'start_date' => $at(7, 14), 'end_date' => $at(7, 16),
            'location' => 'Amphitheatre A', 'capacity' => 200,
        ]);
        Event::create([
            'title' => 'Arduino Workshop', 'category_id' => $categories['Workshop']->id,
            'club_id' => $ieee->id, 'created_by' => $ieeeManager->id,
            'description' => 'Hands-on introduction to microcontrollers. Bring your laptop; kits are provided.',
            'start_date' => $at(5, 10), 'end_date' => $at(5, 13),
            'location' => 'Electronics Lab', 'capacity' => 25,
        ]);
        Event::create([
            'title' => 'IEEE Day', 'category_id' => $categories['Conference']->id,
            'club_id' => $ieee->id, 'created_by' => $ieeeManager->id,
            'description' => 'A day of talks and project demos celebrating engineering innovation.',
            'start_date' => $at(21, 9), 'end_date' => $at(21, 16),
            'location' => 'Conference Room', 'capacity' => 120,
        ]);
        Event::create([
            'title' => 'Social Impact Pitch Competition', 'category_id' => $categories['Competition']->id,
            'club_id' => $enactus->id, 'created_by' => $enactusManager->id,
            'description' => 'Teams pitch a project that solves a local problem in front of a jury of professionals.',
            'start_date' => $at(10, 13), 'end_date' => $at(10, 18),
            'location' => 'Amphitheatre B', 'capacity' => 150,
        ]);
        Event::create([
            'title' => '24h Hackathon', 'category_id' => $categories['Hackathon']->id,
            'club_id' => $gdg->id, 'created_by' => $admin->id,
            'description' => 'Build a working prototype in 24 hours. Teams of up to 4 students.',
            'start_date' => $at(30, 9), 'end_date' => $at(31, 9),
            'location' => 'Computer Science Building', 'capacity' => 80,
        ]);
        // An event that already took place (does not show on the home page).
        Event::create([
            'title' => 'Welcome Day', 'category_id' => $categories['Social']->id,
            'club_id' => null, 'created_by' => $admin->id,
            'description' => 'Orientation day for new students.',
            'start_date' => $at(-20, 9), 'end_date' => $at(-20, 15),
            'location' => 'Campus Square', 'capacity' => 300,
        ]);

        $student->registeredEvents()->attach($fair->id);
    }
}
