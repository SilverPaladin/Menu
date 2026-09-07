<?php

test('env debug', function () {
    dump([
        'env_helper' => env('APP_ENV'),
        'environment' => app()->environment(),
        'runningUnitTests' => app()->runningUnitTests(),
        'runningInConsole' => app()->runningInConsole(),
        'php_sapi' => PHP_SAPI,
        '_ENV' => $_ENV['APP_ENV'] ?? null,
        '_SERVER' => $_SERVER['APP_ENV'] ?? null,
        'getenv' => getenv('APP_ENV') ?: null,
        'config_app_env' => config('app.env'),
        'session_driver' => config('session.driver'),
        'db_connection' => config('database.default'),
        'db_database_sqlite' => config('database.connections.sqlite.database'),
    ]);
    expect(true)->toBeTrue();
});
