# Test verification

- Run `composer test` to clear cached Laravel configuration before running the Pest suite.
- Feature tests use `RefreshDatabase`; confirm they use the `testing` environment and SQLite `:memory:` database configured in `phpunit.xml`.
- Do not run tests directly against cached local configuration: it overrides the test environment, can cause CSRF failures, and can reset the local database.
- Run `php artisan optimize` only after tests pass. Check upgrade script syntax with `sh -n upgrade.sh`.
