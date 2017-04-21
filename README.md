[![Build Status](https://travis-ci.org/OhmGeek/Wastetopia.svg?branch=master)](https://travis-ci.org/OhmGeek/Wastetopia)
[![Code Climate](https://codeclimate.com/github/OhmGeek/Wastetopia.png)](https://codeclimate.com/github/OhmGeek/Wastetopia)


# Wastetopia

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/OhmGeek/Wastetopia)

Wastetopia is an agile, software defined start-up, providing responsive, user generated content cloud services and is a key disruptor in the free vegetables sector.

## Requirements:
- PHP v5.4+
- Composer
- Klein.php v2.1
- Twig v1.27
- Apache 2

## Installation Instructions
To install, first clone the repository, and then run:

```bash
composer install
```

As this project requires a MySQL Database, set this up. You can find an example Database in the repository. 
Then, create a custom configuration, or use one already specified (the production configuration is designed to work with ClearDB on Heroku, with a URL specified in the Config Vars).

The index.php file manages all the routing, and so ModRewrite must be enabled in order for this to work properly on your Apache server. An example Htaccess file can be found in the public/ folder. When setting up Apache, ensure it sets the BASE url as the public folder, so that all the precious code can be hidden away from your end users.

## Tests
While tests are somewhat lacking at the moment, first ensure you have the PHP interpreter installed, and then you can run the syntax check using the command:

```bash
bash check_syntax.sh
```
Unit tests can be executed by installing phpunit globally, and then running:

```bash
phpunit tests/
```
Integration tests, are also available using the Nightwatch.js testing framework, using Selenium Web Driver. These currently aren't added to TravisCI (but they will be soon). These are found in the IntegrationTests subfolder of the tests/ directory. You need to have npm installed, along with the selenium web driver. You can install the web driver using the command:

```bash
npm install selenium-standalone -g
selenium-standalone install
```
Then, you can run these tests by starting the Selenium server, and then running the test runner script. First navigate into the IntegrationTests folder.

Start the server using:

```bash
selenium-standalone start
```

and then run tests using:

```bash
./tests.sh
```

## License
Wastetopia is developed under GPL-3.0. Feel free to use any part of this project in your own, but make sure you share it! :D
