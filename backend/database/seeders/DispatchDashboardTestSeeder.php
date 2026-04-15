<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DispatchDashboardTestSeeder extends Seeder
{
    public function run(): void
    {
        $jobId = 77;
        $driverIds = range(1, 13);
        $now = Carbon::now();

        $rows = [
            [
                'id_driver' => 1,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 105,
                'event' => 'Enter Job Site',
                'miles' => 2.50,
                'eta' => 18,
                'note' => 'Ready for dispatch.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 2,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 8,
                'event' => 'Exit Job Site',
                'miles' => 12.00,
                'eta' => 35,
                'note' => 'Off shift.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 3,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'BREAKDOWN',
                'minutes_ago' => 8,
                'event' => 'Enter Job Site',
                'miles' => 45.00,
                'eta' => 70,
                'note' => 'Broken down yesterday.',
                'predicted_return_minutes' => 120,
            ],
            [
                'id_driver' => 4,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 42,
                'event' => 'Loaded Sand',
                'miles' => 5.25,
                'eta' => 22,
                'note' => 'Fuel stop complete.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 5,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 155,
                'event' => 'Arrived At Well',
                'miles' => 1.75,
                'eta' => 12,
                'note' => 'On location and waiting.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 6,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'OFF DUTY',
                'minutes_ago' => 65,
                'event' => 'Clocked Out',
                'miles' => 0.00,
                'eta' => 0,
                'note' => 'Finished day shift.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 7,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 23,
                'event' => 'En Route To Job',
                'miles' => 16.50,
                'eta' => 44,
                'note' => 'Traffic near county road.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 8,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'DAYS OFF',
                'minutes_ago' => 360,
                'event' => 'Scheduled Off',
                'miles' => null,
                'eta' => null,
                'note' => 'Approved days off.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 9,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 11,
                'event' => 'At Scale House',
                'miles' => 9.80,
                'eta' => 28,
                'note' => 'Scale line moving slowly.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 10,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'BREAKDOWN',
                'minutes_ago' => 31,
                'event' => 'Pulled To Shoulder',
                'miles' => 20.00,
                'eta' => 55,
                'note' => 'Tire issue, roadside service called.',
                'predicted_return_minutes' => 90,
            ],
            [
                'id_driver' => 11,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 74,
                'event' => 'Loaded Water',
                'miles' => 3.60,
                'eta' => 19,
                'note' => 'Ready for next dispatch order.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 12,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'OFF DUTY',
                'minutes_ago' => 180,
                'event' => 'At Yard',
                'miles' => 0.50,
                'eta' => 0,
                'note' => 'Off duty, truck parked.',
                'predicted_return_minutes' => null,
            ],
            [
                'id_driver' => 13,
                'assigned_by_name' => 'Norayr',
                'dispatcher' => 'Miguel',
                'status' => 'ON DUTY',
                'minutes_ago' => 14,
                'event' => 'Waiting On Boxes',
                'miles' => 7.40,
                'eta' => 26,
                'note' => 'Waiting for boxes at pickup.',
                'predicted_return_minutes' => null,
            ],
        ];

        DB::transaction(function () use ($jobId, $driverIds, $rows, $now) {
            DB::table('dispatch_shift_notes')->where('id_join', $jobId)->delete();
            DB::table('dispatch_driver_state_histories')->where('id_join', $jobId)->whereIn('id_driver', $driverIds)->delete();
            DB::table('dispatch_driver_notes')->where('id_join', $jobId)->whereIn('id_driver', $driverIds)->delete();
            DB::table('dispatch_driver_tracking_snapshots')->where('id_join', $jobId)->whereIn('id_driver', $driverIds)->delete();
            DB::table('dispatch_driver_current_states')->where('id_join', $jobId)->whereIn('id_driver', $driverIds)->delete();
            DB::table('dispatch_job_driver_assignments')->where('id_join', $jobId)->whereIn('id_driver', $driverIds)->delete();

            $assignmentRows = [];
            $currentStateRows = [];
            $stateHistoryRows = [];
            $trackingRows = [];
            $noteRows = [];

            foreach ($rows as $index => $row) {
                $startedAt = $now->copy()->subMinutes($row['minutes_ago']);
                $predictedReturnAt = $row['predicted_return_minutes'] !== null
                    ? $now->copy()->addMinutes($row['predicted_return_minutes'])
                    : null;

                $assignmentRows[] = [
                    'id_join' => $jobId,
                    'id_driver' => $row['id_driver'],
                    'assigned_by_user_id' => null,
                    'assigned_by_name' => $row['assigned_by_name'],
                    'assigned_at' => $now->copy()->subDay(),
                    'unassigned_at' => null,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $currentStateRows[] = [
                    'id_driver' => $row['id_driver'],
                    'id_join' => $jobId,
                    'current_status' => $row['status'],
                    'state_started_at' => $startedAt,
                    'predicted_return_at' => $predictedReturnAt,
                    'last_event' => $row['event'],
                    'updated_by_user_id' => null,
                    'updated_by_name' => $row['dispatcher'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $stateHistoryRows[] = [
                    'id_driver' => $row['id_driver'],
                    'id_join' => $jobId,
                    'status' => $row['status'],
                    'started_at' => $startedAt,
                    'ended_at' => null,
                    'predicted_return_at' => $predictedReturnAt,
                    'last_event' => $row['event'],
                    'changed_by_user_id' => null,
                    'changed_by_name' => $row['dispatcher'],
                    'note' => $row['note'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $trackingRows[] = [
                    'id_driver' => $row['id_driver'],
                    'id_join' => $jobId,
                    'last_event' => $row['event'],
                    'miles_to_job' => $row['miles'],
                    'eta_to_deliver_minutes' => $row['eta'],
                    'gps_lat' => 31.9000000 + ($index * 0.0100000),
                    'gps_lng' => -102.3000000 - ($index * 0.0100000),
                    'gps_recorded_at' => $now->copy()->subMinutes(max(1, intdiv($row['minutes_ago'], 2))),
                    'source_table' => 'dispatch_dashboard_test_seeder',
                    'source_id' => (string) $row['id_driver'],
                    'synced_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $noteRows[] = [
                    'id_driver' => $row['id_driver'],
                    'id_join' => $jobId,
                    'note_text' => $row['note'],
                    'created_by_user_id' => null,
                    'created_by_name' => $row['dispatcher'],
                    'updated_by_user_id' => null,
                    'updated_by_name' => $row['dispatcher'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('dispatch_job_driver_assignments')->insert($assignmentRows);
            DB::table('dispatch_driver_current_states')->insert($currentStateRows);
            DB::table('dispatch_driver_state_histories')->insert($stateHistoryRows);
            DB::table('dispatch_driver_tracking_snapshots')->insert($trackingRows);
            DB::table('dispatch_driver_notes')->insert($noteRows);

            DB::table('dispatch_shift_notes')->insert([
                [
                    'id_join' => $jobId,
                    'shift_key' => 'NIGHT',
                    'started_by_user_id' => null,
                    'started_by_name' => 'Miguela',
                    'ended_by_user_id' => null,
                    'ended_by_name' => 'Miguela',
                    'note_text' => 'AAA',
                    'is_active' => 0,
                    'started_at' => $now->copy()->subHours(5),
                    'ended_at' => $now->copy()->subHours(4),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_join' => $jobId,
                    'shift_key' => 'DAY',
                    'started_by_user_id' => null,
                    'started_by_name' => 'Miguel',
                    'ended_by_user_id' => null,
                    'ended_by_name' => null,
                    'note_text' => 'BBB',
                    'is_active' => 0,
                    'started_at' => $now->copy()->subHours(3),
                    'ended_at' => $now->copy()->subHours(2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_join' => $jobId,
                    'shift_key' => 'DAY',
                    'started_by_user_id' => null,
                    'started_by_name' => 'Miguel',
                    'ended_by_user_id' => null,
                    'ended_by_name' => null,
                    'note_text' => 'New Note',
                    'is_active' => 1,
                    'started_at' => $now->copy()->subHour(),
                    'ended_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        });
    }
}
