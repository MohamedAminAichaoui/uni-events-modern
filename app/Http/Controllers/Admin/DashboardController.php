<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function events(Request $request)
    {
        $years = $this->availableYears();
        $year = $this->selectedYear($request, $years);
        $registrationCounts = $this->monthlyCounts(DB::table('registrations'), 'created_at', $year);
        $chart = $this->yearlyChart($year, [
            [
                'key' => 'registrations',
                'label' => 'Participant registrations',
                'color' => '#1b2a4a',
                'counts' => $registrationCounts,
            ],
        ]);

        $events = Event::with('club')
            ->withCount('registrations')
            ->orderByDesc('registrations_count')
            ->orderByDesc('start_date')
            ->limit(10)
            ->get();

        return view('admin.analytics.events', [
            'events' => $events,
            'eventCount' => Event::count(),
            'upcomingEventCount' => Event::upcoming()->count(),
            'registrationCount' => Registration::count(),
            'topEvent' => $events->first(),
            'maxRegistrations' => max(1, (int) $events->max('registrations_count')),
            'year' => $year,
            'years' => $years,
            'chart' => $chart,
        ]);
    }

    public function clubs(Request $request)
    {
        $years = $this->availableYears();
        $year = $this->selectedYear($request, $years);
        $membershipCounts = $this->monthlyCounts(DB::table('club_members'), 'created_at', $year);
        $clubEventRegistrationCounts = $this->monthlyCounts(
            DB::table('registrations')
                ->join('events', 'events.id', '=', 'registrations.event_id')
                ->whereNotNull('events.club_id'),
            'registrations.created_at',
            $year
        );
        $chart = $this->yearlyChart($year, [
            [
                'key' => 'memberships',
                'label' => 'New club memberships',
                'color' => '#c9a227',
                'counts' => $membershipCounts,
            ],
            [
                'key' => 'club_registrations',
                'label' => 'Registrations at club events',
                'color' => '#1b2a4a',
                'counts' => $clubEventRegistrationCounts,
            ],
        ]);

        $clubs = Club::withCount(['members', 'events'])
            ->with(['events' => fn ($query) => $query->withCount('registrations')])
            ->orderByDesc('members_count')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('admin.analytics.clubs', [
            'clubs' => $clubs,
            'clubCount' => Club::count(),
            'clubMemberCount' => DB::table('club_members')->count(),
            'clubEventRegistrationCount' => Registration::whereHas('event', fn ($query) => $query->whereNotNull('club_id'))->count(),
            'topClub' => $clubs->first(),
            'maxClubMembers' => max(1, (int) $clubs->max('members_count')),
            'year' => $year,
            'years' => $years,
            'chart' => $chart,
        ]);
    }

    public function index()
    {
        $now = now();
        $firstMonth = $now->copy()->startOfMonth()->subMonths(5);
        $period = [$firstMonth, $now];

        $registrationsByMonth = Registration::query()
            ->whereBetween('created_at', $period)
            ->get(['created_at'])
            ->countBy(fn (Registration $registration) => $registration->created_at->format('Y-m'));

        $membershipsByMonth = DB::table('club_members')
            ->whereBetween('created_at', $period)
            ->get(['created_at'])
            ->countBy(fn ($membership) => Carbon::parse($membership->created_at)->format('Y-m'));

        $monthlyActivity = collect(range(0, 5))
            ->map(function ($offset) use ($firstMonth, $registrationsByMonth, $membershipsByMonth) {
                $month = $firstMonth->copy()->addMonths($offset);
                $key = $month->format('Y-m');

                return [
                    'label' => $month->format('M Y'),
                    'registrations' => (int) $registrationsByMonth->get($key, 0),
                    'memberships' => (int) $membershipsByMonth->get($key, 0),
                ];
            });

        $peakMonthlyActivity = max(1, (int) $monthlyActivity->max(
            fn ($month) => max($month['registrations'], $month['memberships'])
        ));
        $monthlyActivityScale = max(4, (int) (ceil($peakMonthlyActivity / 4) * 4));

        $monthlyActivity = $monthlyActivity->map(function ($month, $index) use ($monthlyActivityScale) {
            $month['x'] = 68 + $index * 116;
            $month['registration_y'] = round(218 - ($month['registrations'] / $monthlyActivityScale * 180), 1);
            $month['membership_y'] = round(218 - ($month['memberships'] / $monthlyActivityScale * 180), 1);

            return $month;
        });

        $events = Event::with('club')
            ->withCount('registrations')
            ->orderByDesc('start_date')
            ->limit(8)
            ->get();

        $clubs = Club::withCount(['members', 'events'])
            ->with(['events' => fn ($query) => $query->withCount('registrations')])
            ->orderByDesc('members_count')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'eventCount' => Event::count(),
            'upcomingEventCount' => Event::upcoming()->count(),
            'registrationCount' => Registration::count(),
            'clubCount' => Club::count(),
            'clubMemberCount' => DB::table('club_members')->count(),
            'events' => $events,
            'clubs' => $clubs,
            'maxClubMembers' => max(1, (int) $clubs->max('members_count')),
            'monthlyActivity' => $monthlyActivity,
            'monthlyActivityScale' => $monthlyActivityScale,
            'registrationCurve' => $monthlyActivity
                ->map(fn ($month) => $month['x'].','.$month['registration_y'])
                ->implode(' '),
            'membershipCurve' => $monthlyActivity
                ->map(fn ($month) => $month['x'].','.$month['membership_y'])
                ->implode(' '),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function availableYears(): array
    {
        $currentYear = (int) now()->format('Y');
        $activityDates = [
            DB::table('registrations')->min('created_at'),
            DB::table('club_members')->min('created_at'),
        ];
        $firstYear = collect($activityDates)
            ->filter()
            ->map(fn ($date) => (int) Carbon::parse($date)->format('Y'))
            ->min();

        return range(min($firstYear ?: $currentYear, $currentYear), $currentYear);
    }

    /**
     * @param  array<int, int>  $years
     */
    private function selectedYear(Request $request, array $years): int
    {
        $data = $request->validate([
            'year' => ['sometimes', 'required', 'integer', 'min:'.min($years), 'max:'.max($years)],
        ]);

        return (int) ($data['year'] ?? now()->format('Y'));
    }

    private function monthlyCounts(Builder $query, string $createdAtColumn, int $year): Collection
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = $start->copy()->endOfYear();

        return $query
            ->whereBetween($createdAtColumn, [$start, $end])
            ->get([$createdAtColumn])
            ->countBy(fn ($record) => Carbon::parse($record->created_at)->format('Y-m'));
    }

    /**
     * @param  array<int, array{key: string, label: string, color: string, counts: Collection}>  $series
     * @return array{months: Collection, series: array<int, array{key: string, label: string, color: string, points: string}>, scale: int, hasData: bool}
     */
    private function yearlyChart(int $year, array $series): array
    {
        $months = collect(range(0, 11))->map(function ($index) use ($year, $series) {
            $month = Carbon::create($year, 1, 1)->addMonths($index);
            $data = [
                'label' => $month->format('M'),
                'x' => 54 + $index * 55,
            ];

            foreach ($series as $line) {
                $data[$line['key']] = (int) $line['counts']->get($month->format('Y-m'), 0);
            }

            return $data;
        });

        $peak = 1;
        $hasData = false;
        foreach ($months as $month) {
            foreach ($series as $line) {
                $peak = max($peak, $month[$line['key']]);
                $hasData = $hasData || $month[$line['key']] > 0;
            }
        }

        $scale = max(4, (int) (ceil($peak / 4) * 4));
        $months = $months->map(function ($month) use ($series, $scale) {
            foreach ($series as $line) {
                $month[$line['key'].'_y'] = round(210 - ($month[$line['key']] / $scale * 170), 1);
            }

            return $month;
        });

        $lines = collect($series)->map(function ($line) use ($months) {
            $line['points'] = $months
                ->map(fn ($month) => $month['x'].','.$month[$line['key'].'_y'])
                ->implode(' ');
            unset($line['counts']);

            return $line;
        })->all();

        return [
            'months' => $months,
            'series' => $lines,
            'scale' => $scale,
            'hasData' => $hasData,
        ];
    }
}
