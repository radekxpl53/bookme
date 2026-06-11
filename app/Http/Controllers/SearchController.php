<?php

namespace App\Http\Controllers;

use App\Models\BusinessReview;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, AvailabilityService $availability)
    {
        $dzien = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $query = Service::query()
            ->select('services.*', 'br.avg_rating', 'br.reviews_count')
            ->join('businesses', 'businesses.id', '=', 'services.business_id')
            ->leftJoinSub(
                BusinessReview::query()
                    ->select('business_id')
                    ->selectRaw('AVG(rating) as avg_rating')
                    ->selectRaw('COUNT(*) as reviews_count')
                    ->groupBy('business_id'),
                'br',
                'br.business_id',
                '=',
                'businesses.id'
            )
            ->with(['business', 'employees.workingHours']);

        if ($request->filled('usluga')) {
            $query->where('services.name', 'ILIKE', '%'.$request->input('usluga').'%');
        }

        if ($request->filled('lokalizacja')) {
            $query->where('businesses.address', 'ILIKE', '%'.$request->input('lokalizacja').'%');
        }

        if ($request->filled('kategoria')) {
            $query->where('businesses.category', $request->input('kategoria'));
        }

        if ($request->filled('cena_min')) {
            $query->where('services.price', '>=', $request->input('cena_min'));
        }
        if ($request->filled('cena_max')) {
            $query->where('services.price', '<=', $request->input('cena_max'));
        }

        if ($request->filled('ocena_min')) {
            $query->where('br.avg_rating', '>=', $request->input('ocena_min'));
        }

        if ($request->filled(['sw_lat', 'sw_lon', 'ne_lat', 'ne_lon'])) {
            $query->whereBetween('businesses.lat', [$request->input('sw_lat'), $request->input('ne_lat')])
                ->whereBetween('businesses.lon', [$request->input('sw_lon'), $request->input('ne_lon')]);
        }

        $sort = $request->input('sort', 'trafnosc');
        switch ($sort) {
            case 'cena_rosnaco':
                $query->orderBy('services.price');
                break;
            case 'cena_malejaco':
                $query->orderByDesc('services.price');
                break;
            case 'ocena':
                $query->orderByDesc('br.avg_rating');
                break;
            case 'popularnosc':
                $m = 3;
                $c = (float) (BusinessReview::avg('rating') ?? 0);
                $query->orderByRaw(
                    '((COALESCE(reviews_count,0) / (COALESCE(reviews_count,0) + ?)) * COALESCE(avg_rating,0)'
                    .' + (? / (COALESCE(reviews_count,0) + ?)) * ?) DESC',
                    [$m, $m, $m, $c]
                );
                break;
            default:
                $query->orderBy('services.name')->orderBy('services.id');
                break;
        }

        $services = $query->paginate(8)->withQueryString();

        $godzinaOd = $request->input('godzina_od');
        $godzinaDo = $request->input('godzina_do');
        foreach ($services as $service) {
            $service->terminy = $availability->znajdzTerminy($service, $dzien, $godzinaOd, $godzinaDo);
        }

        $pins = $services->getCollection()
            ->map(fn ($s) => $s->business)
            ->unique('id')
            ->filter(fn ($b) => $b->lat && $b->lon)
            ->map(fn ($b) => [
                'name' => $b->name,
                'lat' => (float) $b->lat,
                'lon' => (float) $b->lon,
                'url' => route('lokal.show', $b),
            ])
            ->values();

        $kategorie = ['Fryzjer', 'Barber', 'Kosmetyczka', 'Masaż', 'Paznokcie', 'Brwi i rzęsy'];

        return view('search.index', [
            'services' => $services,
            'pins' => $pins,
            'kategorie' => $kategorie,
            'dzien' => $dzien,
        ]);
    }
}
