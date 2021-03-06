<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit937e61223d9b8407378ca066bc7420e7
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SlowProg\\CopyFile\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SlowProg\\CopyFile\\' => 
        array (
            0 => __DIR__ . '/..' . '/slowprog/composer-copy-file',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit937e61223d9b8407378ca066bc7420e7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit937e61223d9b8407378ca066bc7420e7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
