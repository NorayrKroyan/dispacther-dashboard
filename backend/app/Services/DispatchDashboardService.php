<?php

namespace App\Services;

use App\Models\DispatchShiftNote;
use App\Models\Legacy\JobJoin;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DispatchDashboardService
{
    public function jobs(): Collection
    {
        return JobJoin::query()
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->orderByRaw('CASE WHEN job_name IS NULL OR job_name = "" THEN 1 ELSE 0 END')
            ->orderBy('job_name')
            ->get([
                'id_join',
                'job_name',
                'job_notes',
                'miles',
            ])
            ->map(function (JobJoin $job) {
                return [
                    'id_join' => (int) $job->id_join,
                    'job_name' => $job->job_name ?: ('Job #' . $job->id_join),
                    'job_notes' => $job->job_notes,
                    'miles' => $job->miles,
                ];
            });
    }

    public function dashboard(int $idJoin, ?string $dispatcherName = null): array
    {
        $job = JobJoin::query()
            ->where('id_join', $idJoin)
            ->firstOrFail([
                'id_join',
                'job_name',
                'job_notes',
                'miles',
                'id_pull_point',
                'id_pad_location',
            ]);

        $rows = DB::table('dispatch_job_driver_assignments as a')
            ->join('driver as d', function ($join) {
                $join->on('d.id_driver', '=', 'a.id_driver')
                    ->where('d.is_deleted', '=', 0);
            })
            ->leftJoin('contact as c', 'c.id_contact', '=', 'd.id_contact')
            ->leftJoin('vehicle as v', 'v.id_vehicle', '=', 'd.id_vehicle')
            ->leftJoin('dispatch_driver_current_states as s', function ($join) {
                $join->on('s.id_driver', '=', 'a.id_driver')
                    ->on('s.id_join', '=', 'a.id_join');
            })
            ->leftJoin('dispatch_driver_tracking_snapshots as ts', function ($join) {
                $join->on('ts.id_driver', '=', 'a.id_driver')
                    ->on('ts.id_join', '=', 'a.id_join');
            })
            ->leftJoin('dispatch_driver_notes as n', function ($join) {
                $join->on('n.id_driver', '=', 'a.id_driver')
                    ->on('n.id_join', '=', 'a.id_join');
            })
            ->where('a.id_join', $idJoin)
            ->where('a.is_active', 1)
            ->orderByRaw("COALESCE(v.vehicle_number, '')")
            ->orderByRaw("COALESCE(c.first_name, '')")
            ->orderByRaw("COALESCE(c.last_name, '')")
            ->get([
                'a.id',
                'a.id_join',
                'a.id_driver',
                'd.id_contact',
                'd.id_vehicle',
                'd.id_device',
                'd.idprojects',
                'c.first_name',
                'c.last_name',
                'c.phone_number',
                'c.email',
                'v.vehicle_number',
                'v.vehicle_name',
                's.current_status',
                's.state_started_at',
                's.predicted_return_at',
                's.last_event as state_last_event',
                's.updated_by_name',
                'ts.last_event as tracking_last_event',
                'ts.miles_to_job',
                'ts.eta_to_deliver_minutes',
                'ts.gps_recorded_at',
                'n.note_text',
            ])
            ->map(function ($row) {
                $status = $row->current_status ?: 'OFF DUTY';
                $stateStartedAt = $row->state_started_at ? Carbon::parse($row->state_started_at) : null;
                $duration = $stateStartedAt ? $this->formatDuration($stateStartedAt) : '0m';

                return [
                    'assignment_id' => (int) $row->id,
                    'id_join' => (int) $row->id_join,
                    'id_driver' => (int) $row->id_driver,
                    'id_contact' => $row->id_contact ? (int) $row->id_contact : null,
                    'id_vehicle' => $row->id_vehicle ? (int) $row->id_vehicle : null,
                    'id_device' => $row->id_device ? (int) $row->id_device : null,
                    'driver_name' => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                    'truck_number' => $row->vehicle_number ?: '',
                    'vehicle_name' => $row->vehicle_name,
                    'phone_number' => $row->phone_number,
                    'email' => $row->email,
                    'current_status' => $status,
                    'state_started_at' => $row->state_started_at,
                    'duty_duration_label' => $duration,
                    'predicted_return_at' => $row->predicted_return_at,
                    'last_event' => $row->state_last_event !== null && $row->state_last_event !== ''
                        ? $row->state_last_event
                        : ($row->tracking_last_event ?? ''),
                    'miles_to_job' => $row->miles_to_job,
                    'eta_to_deliver_minutes' => $row->eta_to_deliver_minutes,
                    'gps_recorded_at' => $row->gps_recorded_at,
                    'driver_note' => $row->note_text ?? '',
                    'status_color' => $this->statusColor($status),
                    'duty_color' => $this->dutyColor($status, $stateStartedAt),
                ];
            })
            ->values();

        $shiftNotes = DispatchShiftNote::query()
            ->where('id_join', $idJoin)
            ->orderByDesc('is_active')
            ->orderByDesc('started_at')
            ->get()
            ->map(function (DispatchShiftNote $note) use ($dispatcherName) {
                return [
                    'id' => (int) $note->id,
                    'shift_key' => $note->shift_key,
                    'started_by_name' => $note->started_by_name,
                    'note_text' => $note->note_text ?? '',
                    'is_active' => (bool) $note->is_active,
                    'started_at' => optional($note->started_at)->toDateTimeString(),
                    'ended_at' => optional($note->ended_at)->toDateTimeString(),
                    'is_editable' => (bool) $note->is_active
                        && $dispatcherName
                        && mb_strtolower((string) $dispatcherName) === mb_strtolower((string) $note->started_by_name),
                ];
            })
            ->values();

        return [
            'job' => [
                'id_join' => (int) $job->id_join,
                'job_name' => $job->job_name ?: ('Job #' . $job->id_join),
                'job_notes' => $job->job_notes,
                'miles' => $job->miles,
                'id_pull_point' => $job->id_pull_point,
                'id_pad_location' => $job->id_pad_location,
            ],
            'rows' => $rows,
            'shift_notes' => $shiftNotes,
        ];
    }

    private function formatDuration(Carbon $startedAt): string
    {
        $now = now();

        if ($startedAt->greaterThan($now)) {
            return '0m';
        }

        $totalMinutes = $startedAt->diffInMinutes($now);
        $days = intdiv($totalMinutes, 1440);
        $hours = intdiv($totalMinutes % 1440, 60);
        $minutes = $totalMinutes % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . 'd';
        }

        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }

        if ($minutes > 0 || empty($parts)) {
            $parts[] = $minutes . 'm';
        }

        return implode(' ', $parts);
    }

    private function statusColor(string $status): string
    {
        return match ($status) {
            'ON DUTY' => 'status-on-duty',
            'OFF DUTY' => 'status-off-duty',
            'BREAKDOWN' => 'status-breakdown',
            'DAYS OFF' => 'status-days-off',
            default => 'status-off-duty',
        };
    }

    private function dutyColor(string $status, ?Carbon $startedAt): string
    {
        if ($status === 'BREAKDOWN') {
            return 'duty-down';
        }

        if ($status === 'DAYS OFF') {
            return 'duty-days-off';
        }

        if ($status === 'OFF DUTY') {
            return 'duty-muted-green';
        }

        if (!$startedAt) {
            return 'duty-normal';
        }

        $hours = $startedAt->diffInMinutes(now()) / 60;

        if ($hours >= 13) {
            return 'duty-danger';
        }

        if ($hours >= 11) {
            return 'duty-warn';
        }

        return 'duty-normal';
    }
}
