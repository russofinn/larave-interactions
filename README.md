# Registre as interações do usuário em seu aplicativo Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-activitylog.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-activitylog)
[![Build Status](https://img.shields.io/travis/spatie/laravel-activitylog/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-activitylog)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-activitylog.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-activitylog)
[![StyleCI](https://styleci.io/repos/61802818/shield)](https://styleci.io/repos/61802818)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-activitylog.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-activitylog)

O pacote `russofinn/laravel-interactions` fornece funções fáceis de usar para interações dos usuários em seu aplicativo

Aqui está uma demonstração de como você pode usá-lo:

```php
interactions()->comment('Look, I said something.');
```

Você pode recuperar todas as interações usando o model `Russofinn\Interactions\Models\Comment`,`Russofinn\Interactions\Models\Like` e `Russofinn\Interactions\Models\View`.


## Documentation
You'll find the documentation on [https://docs.spatie.be/laravel-activitylog/v2](https://docs.spatie.be/laravel-activitylog/v2).

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving the activity log? Feel free to [create an issue on GitHub](https://github.com/spatie/laravel-activitylog/issues), we'll try to address it as soon as possible.

If you've found a security issue please mail [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.


## Installation

You can install the package via composer:

``` bash
composer require russofinn/laravel-interactions
```

The package will automatically register itself.

You can publish the migration with:
```bash
php artisan vendor:publish --provider="Russofinn\Interactions\InteractionsServiceProvider" --tag="migrations"
```

*Nota*: O migrations padrão assume que você esteja usando números inteiros para seus IDs. Se você estiver usando UUIDs ou algum outro formato, ajuste o formato dos campos subject_id e causer_id nos migrations antes de continuar.

Depois de publicar os migrations, você pode criar as tabelas `comments`,`likes`,`views` e `mentions` executando o seguinte comando:


```bash
php artisan migrate
```

Você pode, opcionalmente, publicar o arquivo de configuração com:
```bash
php artisan vendor:publish --provider="Russofinn\Interactions\InteractionsServiceProvider" --tag="config"
```

Este é o conteúdo do arquivo de configuração publicado:

```php
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

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

Se você descobrir algum problema relacionado à segurança, envie um e-mail para almeida.weslley577@gmail.com em vez de abrir um ISSUE.

## Credits

- [Weslley Almeida](https://github.com/russofinn)
- [All Contributors](../../contributors)

## License

Este pacote está diponibilizado sob MIT License (MIT). Leia [Arquivo de Licença](LICENSE.md) para maiores informações.