# test-framework

Reusable GitHub Actions workflow for testing phpBB extensions across multiple environments.

This repository contains a pre-configured test workflow designed for phpBB extension developers. It runs your extension's tests using various PHP versions and database systems, including **MySQL**, **PostgreSQL**, **SQLite**, and **Microsoft SQL Server**.

## Table of Contents

- ✨ [Features](#-features)
- 🚀 [How to Use](#-how-to-use)
- ✅ [Requirements](#-requirements)
- 🛠 [Advanced Configuration Options](#-advanced-configuration-options)
- 📊 [Code Coverage with Codecov](#-code-coverage-with-codecov)

## ✨ Features

- Supports **PHP 7.2+** through **8.x**
- Tests against multiple database engines
- Optional checks for:
  - PHP CodeSniffer
  - ICC image profiles
  - EPV (Extension Pre Validator)
  - Executable files
  - Code coverage via Codecov

## 🚀 How to Use

On GitHub.com, go to your extension's repository, click **Add file → Create new file**, name it `.github/workflows/tests.yml`, add the workflow content shown below, and commit the file. Make sure to replace `acme/demo` with your actual extension vendor/package name, and optionally you may adjust any of the branch names and other checks.

```yaml
name: Tests

on:
    push:
        branches:        # Run tests when commits are pushed to these branches in your repo
            - main
            - master
            - develop
            - dev/*
    pull_request:        # Run tests when pull requests are made on these branches in your repo
        branches:
            - main
            - master
            - develop
            - dev/*

jobs:
    call-tests:
        uses: phpbb-extensions/test-framework/.github/workflows/tests.yml@3.3.x  # Must match PHPBB_BRANCH
        with:
            EXTNAME: acme/demo   # Your extension vendor/package name
            SNIFF: 1             # Run code sniffer on your code? 1 or 0
            IMAGE_ICC: 1         # Run icc profile sniffer on your images? 1 or 0
            EPV: 1               # Run EPV (Extension Pre Validator) on your code? 1 or 0
            EXECUTABLE_FILES: 1  # Run check for executable files? 1 or 0
            CODECOV: 0           # Run code coverage via codecov? 1 or 0
            PHPBB_BRANCH: 3.3.x  # The phpBB branch to run tests on
        secrets:
            CODECOV_TOKEN: ${{ secrets.CODECOV_TOKEN }} # Do not edit this
```

### Branches

Use the branch that matches the phpBB version you're developing for.

- `3.3.x`: Targets the **phpBB 3.3.x** release line.
- `master`: Targets the latest development version of **phpBB** (`master` branch).

## ✅ Requirements

- Your extension's package contents must be located at the root level of the repository. That is, the repository **must directly represent the package**, with all relevant files such as `composer.json`, `README`, `LICENSE`, etc. placed directly in the **root of the repository**, **not inside a subdirectory within the repository**. See any of phpbb-extension's official extension repositories as an example.
- Tests must be defined in your repository using PHPUnit.

## 🛠 Advanced Configuration Options

You can fine-tune this workflow with several optional arguments:

## 🔃 Selectively Run Job Groups

By default, all test job groups run. You can skip any by setting their arguments to 0:

```yaml
with:
    ...
    RUN_BASIC_JOBS: 0       # Skip checks like sniffer, EPV, etc.
    RUN_MYSQL_JOBS: 0       # Skip MySQL/MariaDB tests
    RUN_POSTGRESQL_JOBS: 0  # Skip PostgreSQL tests
    RUN_MSSQL_JOBS: 0       # Skip MSSQL and SQLite3 tests
```

> 💡 Omit these inputs or set them to 1 to enable each group (default behavior).

## 📦 Install Dependencies

If your extension relies on NPM or Composer dependencies, enable automated installs:

```yaml
with:
    ...
    RUN_NPM_INSTALL: 1        # Run `npm ci`
    RUN_COMPOSER_INSTALL: 1   # Run `composer install`
```

> 💡 Omit if not needed.

## 🐘 Customise PHP Versions

To override the default PHP versions tested (7.2 through 8.4), use:

```yaml
with:
    ...
    PRIMARY_PHP_VERSION: '8.1'
    PHP_VERSION_MATRIX: '["8.0", "8.1", "8.2", "8.3", "8.4"]'
```

- `PRIMARY_PHP_VERSION`: PHP version used for all jobs, including checks and MSSQL. Default is 7.2.
- `PHP_VERSION_MATRIX`: List of all PHP versions you want tested (in MySQL and PostgreSQL jobs).

> 💡 Use this if your extension supports a limited or newer PHP version range. Omit if not needed.

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
