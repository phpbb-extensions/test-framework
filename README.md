test-framework
==============

Basic test framework for using Travis CI with phpBB Extensions

You can setup your extension to use this by following these steps:

1. Copy the example.travis.yml file into your extension directory.
2. Rename the file .travis.yml
3. Edit the file and change the VENDOR= and NAME= lines to fit your extension.
4. Add any tests you wish in your repository under tests/ (the format should be the same as the phpBB tests).

If you wish to see a simple example: https://github.com/phpbb-extensions/test-framework/tree/example