<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite19fa5a902b0a73259048743d1ce6c68
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Log\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Log\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite19fa5a902b0a73259048743d1ce6c68::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite19fa5a902b0a73259048743d1ce6c68::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}