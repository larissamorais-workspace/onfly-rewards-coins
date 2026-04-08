<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnflyApiService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.onfly.api_url', 'https://api.onfly.com'), '/');
    }

    /**
     * Get auth token via client_credentials grant (cached 23h).
     *
     * NOTE: This token does not carry user context, so hotel/geolocation
     * endpoints return 401. The SearchController falls back to MockDataGenerator.
     * To enable real hotel data, a password-grant client_id with access to
     * /hotel/search and /geolocation/search-destination is required.
     */
    public function getToken(): ?string
    {
        return Cache::remember('onfly_api_token', 60 * 60 * 23, function () {
            $response = Http::asJson()->post("{$this->apiUrl}/oauth/token", [
                'grant_type'    => 'client_credentials',
                'scope'         => '*',
                'client_id'     => config('services.onfly.client_id'),
                'client_secret' => config('services.onfly.client_secret'),
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::warning('Onfly API auth failed', [
                'status' => $response->status(),
            ]);

            return null;
        });
    }

    /**
     * Search hotels via Onfly V1 API.
     * Returns [] if auth token lacks user context — SearchController falls back to mock.
     */
    public function searchHotels(string $cityName, string $checkIn, string $checkOut, int $guests = 1): array
    {
        $token = $this->getToken();
        if (!$token) return [];

        // Step 1: resolve placeId
        $geoResponse = Http::withToken($token)
            ->timeout(10)
            ->get("{$this->apiUrl}/geolocation/search-destination", [
                'input'         => $cityName,
                'international' => 'false',
            ]);

        if (!$geoResponse->successful() || empty($geoResponse->json('data'))) {
            return [];
        }

        $destination = $geoResponse->json('data.0');
        $placeId     = $destination['placeId'] ?? null;

        if (!$placeId) return [];

        // Step 2: search hotels
        $response = Http::withToken($token)
            ->timeout(20)
            ->get("{$this->apiUrl}/hotel/search", [
                'placeId'        => $placeId,
                'suggest'        => 'false',
                'cityName'       => $destination['name'] ?? $cityName,
                'checkIn'        => $checkIn,
                'checkOut'       => $checkOut,
                'guestsQuantity' => min($guests, 3),
            ]);

        if (!$response->successful()) {
            return [];
        }

        $hotels = $response->json('data.results.hotels') ?? [];

        return $this->normalizeHotels($hotels);
    }

    private function normalizeHotels(array $hotels): array
    {
        return array_map(function ($hotel) {
            $priceInReais = ($hotel['bestDailyPrice'] ?? 0) / 100;

            $amenities = [];
            if ($hotel['hasBreakFast'] ?? false) $amenities[] = 'Café da manhã';
            if ($hotel['hasRefundableRoom'] ?? false) $amenities[] = 'Reembolsável';

            $address = $hotel['address'] ?? [];

            return [
                'id'        => (string) ($hotel['id'] ?? uniqid()),
                'name'      => $hotel['name'] ?? 'Hotel',
                'price'     => round($priceInReais, 2),
                'rating'    => (int) ($hotel['stars'] ?? 3),
                'address'   => $address['fullAddress'] ?? ($address['district'] ?? ''),
                'amenities' => $amenities,
                'modal'     => 'hotel',
                'thumb'     => $hotel['thumb'] ?? null,
                'images'    => $hotel['images'] ?? [],
                'has_onhappy_coins'    => false,
                'onhappy_coins_amount' => 0,
                'savings_total'        => 0,
                'company_savings'      => 0,
            ];
        }, $hotels);
    }
}
