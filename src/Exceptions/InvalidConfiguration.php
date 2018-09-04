<?php

namespace Russofinn\Interactions\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className,string $classBase)
    {
        return new static("The given model class `$className` does not extend `$classBase`");
    }
}