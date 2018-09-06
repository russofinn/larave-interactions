<?php

namespace Russofinn\Interactions\Exceptions;

use Exception;

class CouldNotInteraction extends Exception
{
    public static function couldNotDetermineUser($id)
    {
        return new static("Could not determine a user with identifier `{$id}`.");
    }
}
