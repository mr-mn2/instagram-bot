<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit85b805115dcb87ae6a3a664837b92539
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit85b805115dcb87ae6a3a664837b92539::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit85b805115dcb87ae6a3a664837b92539::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit85b805115dcb87ae6a3a664837b92539::$classMap;

        }, null, ClassLoader::class);
    }
}
