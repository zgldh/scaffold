<?php
/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 09/06/2017
 * Time: 12:31
 */

function AddServiceProvider($serviceProviderClassName)
{
    $appConfigPath = realpath( 'config/app.php');
    $appConfig = file_get_contents($appConfigPath);
    if (strpos($appConfig, $serviceProviderClassName) === false) {
        $providerEndIndex = strpos($appConfig, "]", strpos($appConfig, " 'providers' => ["));
        $appConfig = substr($appConfig, 0, $providerEndIndex) .
            "    " . $serviceProviderClassName . ",\n    " .
            substr($appConfig, $providerEndIndex);
        file_put_contents($appConfigPath, $appConfig);
    }
}

$scaffoldServiceProvider = 'zgldh\Scaffold\ScaffoldServiceProvider::class';
AddServiceProvider($scaffoldServiceProvider);