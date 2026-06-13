<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;

class AvailabilityService
{
    const STEP_MINUTES = 15;

    public function findSlots(Service $service, Carbon $day, ?string $from = null, ?string $to = null, int $limit = 6, ?int $employeeId = null): array
    {
        $isoDay = $day->dayOfWeekIso;

        $slots = [];

        foreach ($service->employees as $employee) {
            if (! $employee->is_active) {
                continue;
            }

            if ($employeeId && $employee->id !== $employeeId) {
                continue;
            }

            $hours = $employee->workingHours->firstWhere('day_of_week', $isoDay);
            if (! $hours) {
                continue;
            }

            $onLeave = $employee->leaves()
                ->where('start_date', '<=', $day->toDateString())
                ->where('end_date', '>=', $day->toDateString())
                ->exists();

            if ($onLeave) {
                continue;
            }

            $start = $day->copy()->setTimeFromTimeString($hours->start_time);
            $end = $day->copy()->setTimeFromTimeString($hours->end_time);

            $booked = Appointment::where('employee_id', $employee->id)
                ->whereDate('start_at', $day->toDateString())
                ->where('status', '!=', 'cancelled')
                ->get(['start_at', 'finish_at']);

            $cursor = $start->copy();

            while ($cursor->copy()->addMinutes($service->duration_minutes)->lessThanOrEqualTo($end)) {
                $slotStart = $cursor->copy();
                $slotEnd = $cursor->copy()->addMinutes($service->duration_minutes);

                if ($slotStart->isPast()) {
                    $cursor->addMinutes(self::STEP_MINUTES);
                    continue;
                }

                if ($from && $slotStart->format('H:i') < $from) {
                    $cursor->addMinutes(self::STEP_MINUTES);
                    continue;
                }
                if ($to && $slotStart->format('H:i') >= $to) {
                    break;
                }

                $collides = false;
                foreach ($booked as $appt) {
                    if ($slotStart->lessThan($appt->finish_at) && $slotEnd->greaterThan($appt->start_at)) {
                        $collides = true;
                        break;
                    }
                }

                if (! $collides) {
                    $slots[] = [
                        'time' => $slotStart,
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                    ];
                }

                $cursor->addMinutes(self::STEP_MINUTES);
            }
        }

        usort($slots, fn ($a, $b) => $a['time']->timestamp <=> $b['time']->timestamp);

        return array_slice($slots, 0, $limit);
    }

    public function findSlotsInRange(Service $service, Carbon $from, Carbon $toDay, ?string $fromTime = null, ?string $toTime = null, int $limit = 6, ?int $employeeId = null): array
    {
        $slots = [];

        $day = $from->copy()->startOfDay();
        $end = $toDay->copy()->startOfDay();
        if ($end->lessThan($day)) {
            $end = $day->copy();
        }
        if ($end->diffInDays($day) > 31) {
            $end = $day->copy()->addDays(31);
        }

        while ($day->lessThanOrEqualTo($end) && count($slots) < $limit) {
            $daySlots = $this->findSlots($service, $day, $fromTime, $toTime, $limit - count($slots), $employeeId);
            $slots = array_merge($slots, $daySlots);
            $day->addDay();
        }

        return $slots;
    }

    public function isAvailable(Service $service, Employee $employee, Carbon $start): bool
    {
        if (! $employee->is_active || $start->isPast()) {
            return false;
        }

        $finish = $start->copy()->addMinutes($service->duration_minutes);

        $hours = $employee->workingHours->firstWhere('day_of_week', $start->dayOfWeekIso);
        if (! $hours) {
            return false;
        }

        $onLeave = $employee->leaves()
            ->where('start_date', '<=', $start->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->exists();

        if ($onLeave) {
            return false;
        }

        $workStart = $start->copy()->setTimeFromTimeString($hours->start_time);
        $workEnd = $start->copy()->setTimeFromTimeString($hours->end_time);
        if ($start->lessThan($workStart) || $finish->greaterThan($workEnd)) {
            return false;
        }

        $collides = Appointment::where('employee_id', $employee->id)
            ->where('status', '!=', 'cancelled')
            ->where('start_at', '<', $finish)
            ->where('finish_at', '>', $start)
            ->exists();

        return ! $collides;
    }
}
