# Contributing to Laravel Daraja

Thank you for considering contributing to Laravel Daraja! We welcome contributions of all kinds.

## Development Setup

1. Fork and clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Create a branch for your feature or fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Coding Standards

- Follow PSR-12 coding standards
- Use strict typing in every file (`declare(strict_types=1)`)
- Write PHPDoc blocks for public methods
- Keep classes focused (Single Responsibility Principle)
- Favor composition over inheritance

## Running Quality Checks

```bash
# Run tests
composer test

# Run static analysis
composer analyse

# Format code
composer format

# Check formatting without fixing
composer format-check
```

## Pull Request Process

1. Ensure all quality checks pass before submitting
2. Update documentation if you're changing public API
3. Add tests for any new functionality
4. Write a clear PR description explaining your changes
5. Reference any related issues

## Reporting Bugs

When reporting bugs, please include:

- Laravel and PHP versions
- Package version
- Steps to reproduce
- Expected vs actual behavior
- Any relevant logs (ensure no secrets are included)

## Security Vulnerabilities

If you discover a security vulnerability, please see [SECURITY.md](SECURITY.md) for responsible disclosure instructions.
