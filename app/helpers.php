<?php

if (!function_exists('enum_pluck')) {
    function enum_pluck($enum, string $method = 'title'): array
    {
        return collect($enum::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->{$method}()])
            ->toArray();
    }
}

if (!function_exists('mailToName')) {
    function mailToName(?string $mail): string
    {
        if ($mail == null) {
            return '';
        }
        $name = explode('@', $mail)[0];
        $name = str_replace('.', ' ', $name);
        return ucwords($name);
    }
}
