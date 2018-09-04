<?php

use Russofinn\Interactions\Interactions;

if (! function_exists('interactions')) {
    function interactions(): Interactions
    {
        return app(Interactions::class);
    }
}