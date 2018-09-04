<?php
namespace Spatie\Activitylog\Exceptions;

use Exception;
use Spatie\Activitylog\Models\Activity;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className,string $classBase)
    {
        return new static("The given model class `$className` does not extend `$classBase`");
    }
}