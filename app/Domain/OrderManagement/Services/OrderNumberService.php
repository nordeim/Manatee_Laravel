<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderNumberService
{
    public function generate(): string
    {
        // Example: ORD-YYYYMMDD-XXXXXX (random alphanumeric)
        $prefix = 'ORD-';
        $datePart = Carbon::now()->format('Ymd');
        $randomPart = strtoupper(Str::random(6));

        // Could add a sequence number or check for uniqueness if high volume
        return $prefix . $datePart . '-' . $randomPart;
    }
}
