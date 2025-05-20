# phpBB Extension Test Framework

<img align="left" src="https://raw.githubusercontent.com/phpbb/website-assets/master/images/images/extensions/detective-bertie.png" alt="Detective Bertie running diagnostics" height="200" width="200">

This repository provides a reusable GitHub Actions workflow for phpBB extension developers. It supports testing phpBB extensions across multiple PHP versions and database engines including MySQL, PostgreSQL, SQLite, and Microsoft SQL Server.

Additional checks include PHP CodeSniffer, Extension Pre Validator (EPV), executable file detection, image ICC profile removal, and code coverage reporting via Codecov.

[![Build](https://github.com/phpbb-extensions/test-framework/actions/workflows/validate_workflows.yml/badge.svg)](https://github.com/phpbb-extensions/test-framework/actions/workflows/validate_workflows.yml)
[![Tests](https://github.com/phpbb/phpbb-ext-acme-demo/actions/workflows/tests.yml/badge.svg)](https://github.com/phpbb/phpbb-ext-acme-demo/actions/workflows/tests.yml)
<br clear="both"/>

## How to Use

Your extension's package contents must be located at the root level of the repository. That is, your extension's `composer.json` must be in the **root of the repository**, not inside a subdirectory within the repository.

On GitHub.com, go to your extension's repository, click **Add file → Create new file**, name it `.github/workflows/tests.yml`, add the workflow content shown below, and commit the file. Make sure to replace `acme/demo` with your actual extension vendor/package name, and optionally you may adjust any of the branch names and other checks.

```yaml
name: Tests

on:
  push:           # Run tests when commits are pushed to these branches in your repo,
    branches:     # ... or remove this branches section to run tests on all your branches
      - main      # Main production branch
      - master    # Legacy or alternative main branch
      - dev/*     # Any feature branches under "dev/", e.g., dev/new-feature
  
  pull_request:   # Run tests when pull requests are made on these branches in your repo,
    branches:     # ... or remove this branches section to run tests on all your branches
      - main
      - master
      - dev/*

jobs:
  call-tests:
    name: Extension tests
    uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
    with:
      EXTNAME: acme/demo   # Your extension vendor/package name (required)
```

### phpBB Branches

Use the test-framework branch that matches the phpBB version you're developing for:

- `3.3.x`: Targets the phpBB 3.3.x release line.
- `master`: Targets the latest development version of phpBB (`master` branch).

> ‼️ Whichever branch of this framework you choose, be sure it is appended to the `uses:` line after the `@` symbol. For example, if you're targeting the `3.3.x` branch:
>
> ```yaml
> uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
> ```

## Configuration Options

You can fine-tune this workflow with several optional arguments in the `with` section:

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo   # Your extension vendor/package name (required)

    # OPTIONAL CONFIGURATIONS BELOW
    # The following arguments are optional and can be omitted if not needed.

    # The phpBB repository's branch to use when running tests.
    # Default is '3.3.x', which this framework is designed for.
    # If using a different branch, ensure it's compatible with 3.3.x.
    # To test with phpBB's master branch, refer to the phpBB Branches section of this README.
    # Default: '3.3.x'
    PHPBB_BRANCH: '3.3.x'

    # Run phpBB's EPV (Extension Pre Validator)? 1 (yes) or 0 (no)
    # Default: 1
    EPV: 1

    # Check for files with executable permissions? 1 (yes) or 0 (no)
    # Default: 1
    EXECUTABLE_FILES: 1

    # Remove embedded ICC profiles from images? 1 (yes) or 0 (no)
    # Default: 1
    IMAGE_ICC: 1

    # Run CodeSniffer to detect PHP code style issues? 1 (yes) or 0 (no)
    # Default: 1
    SNIFF: 1

    # Run MySQL/MariaDB tests? 1 (yes) or 0 (no)
    # Default: 1
    RUN_MYSQL_JOBS: 1

    # Run PostgreSQL tests? 1 (yes) or 0 (no)
    # Default: 1
    RUN_PGSQL_JOBS: 1

    # Run MSSQL and SQLite3 tests? 1 (yes) or 0 (no)
    # Default: 1
    RUN_MSSQL_JOBS: 1

    # Run Windows IIS & PostgreSQL tests? 1 (yes) or 0 (no)
    # Default: 1
    RUN_WINDOWS_JOBS: 1

    # Run functional tests if you have them? 1 (yes) or 0 (no)
    # Default: 1
    RUN_FUNCTIONAL_TESTS: 1

    # Install npm dependencies (if your extension relies on them)? 1 (yes) or 0 (no)
    # Default: 0
    RUN_NPM_INSTALL: 0

    # Install Composer dependencies (if your extension relies on them)? 1 (yes) or 0 (no)
    # Default: 0
    RUN_COMPOSER_INSTALL: 0

    # CUSTOMISE PHP VERSIONS
    # To override the default PHP versions tested (7.2 through 8.4):

    # Preferred PHP version used for all test jobs.
    # Default: '7.2'
    PRIMARY_PHP_VERSION: '7.2'

    # The MySQL and PostgreSQL jobs run tests across multiple PHP versions.
    # List the PHP versions you want your extension tested with.
    # Default: '["7.2", "7.3", "7.4", "8.0", "8.1", "8.2", "8.3", "8.4"]'
    PHP_VERSION_MATRIX: '["7.2", "7.3", "7.4", "8.0", "8.1", "8.2", "8.3", "8.4"]'

    # Generate a code coverage report (see documentation below)? 1 (yes) or 0 (no)
    # Default: 0
    CODECOV: 0
```

## Configuration Examples

### Test an extension with phpBB 3.3.x

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
```

### Test an extension with phpBB's master-dev version

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@master
  with:
    EXTNAME: acme/demo
```

### Test an extension but skip the PostgreSQL on Linux and Windows tests

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
    RUN_PGSQL_JOBS: 0
    RUN_WINDOWS_JOBS: 0
```

### Test an extension that has no PHPUnit tests (basic checks only)

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
    RUN_MYSQL_JOBS: 0
    RUN_PGSQL_JOBS: 0
    RUN_MSSQL_JOBS: 0
    RUN_WINDOWS_JOBS: 0
```

### Test an extension that has no Functional tests

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
    RUN_FUNCTIONAL_TESTS: 0
```

### Test an extension that only supports PHP 8+

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
    PRIMARY_PHP_VERSION: '8.0'
    PHP_VERSION_MATRIX: '["8.0", "8.1", "8.2", "8.3", "8.4"]'
```

### Test an extension that has composer and npm dependencies

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@master
  with:
    EXTNAME: acme/demo
    RUN_NPM_INSTALL: 1
    RUN_COMPOSER_INSTALL: 1
```

### Test an extension + generate a code coverage report

This test framework supports code coverage reporting through [Codecov.io](https://codecov.io).

```yaml
call-tests:
  name: Extension tests
  uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
  with:
    EXTNAME: acme/demo
    CODECOV: 1
  secrets:                                      # This must be included
    CODECOV_TOKEN: ${{ secrets.CODECOV_TOKEN }} # This must be included
```

> **Get Your Codecov Token (if required)**
>
> Most public repositories do **not** require a token.  
> For private repositories or certain CI setups, you may need a global **Codecov token**:
>
> - Visit [https://codecov.io](https://codecov.io)
> - Log in with your **GitHub** account
> - Go to your [Codecov account settings](https://app.codecov.io/account/token)
> - Copy the token
>
> Then, in your GitHub repository:
>
> - Navigate to **Settings → Secrets and variables → Actions**
> - Click **"New repository secret"**
> - Name it `CODECOV_TOKEN` and paste your token value
>
> 💡 You can view your coverage reports and badges by visiting your extension's page on [Codecov.io](https://codecov.io).

## When the configuration options aren’t enough

If testing your extension requires more flexibility than the provided configuration options allow, you have two choices:

**Open an Issue** – If something is missing or could be improved, feel free to [open an issue](https://github.com/phpbb-extensions/test-framework/issues). Suggestions and feedback are welcome and may help improve the framework for everyone.

**Create Your Own Custom Version** – For highly specific needs, you can create your own version of this framework by using it as a template. Just click **Use this template → Create a new repository** to get started. Once your custom repository is set up, you can modify the workflow as needed. Then, reference your version of the framework from your extension’s test workflow like so:

```yaml
call-tests:
  name: Extension tests
  uses: your-org/your-repo/.github/workflows/tests.yml@your-branch
  with:
    EXTNAME: acme/demo
    PHPBB_BRANCH: 3.3.x
```

## Status Badges

Display a status badge in your repository to indicate the status of your test results.

![Tests](https://github.com/phpbb/phpbb-ext-acme-demo/actions/workflows/tests.yml/badge.svg)

```md
[![Tests](https://github.com/your-org/your-repo/actions/workflows/tests.yml/badge.svg)](https://github.com/your-org/your-repo/actions/workflows/tests.yml)
```

## Contributing

Issues and pull requests are welcome! If you have suggestions for improvement, feel free to [open an issue](https://github.com/phpbb-extensions/test-framework/issues).

## License

[GNU General Public License v2](https://opensource.org/licenses/GPL-2.0)
