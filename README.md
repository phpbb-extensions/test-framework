test-framework
==============

Basic test framework for using Travis CI with phpBB Extensions

You can setup your extension to use this by following these steps:

1. Copy the **example.travis.yml** file into your extension directory.

2. Rename the file **.travis.yml**

3. Edit the file and change the VENDOR= and NAME= lines to fit your extension.

4. Add any tests you wish in your repository under `tests/` (the format should be the same as the phpBB tests).

If you wish to see a simple example: https://github.com/phpbb-extensions/test-framework/tree/example

* * *

If you wish to run your tests on your local system (from a checked out copy of phpBB develop):

Note: Change your/extension to your vendor name/extension name.

1. Clone your extension into your checked out copy of phpBB develop to:
`phpBB/ext/your/extension/`

2. Copy `tests/framework/*` to `phpBB/ext/your/extension/tests/framework/*`

3. Copy `travis/bootstrap.php` to `phpBB/ext/your/extension/tests/bootstrap.php`

4. Copy `travis/phpunit-mysql-travis.xml` to `phpBB/ext/your/extension/tests/phpunit-mysql-travis.xml`

5. Rename **phpunit-mysql-travis.xml** to **phpunit-mysql-local.xml**

5. Edit **phpunit-mysql-local.xml** changing the server config settings to match your phpBB config.php settings:

6. Run the tests from the command line using:

`phpBB/vendor/bin/phpunit -c phpBB/ext/your/extension/tests/phpunit-mysql-local.xml`
