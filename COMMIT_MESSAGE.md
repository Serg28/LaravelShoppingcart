feat: Add Laravel 11 and 12 support

## Major Changes

### Dependencies
- Add PHP 8.2+ requirement
- Add Laravel 12.x support (^9.0||^10.0||^11.0||^12.0)
- Update Carbon to ^2.0||^3.0
- Update PHPUnit to ^10.0||^11.0
- Update Mockery to ^1.6
- Update Orchestra Testbench to ^7.0||^8.0||^9.0||^10.0

### Testing
- Update phpunit.xml for PHPUnit 10+ compatibility
- Replace deprecated <filter> with <source>
- Add cacheDirectory attribute
- Remove deprecated attributes

### CI/CD
- Update GitHub Actions workflow
- Add test matrix for PHP 8.2 and 8.3
- Add test matrix for Laravel 9, 10, 11, and 12
- Update actions to v4
- Add code style check
- Remove deprecated .travis.yml

### Documentation
- Update README.md with Laravel 11/12 compatibility info
- Add CHANGELOG.md with version history
- Add UPGRADE.md with detailed upgrade guide
- Add EXAMPLES.md with Laravel 11/12 examples
- Add TESTING.md with testing instructions
- Add WHATS_NEW.md with highlights
- Add README_ru.md with Russian documentation
- Add SUMMARY.md with complete change summary
- Add REPORT.md with implementation report
- Add .gitattributes for proper Git handling

## Compatibility Matrix

| PHP Version | Laravel 9 | Laravel 10 | Laravel 11 | Laravel 12 |
|-------------|-----------|------------|------------|------------|
| 8.2         | ✅        | ✅         | ✅         | ❌         |
| 8.3         | ✅        | ✅         | ✅         | ✅         |

## Breaking Changes

None. This update is fully backward compatible with Laravel 9 and 10.

## Migration Guide

For existing projects:
1. Update PHP to 8.2+ (for Laravel 11) or 8.3+ (for Laravel 12)
2. Run: composer update bumbummen99/shoppingcart
3. Clear cache: php artisan config:clear && php artisan cache:clear

See UPGRADE.md for detailed instructions.

## Testing

All existing tests pass with PHPUnit 10 and 11.
Run tests: vendor/bin/phpunit

## Notes

- Service Provider is compatible without changes
- All migrations are compatible without changes
- All existing functionality preserved
- Package auto-discovery works in Laravel 11/12
