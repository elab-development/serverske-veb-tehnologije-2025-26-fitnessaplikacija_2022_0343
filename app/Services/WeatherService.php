<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// Klijent za javni Open-Meteo API (bez API ključa), koristi se u Insights za trenutnu temperaturu.
class WeatherService
{
    public function currentTemperature(float $lat = 44.7872, float $lon = 20.4573): ?float
    {
        // Keširamo 10 minuta po lokaciji da ne bombardujemo eksterni servis pri svakom otvaranju Insights-a.
        $key = "weather:current:{$lat}:{$lon}";

        return Cache::remember($key, 600, function () use ($lat, $lon) {
            $res = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lon,
                'current_weather' => true,
            ]);

            if (! $res->ok()) {
                return null;
            }

            return $res->json('current_weather.temperature');
        });
    }
}
