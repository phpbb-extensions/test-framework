# test-framework

Reusable GitHub Actions workflow for testing phpBB extensions across multiple environments.

This repository provides a pre-configured test workflow designed for phpBB extension developers. It runs your extension's tests using various PHP versions and database systems, including **MySQL**, **PostgreSQL**, **SQLite**, and **Microsoft SQL Server**.

## 🚀 Features

- Supports **PHP 7.2+** through **8.x**
- Tests against multiple database engines
- Optional checks for:
  - PHP CodeSniffer
  - ICC image profiles
  - EPV (Extension Pre Validator)
  - Executable files
  - Code coverage via Codecov

## 🧪 Branches

- `3.3.x`: Targets the **phpBB 3.3.x** release line.
- `master`: Targets the latest development version of **phpBB** (`master` branch).

Use the branch that matches the phpBB version you're developing for.

## 📦 How to Use

Add a `tests.yml` file in your extension’s `.github/workflows/` directory. Here's a complete example:

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

## ✅ Requirements

- Your extension must follow standard phpBB extension structure.
- Tests must be defined in your repository using PHPUnit.

## 📊 Code Coverage with Codecov

This test framework supports code coverage reporting through [Codecov.io](https://codecov.io). To enable it, follow these steps:

### 1. Add a `codecov.yml` Path Fix

Codecov may report incorrect file paths if phpBB is cloned into a subdirectory. To fix this, add a `codecov.yml` file to the `.github/` directory of your extension’s repository with the following content:

```yaml
fixes:
    - "/phpBB3/phpBB/ext/acme/demo/::"
```

Make sure to replace `acme/demo` with your actual extension vendor/package name.

### 2. Sign in to Codecov

- Visit [https://codecov.io](https://codecov.io)
- Log in with your **GitHub** account

### 3. Get Your Codecov Token (if required)

Most public repositories do **not** require a token.  
For private repositories or certain CI setups, you may need a global **Codecov token**:

- Go to your [Codecov account settings](https://app.codecov.io/account/token)
- Copy the token

Then, in your GitHub repository:

- Navigate to **Settings → Secrets and variables → Actions**
- Click **"New repository secret"**
- Name it `CODECOV_TOKEN` and paste your token value

### 4. Enable Codecov in the Workflow

Ensure `CODECOV: 1` is set in your workflow call:

```yaml
with:
    ...
    CODECOV: 1
    ...
```

Once set up, Codecov will automatically collect and display coverage reports for your extension after each test run.

> 💡 You can view your coverage reports and badges by visiting your extension's page on [Codecov.io](https://codecov.io).

## 📄 License

[GNU General Public License v2](license.txt)
