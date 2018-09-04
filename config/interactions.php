<?php

return [
    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),
    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => 365,
    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => 'default',
    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the default Laravel auth driver.
     */
    'default_auth_driver' => null,
    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    'mentions' => [
        'character' => '@',
        'regex' => '/\s({character}{pattern}{rules})/',
        'regex_replacement' => [
            '{character}' => '@',
            '{pattern}' => '[A-Za-z0-9]',
            '{rules}' => '{4,20}'
        ]
        'model' => 'App\User',
        'column' => 'username',
        'route' => '/users/profile/@'
    ],
    'prefix_mention' => '@',
    'search_mention_model' => 'App\User',
    'search_mention_column' => 'username',
    /*
     * This model will be used to log activity. The only requirement is that
     * it should be or extend the Spatie\Activitylog\Models\Activity model.
     */
    'comment_model' => \Russofinn\Interactions\Models\Comment::class,
    'like_model' => \Russofinn\Interactions\Models\Like::class,
    'view_model' => \Russofinn\Interactions\Models\View::class,
    'mention_model' => \Russofinn\Interactions\Models\Mention::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name_comments' => 'comments',
    'table_name_likes' => 'likes',
    'table_name_views' => 'views',
    'table_name_mentions' => 'mentions'

];