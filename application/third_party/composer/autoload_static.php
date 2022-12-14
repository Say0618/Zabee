<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitae517d4e8fd88fbf568ecb3c93ffbd85
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitae517d4e8fd88fbf568ecb3c93ffbd85::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitae517d4e8fd88fbf568ecb3c93ffbd85::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
