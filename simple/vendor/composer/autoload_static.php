<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4d6ff87918faf1f59b3582e55033dbdc
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Longtnt\\SimplePermission\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Longtnt\\SimplePermission\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4d6ff87918faf1f59b3582e55033dbdc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4d6ff87918faf1f59b3582e55033dbdc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4d6ff87918faf1f59b3582e55033dbdc::$classMap;

        }, null, ClassLoader::class);
    }
}
