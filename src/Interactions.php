<?php

namespace Russofinn\Interactions;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use Russofinn\Interactions\Models\Comment;
use Russofinn\Interactions\Models\Like;
use Russofinn\Interactions\Models\View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Config\Repository;
use Russofinn\Interactions\Exceptions\CouldNotInteraction;

class Interactions
{
	use Macroable;

	/** @var \Illuminate\Auth\AuthManager */
    protected $auth;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $performedOn;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $causedBy;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $reply;

    /** @var \Illuminate\Support\Collection */
    protected $properties;

    /** @var string */
    protected $authDriver;

    public function __construct(AuthManager $auth, Repository $config, ActivityLogStatus $logStatus)
    {
        $this->auth = $auth;
        $this->properties = collect();
        $this->authDriver = $config['interactions']['default_auth_driver'] ?? $auth->getDefaultDriver();

        if (starts_with(app()->version(), '5.1')) {
            $this->causedBy = $auth->driver($this->authDriver)->user();
        } else {
            $this->causedBy = $auth->guard($this->authDriver)->user();
        }
    }

    public function performedOn(Model $model)
    {
        $this->performedOn = $model;

        return $this;
    }

    public function on(Model $model)
    {
        return $this->performedOn($model);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int|string $modelOrId
     *
     * @return $this
     */
    public function causedBy($modelOrId)
    {
        if ($modelOrId === null) {
            return $this;
        }
        $model = $this->normalizeCauser($modelOrId);
        $this->causedBy = $model;
        return $this;
    }

    public function by($modelOrId)
    {
        return $this->causedBy($modelOrId);
    }

    public function reply($modelOrId) 
    {
    	if ($modelOrId === null) {
            return $this;
        }
        $model = $this->normalizeCauser($modelOrId);
        $this->reply = $model;
        return $this;
    }

    /**
     * @param string $text
     *
     * @return null|mixed
     */
    public function comment(string $text)
    {

        $comment = InteractionsServiceProvider::getCommentModelInstance();
        if ($this->performedOn) {
            $comment->subject()->associate($this->performedOn);
        }

        if ($this->causedBy) {
            $comment->causer()->associate($this->causedBy);
        }
        $comment->reply_id = $this->reply;
        $comment->text = $text;
        $comment->save();

        return $comment;
    }

    /**
     *
     * @return null|mixed
     */
    public function like()
    {

        $like = InteractionsServiceProvider::getLikeModelInstance();
        if ($this->performedOn) {
            $like->subject()->associate($this->performedOn);
        }

        if ($this->causedBy) {
            $like->causer()->associate($this->causedBy);
        }
        $like->save();

        return $like;
    }

     /**
     *
     * @return null|mixed
     */
    public function view()
    {

        $view = InteractionsServiceProvider::getViewModelInstance();
        if ($this->performedOn) {
            $view->subject()->associate($this->performedOn);
        }

        if ($this->causedBy) {
            $view->causer()->associate($this->causedBy);
        }
        $view->save();

        return $view;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int|string $modelOrId
     *
     * @throws \Russofinn\Interactions\Exceptions\CouldNotInteraction
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function normalizeCauser($modelOrId): Model
    {
        if ($modelOrId instanceof Model) {
            return $modelOrId;
        }
        if (starts_with(app()->version(), '5.1')) {
            $model = $this->auth->driver($this->authDriver)->getProvider()->retrieveById($modelOrId);
        } else {
            $model = $this->auth->guard($this->authDriver)->getProvider()->retrieveById($modelOrId);
        }
        if ($model) {
            return $model;
        }
        throw CouldNotInteraction::couldNotDetermineUser($modelOrId);
    }
}

