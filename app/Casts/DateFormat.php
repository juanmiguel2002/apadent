<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DateFormat implements CastsAttributes
{

    private string $dateFormat = 'd/m/Y';
    private string $setFormat = 'Y-m-d';
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return strlen($value)
        ? Carbon::parse($value)->format($this->dateFormat)
        : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return strlen($value)
            ? Carbon::parse($value)->format($this->setFormat)
            : null;
    }
}
