<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3ab1f9f6203450c0d39123b705f7ae20
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Safiy\\NexusCryp\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Safiy\\NexusCryp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3ab1f9f6203450c0d39123b705f7ae20::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3ab1f9f6203450c0d39123b705f7ae20::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3ab1f9f6203450c0d39123b705f7ae20::$classMap;

        }, null, ClassLoader::class);
    }
}
