<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptos = [
            'BTC',
            'ETH',
            'SOL',
            'ADA',
            'XRP',
            'BNB',
            'DOGE',
            'DOT',
        ];

        $stocks = [
            'AAPL',
            'GOOGL',
            'MSFT',
            'TSLA',
            'AMZN',
            'NVDA',
            'META',
            'NFLX',
        ];

        foreach ($cryptos as $symbol) {
            Asset::firstOrCreate([
                'symbol' => $symbol,
            ], [
                'type' => 'crypto',
            ]);
        }

        foreach ($stocks as $symbol) {
            Asset::firstOrCreate([
                'symbol' => $symbol,
            ], [
                'type' => 'stock',
            ]);
        }
    }
}
