<?php

namespace App\Services;

use Illuminate\Support\Str;

class MockDataGenerator
{
    private const HOTELS = [
        ['name' => 'Ibis Budget',              'rating' => 3],
        ['name' => 'Ibis Styles',              'rating' => 3],
        ['name' => 'Go Inn',                   'rating' => 3],
        ['name' => 'Comfort Suítes',           'rating' => 3],
        ['name' => 'Quality Hotel',            'rating' => 3],
        ['name' => 'Tulip Inn',                'rating' => 3],
        ['name' => 'Slaviero Slim',            'rating' => 4],
        ['name' => 'Slaviero Essential',       'rating' => 4],
        ['name' => 'Bristol Merit',            'rating' => 4],
        ['name' => 'Mercure Apartments',       'rating' => 4],
        ['name' => 'Intercity',                'rating' => 4],
        ['name' => 'Adagio',                   'rating' => 4],
        ['name' => 'Holiday Inn',              'rating' => 4],
        ['name' => 'Ramada',                   'rating' => 4],
        ['name' => 'Deville',                  'rating' => 4],
        ['name' => 'Wyndham Garden',           'rating' => 4],
        ['name' => 'Novotel',                  'rating' => 4],
        ['name' => 'Golden Tulip',             'rating' => 5],
        ['name' => 'Transamerica Executive',   'rating' => 5],
        ['name' => 'Radisson',                 'rating' => 5],
        ['name' => 'Blue Tree Premium',        'rating' => 5],
        ['name' => 'Hilton Garden Inn',        'rating' => 5],
        ['name' => 'Matsubara',                'rating' => 5],
    ];

    private const ADDRESSES = [
        'Centro',
        'Região Central',
        'Zona Hoteleira',
        'Próximo ao Aeroporto',
        'Área Comercial',
        'Região Nobre',
    ];

    private const AMENITIES = [
        3 => ['Wi-Fi', 'Estacionamento', 'Ar-condicionado'],
        4 => ['Wi-Fi', 'Café da manhã', 'Academia', 'Restaurante', 'Estacionamento'],
        5 => ['Wi-Fi', 'Café da manhã', 'Piscina', 'Spa', 'Academia', 'Concierge', 'Room Service'],
    ];

    private const PRICE_RANGES = [
        3 => [150, 350],
        4 => [280, 550],
        5 => [450, 800],
    ];

    public function generate(string $modal, string $city, ?float $policyMax): array
    {
        $pool = self::HOTELS;
        shuffle($pool);

        $count   = rand(6, 8);
        $selected = array_slice($pool, 0, $count);

        $results = array_map(function ($hotel) {
            $rating = $hotel['rating'];
            [$min, $max] = self::PRICE_RANGES[$rating];
            $price = round(rand($min * 100, $max * 100) / 100, 2);

            $address   = self::ADDRESSES[array_rand(self::ADDRESSES)];
            $amenities = self::AMENITIES[$rating];

            return [
                'id'        => Str::uuid()->toString(),
                'name'      => $hotel['name'],
                'price'     => $price,
                'rating'    => $rating,
                'address'   => $address,
                'amenities' => $amenities,
                'modal'     => 'hotel',
                'has_onhappy_coins'    => false,
                'onhappy_coins_amount' => 0,
                'savings_total'        => 0,
                'company_savings'      => 0,
            ];
        }, $selected);

        shuffle($results);

        return $results;
    }
}
