test-framework
==============

Basic test framework for using Travis CI with phpBB Extensions

You can setup your extension to use this by following these steps:

1. Copy the example.travis.yml file into your extension directory.
2. Rename the file .travis.yml
3. Edit the file and change the VENDOR= and NAME= lines to fit your extension.
4. Add any tests you wish in your repository under tests/ (the format should be the same as the phpBB tests).

If you wish to see a simple example: https://github.com/phpbb-extensions/test-framework/tree/example

If you wish to run your tests on your local system (from a checked out copy of phpbb develop):

Note: Change your/extension to your vendor name/extension name.

1. Copy tests/framework/* to phpBB/ext/your/extension/tests/framework/*
2. Edit tests/bootstrap.php and add this line at the end:
	require_once $phpbb_root_path . 'ext/your/extension/tests/framework/extension_database_test_case.php';
