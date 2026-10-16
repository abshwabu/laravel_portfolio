<?php

if (! function_exists('hashed_asset')) {
    function hashed_asset(string $path): string
    {
        return \App\Support\Asset::hashed($path);
    }
}
