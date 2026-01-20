# Contributing to Personal Billing App

Thank you for your interest in contributing! This document provides guidelines for contributing to the project.

## Coding Standards

- Follow Laravel conventions and PSR-12 coding standards
- Use meaningful variable and method names
- Keep methods short and focused (single responsibility)
- Add docblocks to public methods and classes
- Add comments only when explaining "why", not "what"

## Account Scoping

**CRITICAL**: Always use explicit account scoping in queries:

```php
// ✅ Good - explicit scope
Transaction::forAccount($accountId)->get();

// ❌ Bad - missing account scope
Transaction::all();
```

Never use global scopes for account filtering - this breaks admin queries, background jobs, and tests.

## Permission Checks

Use role-based permissions via the User model:

```php
$user->hasPermissionInAccount($accountId, 'transactions.create');
```

Permissions are defined in `config/permissions.php` and mapped to roles in the `account_user` pivot table.

## Database Changes

- Always create migrations for schema changes
- Include rollback logic in `down()` method
- Add proper indexes for query performance
- Use DB transactions for multi-step operations

## Branch Naming

- Feature: `feature/description-of-feature`
- Bug fix: `fix/description-of-bug`
- Documentation: `docs/description`

## Commit Style

Use clear, descriptive commit messages:

```
Add immutable transaction rule

- Transactions older than 30 days cannot be edited
- Prevents historical data corruption
- Configurable via config/billing.php
```

## Pull Request Process

1. Fork the repository
2. Create a feature branch
3. Make your changes following coding standards
4. Ensure all queries are account-scoped
5. Test your changes
6. Submit a pull request with a clear description

## Testing

Write tests for:
- Critical business logic (services)
- Authorization (policies)
- Account scoping (queries)

## Questions?

Open an issue for discussion before making major changes.
