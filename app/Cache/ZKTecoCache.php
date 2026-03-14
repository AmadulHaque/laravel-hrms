<?php

namespace App\Cache;

use App\Models\ZKTeco\Device;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ZKTecoCache
{
    public const CACHE_TTL = 30 * 60; // 30 minutes
    public const CACHE_DEVICE_TTL = 30; // 40 second

    public const CACHE_KEY = 'zekto_devices';
    public const CACHE_DEVICE_KEY = 'zekto_device_serial_number_';

    public static function getSerialNumbers()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Device::pluck('id', 'serial_number')->toArray() ?? [];
        });
    }

    public static function findBySerialNumber(string $key): bool
    {
        return self::getSerialNumbers()[$key] ?? false;
    }


    public static function deviceHeartbeatStatus(string $serialNumber): mixed
    {
        return Cache::remember(self::CACHE_DEVICE_KEY . $serialNumber, self::CACHE_DEVICE_TTL, function (){
            return now();
        });
    }

    public static function isOnline(string $serialNumber): mixed
    {
        return Cache::get(self::CACHE_DEVICE_KEY . $serialNumber);
    }


    public static function invalidate(): void
    {
        $driver = config('cache.default');
        match ($driver) {
            'redis' => Redis::del(self::CACHE_KEY),
            'database', 'file' => Cache::forget(self::CACHE_KEY),
            default => Cache::forget(self::CACHE_KEY),
        };

        match ($driver) {
            'redis' => Redis::del('settings'),
            'database', 'file' => Cache::forget('settings'),
            default => Cache::forget('settings'),
        };

        Cache::forget(self::CACHE_KEY);
        Cache::forget('settings');
    }
}