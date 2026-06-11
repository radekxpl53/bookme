<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;

class AvailabilityService
{
    const KROK_MINUT = 15;

    public function znajdzTerminy(Service $service, Carbon $dzien, ?string $od = null, ?string $do = null, int $limit = 6): array
    {
        $numerDnia = $dzien->dayOfWeekIso;

        $terminy = [];

        foreach ($service->employees as $employee) {
            if (! $employee->is_active) {
                continue;
            }

            $godziny = $employee->workingHours->firstWhere('day_of_week', $numerDnia);
            if (! $godziny) {
                continue;
            }

            $start = $dzien->copy()->setTimeFromTimeString($godziny->start_time);
            $koniec = $dzien->copy()->setTimeFromTimeString($godziny->end_time);

            $zajete = Appointment::where('employee_id', $employee->id)
                ->whereDate('start_at', $dzien->toDateString())
                ->where('status', '!=', 'cancelled')
                ->get(['start_at', 'finish_at']);

            $kursor = $start->copy();

            while ($kursor->copy()->addMinutes($service->duration_minutes)->lessThanOrEqualTo($koniec)) {
                $slotStart = $kursor->copy();
                $slotKoniec = $kursor->copy()->addMinutes($service->duration_minutes);

                if ($slotStart->isPast()) {
                    $kursor->addMinutes(self::KROK_MINUT);
                    continue;
                }

                if ($od && $slotStart->format('H:i') < $od) {
                    $kursor->addMinutes(self::KROK_MINUT);
                    continue;
                }
                if ($do && $slotStart->format('H:i') >= $do) {
                    break;
                }

                $koliduje = false;
                foreach ($zajete as $wizyta) {
                    if ($slotStart->lessThan($wizyta->finish_at) && $slotKoniec->greaterThan($wizyta->start_at)) {
                        $koliduje = true;
                        break;
                    }
                }

                if (! $koliduje) {
                    $terminy[] = [
                        'time' => $slotStart,
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                    ];
                }

                $kursor->addMinutes(self::KROK_MINUT);
            }
        }

        usort($terminy, fn ($a, $b) => $a['time']->timestamp <=> $b['time']->timestamp);

        return array_slice($terminy, 0, $limit);
    }
}
