<?php

namespace Russofinn\Interactions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Russofinn\Interactions\Models\Comment;
use Russofinn\Interactions\Models\Like;
use Russofinn\Interactions\Models\View;
use Russofinn\Interactions\Exceptions\InvalidConfiguration;

class InteractionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/interactions.php' => config_path('interactions.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/interactions.php', 'interactions');

        if (!class_exists('CreateCommentsTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_comments_table.php.stub' => database_path("/migrations/{$timestamp}_create_comments_table.php"),
            ], 'migrations');
        }

        if (!class_exists('CreateLikesTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_likes_table.php.stub' => database_path("/migrations/{$timestamp}_create_likes_table.php"),
            ], 'migrations');
        }

        if (!class_exists('CreateViewsTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_views_table.php.stub' => database_path("/migrations/{$timestamp}_create_views_table.php"),
            ], 'migrations');
        }
    }
    public function register()
    {
        $this->app->bind(Interactions::class);
    }

    public static function determineCommentModel(): string
    {
        $commentModel = config('intercations.comment_model') ?? Comment::class;
        if (! is_a($commentModel, Comment::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($commentModel,Comment::class);
        }
        return $commentModel;
    }

    public static function determineLikeModel(): string
    {
        $likeModel = config('intercations.like_model') ?? Like::class;
        if (! is_a($likeModel, Like::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($likeModel,Like::class);
        }
        return $likeModel;
    }

    public static function determineViewModel(): string
    {
        $viewModel = config('intercations.view_model') ?? View::class;
        if (! is_a($viewModel, View::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($viewModel,View::class);
        }
        return $viewModel;
    }

    public static function getCommentModelInstance(): Model
    {
        $commentModelClassName = self::determineCommentModel();
        return new $commentModelClassName();
    }

    public static function getLikeModelInstance(): Model
    {
        $likeModelClassName = self::determineLikeModel();
        return new $likeModelClassName();
    }

    public static function getViewModelInstance(): Model
    {
        $viewModelClassName = self::determineViewModel();
        return new $viewModelClassName();
    }
}