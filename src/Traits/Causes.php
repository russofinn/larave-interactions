<?php

namespace Russofinn\Interactions\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Russofinn\Interactions\InteractionsServiceProvider;

trait Causes
{
    public function comments(): MorphMany
    {
        return $this->morphMany(InteractionsServiceProvider::determineCommentModel(), 'causer');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(InteractionsServiceProvider::determineLikeModel(), 'causer');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(InteractionsServiceProvider::determineViewModel(), 'causer');
    }
}
