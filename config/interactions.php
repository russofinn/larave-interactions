<?php

return [

    /*
     * If set to true.
     */
    'enabled_mentions' => true,
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
        /*
         * If set to true, the subject returns soft deleted models.
         */
        'character' => '@',
        'regex' => '/\s({character}{pattern}{rules})/',
        'regex_replacement' => [
            '{character}' => '@',
            '{pattern}' => '[A-Za-z0-9]',
            '{rules}' => '{4,20}'
        ],

        /*
         * Model that will be mentioned.
         */
        'model' => 'App\User',

        /*
         * The column that will be used to search the model by the parser.
         */
        'column' => 'username',

        /*
         * The route used to generate the user link when mention
         */
        'route' => '/users/profile/@',

        'markdown' => true
    ],

    /*
     * This model will be used to log activity. The only requirement is that
     * it should be or extend the Russofinn\Interactions\Models\Commenty model.
     */
    'comment_model' => \Russofinn\Interactions\Models\Comment::class,

    /*
     * This model will be used to log activity. The only requirement is that
     * it should be or extend the Russofinn\Interactions\Models\Like model.
     */
    'like_model' => \Russofinn\Interactions\Models\Like::class,

    /*
     * This model will be used to log activity. The only requirement is that
     * it should be or extend the Russofinn\Interactions\Models\View model.
     */
    'view_model' => \Russofinn\Interactions\Models\View::class,

    /*
     * This model will be used to log activity. The only requirement is that
     * it should be or extend the Russofinn\Interactions\Models\Mention model.
     */
    'mention_model' => \Russofinn\Interactions\Models\Mention::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Comment model shipped with this package.
     */
    'table_name_comments' => 'comments',

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Like model shipped with this package.
     */
    'table_name_mentions' => 'likes',

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Like model shipped with this package.
     */
    'table_name_likes' => 'likes',

    /*
     * This is the name of the table that will be created by the migration and
     * used by the View model shipped with this package.
     */
    'table_name_views' => 'views'
];