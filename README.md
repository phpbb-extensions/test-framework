# test-framework

Reusable GitHub Actions workflow for testing phpBB extensions across multiple environments.

This repository contains a pre-configured test workflow designed for phpBB extension developers. It runs your extension's tests using various PHP versions and database systems, including **MySQL**, **PostgreSQL**, **SQLite**, and **Microsoft SQL Server**.

## Table of Contents

- ✨ [Features](#-features)
- 🚀 [How to Use](#-how-to-use)
- 🛠 [Configuration Options](#-configuration-options)
- 📊 [Code Coverage with Codecov](#-code-coverage-with-codecov)

## ✨ Features

- Supports **PHP 7.2+** through **8.x**
- Tests against multiple database engines
- Optional checks for:
  - PHP CodeSniffer
  - Image ICC profile removal
  - EPV (Extension Pre Validator)
  - Files with executable permissions
  - Code coverage reports via Codecov

## 🚀 How to Use

On GitHub.com, go to your extension's repository, click **Add file → Create new file**, name it `.github/workflows/tests.yml`, add the workflow content shown below, and commit the file. Make sure to replace `acme/demo` with your actual extension vendor/package name, and optionally you may adjust any of the branch names and other checks.

```yaml
name: Tests

on:
    push:
        branches:   # Run tests when commits are pushed to these branches in your repo
            - main
            - master
            - develop
            - dev/*
    pull_request:   # Run tests when pull requests are made on these branches in your repo
        branches:
            - main
            - master
            - develop
            - dev/*

jobs:
    call-tests:
        name: Extension tests
        uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x # The phpBB branch to run tests with
        with:
            EXTNAME: acme/demo   # Your extension vendor/package name
        secrets:
            CODECOV_TOKEN: ${{ secrets.CODECOV_TOKEN }} # Do not edit or remove this
```

### Branches

Use the test-framework branch that matches the phpBB version you're developing for:

- `3.3.x`: Targets the phpBB 3.3.x release line.
- `master`: Targets the latest development version of phpBB (`master` branch).

> ‼️ Whichever branch of this framework you choose, be sure it is appended to the `uses:` line after the `@` symbol. For example, if you're targeting the `3.3.x` branch:
> 
> ```yaml
> uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
> ```

### Requirements

- Your extension's package contents must be located at the root level of the repository. That is, the repository **must directly represent the package**, with all relevant files such as `composer.json`, `README`, `LICENSE`, etc. placed directly in the **root of the repository**, **not inside a subdirectory within the repository**.
- Tests must be defined in your repository using PHPUnit.

## 🛠 Configuration Options

You can fine-tune this workflow with several optional arguments in the `with` section:

```yaml
call-tests:
    name: Extension tests
    uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x
    with:
        EXTNAME: acme/demo   # Your extension vendor/package name

        # OPTIONAL CONFIGURATIONS BELOW
        # The following arguments are optional and can be omitted if not needed.

        # The phpBB branch to use when running tests.
        # Default is '3.3.x', which this framework is designed for.
        # If using a different branch, ensure it's compatible with 3.3.x.
        # To test with phpBB's master branch, refer to the Branches section of this README.
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

        # Install NPM dependencies (if your extension relies on them)? 1 (yes) or 0 (no)
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

## 📊 Code Coverage with Codecov

This test framework supports code coverage reporting through [Codecov.io](https://codecov.io). To enable it, follow these steps:

### 1. Add a `codecov.yml` Path Fix

Codecov may report incorrect file paths if phpBB is cloned into a subdirectory. To fix this, add a `codecov.yml` file to the `.github/` directory of your extension’s repository with the following content:

```yaml
fixes:
    - "/phpBB3/phpBB/ext/acme/demo/::"
```

Make sure to replace `acme/demo` with your actual extension vendor/package name.

### 2. Enable Codecov in the Workflow

Ensure `CODECOV: 1` is set in your workflow call:

```yaml
with:
    ...
    CODECOV: 1
```

### 3. Get Your Codecov Token (if required)

Most public repositories do **not** require a token.  
For private repositories or certain CI setups, you may need a global **Codecov token**:

- Visit [https://codecov.io](https://codecov.io)
- Log in with your **GitHub** account
- Go to your [Codecov account settings](https://app.codecov.io/account/token)
- Copy the token

Then, in your GitHub repository:

- Navigate to **Settings → Secrets and variables → Actions**
- Click **"New repository secret"**
- Name it `CODECOV_TOKEN` and paste your token value

Once set up, Codecov will automatically collect and display coverage reports for your extension after each test run.

> 💡 You can view your coverage reports and badges by visiting your extension's page on [Codecov.io](https://codecov.io).

## 📄 License

[GNU General Public License v2](license.txt)
