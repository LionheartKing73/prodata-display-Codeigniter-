<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4d1c1967306cf31de2e1b60495dc8417
{
    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Math_' => 
            array (
                0 => __DIR__ . '/..' . '/pear/math_biginteger',
            ),
        ),
        'L' => 
        array (
            'Leth\\IPAddress' => 
            array (
                0 => __DIR__ . '/../..' . '/classes',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit4d1c1967306cf31de2e1b60495dc8417::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
