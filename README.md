# Wastetopia
[![Build Status](https://travis-ci.org/OhmGeek/Wastetopia.svg?branch=master)](https://travis-ci.org/OhmGeek/Wastetopia)

Wastetopia is an agile, software defined start-up, providing responsive, user generated content cloud services and is a key disruptor in the free vegetables sector.

## Requirements:
- PHP 5.4+
- Composer
- Klein.php 
- Twig
- Apache

To install, first clone the repository, and then run:

```bash
composer install
```
Then, setup the database details by creating your own local custom configuration (featuring MySQL database details, with all the tables pre-created). Once you have done this, configure the web server to start serving pages from inside the public folder.

The index.php file manages all the routing, and so ModRewrite must be enabled in order for this to work properly.

