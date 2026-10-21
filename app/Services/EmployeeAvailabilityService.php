<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessAgendaSetting;
use App\Models\EmployeeAvailabilitySlot;
use App\Models\User;
use Carbon\Carbon;

class EmployeeAvailabilityService
{
    public function ensureSlotsExist(
        Business $business,
        User $employee
    ): void {
        $settings = BusinessAgendaSetting::firstOrCreate(
            ['business_id' => $business->id],
            [
                'slot_interval_minutes' => 60,
                'opening_time' => '08:00:00',
                'closing_time' => '18:00:00',
            ]
        );

        $interval = (int) $settings->slot_interval_minutes;

        if ($interval < 1) {
            throw new \InvalidArgumentException(
                'El intervalo de slots debe ser mayor que cero.'
            );
        }

        $opening = Carbon::createFromFormat(
            'H:i:s',
            $settings->opening_time
        );

        $closing = Carbon::createFromFormat(
            'H:i:s',
            $settings->closing_time
        );

        for ($day = 0; $day <= 6; $day++) {
            $cursor = $opening->copy();

            while (
                $cursor->copy()->addMinutes($interval)->lte($closing)
            ) {
                EmployeeAvailabilitySlot::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'day_of_week' => $day,
                        'start_time' => $cursor->format('H:i:s'),
                    ],
                    [
                        'business_id' => $business->id,
                        'is_active' => true,
                    ]
                );

                $cursor->addMinutes($interval);
            }
        }
    }
}