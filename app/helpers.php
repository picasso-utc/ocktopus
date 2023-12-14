<?php

if (!function_exists('enum_pluck')) {
    function enum_pluck($enum, string $method = 'title'): array
    {
        return collect($enum::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->{$method}()])
            ->toArray();
    }
}
