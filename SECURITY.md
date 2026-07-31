# Security Policy

## Reporting Vulnerabilities

If you discover a security vulnerability in Laravel Daraja, please report it responsibly.

**Do NOT open a public issue.**

Instead, please email security concerns to the maintainer directly via GitHub. You can reach us through the [GitHub Security Advisories](https://github.com/Moodlood/laravel-daraja/security/advisories) feature.

## Response Timeline

- We will acknowledge your report within **48 hours**
- We will provide an initial assessment within **5 business days**
- We will work with you to understand and resolve the issue promptly

## Scope

This security policy covers the `moodlood/laravel-daraja` package itself. Issues related to the Safaricom Daraja API should be reported directly to Safaricom.

## Best Practices

When using this package, ensure you:

- Never commit your `.env` file or API credentials to version control
- Use environment variables for all sensitive configuration
- Keep the package updated to the latest version
- Use HTTPS for all callback URLs
- Validate webhook payloads before processing
