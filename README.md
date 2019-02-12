Custom urls yii2 extension
========================
Custom urls yii2 extension

## Info

The best practice is use this module/extension with [yii2 advanced application](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

1. Either run

    ```
    php composer.phar require dmitriikoziuk/yii2-custom-urls
    ```
    
    or add
    
    ```
    "dmitriikoziuk/yii2-custom-urls": "~0.2.0"
    ```
    
    to the require section of your `composer.json` file.
    
2. Create a new database and adjust the `components['db']` configuration in `/path/to/yii-application/common/config/main-local.php` accordingly.

3. Open a console terminal, apply migrations with command `/path/to/php-bin/php /path/to/yii-application/yii migrate`.

4. Run command `/path/to/php-bin/php /path/to/yii-application/yii migrate --migrationPath=@DmitriiKoziuk/yii2CustomUrls/migrations`.
