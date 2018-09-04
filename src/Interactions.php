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
use Illuminate\Support\Str;

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

    public function __construct(AuthManager $auth, Repository $config)
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

        $this->reply = $modelOrId;
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

    protected function mentionPlaceholders($text): string 
    {
        if (is_null($input) || empty($input)) {
            return $input;
        }

        $character = config('interactions.mentions.character');
        $regex = strtr(config('interactions.mentions.regex'), config('interactions.mentions.regex_replacement'));

        preg_match_all($regex, $text, $matches);

        $matches = array_map([$this, 'mapper'], $matches[0]);

        $matches = $this->removeNullKeys($matches);
        $matches = $this->prepareArray($matches);
        
        $output = preg_replace_callback($matches, [$this, 'replace'], $input);

        return $output;
    }

    /**
     * Replace the mention with a markdown link.
     *
     * @param array $match The mention to replace.
     *
     * @return string
     */
    protected function replace(array $match): string
    {
        $character = config('interactions.mentions.character');

        $mention = Str::title(str_replace($character, '', trim($match[0])));
        $route = config('interactions.interactions.route');

        $link = $route . $mention;

        return " [{$character}{$mention}]($link)";
    }

    /**
     * Prepare the array before calling the replace function.
     *
     * We basically order the array in alphabetic order, then we reverse it
     * so it will match the largest name first, else it can remove
     * `@admin2` if it match `@admin` first (based on the default regex).
     *
     * @param array $array The array to prepare
     *
     * @return array
     */
    protected function prepareArray(array $array): array
    {
        sort($array, SORT_STRING);
        $array = array_reverse($array);
        return $array;
    }
    /**
     * Remove all `null` key in the given array.
     *
     * @param array $array The array where the filter should be applied.
     *
     * @return array
     */
    protected function removeNullKeys(array $array): array
    {
        return array_filter($array, function ($key) {
            return ($key !== null);
        });
    }

    /**
     * Handle a mention and return it has a regex. If you want to delete
     * this mention from the out array, just return `null`.
     *
     * @param string $key The mention that has been matched.
     *
     * @return null|string
     */
    protected function mapper(string $key)
    {
        $character = config('interactions.mentions.character');
        $config = config('interactions.mentions');
        
        $mention = str_replace($character, '', trim($key));
        $mentionned = $config['model']::whereRaw("LOWER({$config['column']}) = ?", [Str::lower($mention)])->first();
        
        if ($mentionned == false) {
            return null;
        }

        if ($mentionned->getKey() !== Auth::id()) {
            $this->model->mention($mentionned, $this->getOption('notify'));
        }

        return '/' . preg_quote($key) . '(?!\w)/';
    }
}

