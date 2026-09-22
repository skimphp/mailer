# SKIM Mailer Extension — Developer Guide

This document outlines how to maintain and develop the `skim/mailer` extension, highlighting integration with the SKIM Framework static manifest specifications and local Ethereal preview workflows.

## Development Environment Setup

To run tests and develop `skim/mailer` in isolation, use the `skim_framework` Docker environment:

```bash
cd /Users/liquan/Documents/web/skim_extensions/mailer

# Set up local symlink repository for skim/framework
docker compose \
  -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
  run --rm -T \
  -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
  -v /Users/liquan/Documents/web/skim_framework:/skim_framework \
  -w /work \
  app sh -lc 'composer config repositories.skim_framework "{\"type\":\"path\",\"url\":\"/skim_framework\",\"options\":{\"versions\":{\"skim/framework\":\"1.0.0\"}}}" && composer install'
```

## Ethereal Local Preview SMTP Testing

To test real SMTP mail generation during development, we use Ethereal Mail. 

1. Create test credentials at [https://ethereal.email](https://ethereal.email).
2. Create a local `.env.ethereal` file inside `skim_extensions/mailer/`:
   ```dotenv
   ETHEREAL_USERNAME=your-ethereal-address@ethereal.email
   ETHEREAL_PASSWORD=your-ethereal-password
   ```
3. Send a preview mail to check configuration:
   ```bash
   docker compose \
     -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
     run --rm -T \
     -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
     -v /Users/liquan/Documents/web/skim_framework:/skim_framework \
     -w /work \
     app php bin/preview-test you@example.com
   ```

## SKIM Framework Core Features Integration

As an extension developer, your code interacts with several framework-level mechanisms configured to assist AI agents and debuggers:

### 1. Dependency Validation & Topological Sorting
- **What it does**: The SKIM Framework reads your extension's static manifest and/or `composer.json` `extra.skim.requires` section. It automatically validates that all dependencies are satisfied by active extensions before invoking `register()` or `boot()`.
- **How it boots**: Extensions are sorted topologically so that dependencies always boot before dependents.
- **Developer Action**: Always keep `requires` in `mailer_extension::manifest()` and `composer.json` in sync.

### 2. Architecture Invariant Violation Audits
- **What it does**: The framework enforces a strict lifecycle mutation guard. Post-boot, the app is "frozen", preventing register, route, service, or middleware alterations.
- **Audit Logging**: Any post-boot violation throws a `LogicException` and writes an audit log in PHP's error stream containing the offending extension name:
  `[skim][invariant-violation] extension='mailer' attempted 'bind' after freeze`
- **Developer Action**: Never register bindings or append routes in callback listeners that execute after the `boot()` lifecycle phase finishes.

### 3. Structured Request Tracing (`request_trace`)
- **What it does**: In debug mode (`APP_DEBUG=true`), the framework captures a high-resolution structured trace timeline of each HTTP request, including active extensions, loaded middleware, and controller execution times.
- **Developer Action**: You can trace custom mail dispatch milestones or transport timings using the `request_trace` API:
  ```php
  use Skim\Dev\RequestTrace;

  request_trace::event('mail_transport_dispatched', ['driver' => $driver, 'subject' => $subject]);
  ```

## Running Tests

Execute Pest feature and unit tests inside the container environment:

```bash
docker compose \
  -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
  run --rm -T \
  -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
  -v /Users/liquan/Documents/web/skim_framework:/skim_framework \
  -w /work \
  app ./vendor/bin/pest tests/unit/extension_manifest_test.php tests/unit/transport_factory_test.php tests/feature/mailer_test.php
```
