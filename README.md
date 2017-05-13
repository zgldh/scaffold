## zgldh/Scaffold

1. Edit composer.json
 ```
    "repositories": [
        {
            "type": "path",
            "url": "zgldh/Scaffold"
        }
    ],
    "require": {
      "zgldh/scaffold": "^0.1",
      "infyomlabs/laravel-generator": "5.3.x-dev",
      "laracasts/generators": "dev-master"
    }
 ```
2. composer update zgldh/scaffold -vvv
3. edit your `.env` to setup database configuration. 
2. php artisan zgldh:scaffold:init
2. php artisan zgldh:scaffold:init --setup
4. All set.
