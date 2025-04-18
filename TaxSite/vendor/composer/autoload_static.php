<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc8338cf7be6facb464e380585dacc834
{
    public static $files = array (
        '271cb6f21c9ae69ccbad5cc1b8d6707c' => __DIR__ . '/..' . '/wapmorgan/morphos/src/English/functions.php',
        '34d31f2fd925dfe2696a521f5ec12db2' => __DIR__ . '/..' . '/wapmorgan/morphos/src/Russian/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'morphos\\' => 8,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'L' => 
        array (
            'Laminas\\Escaper\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'morphos\\' => 
        array (
            0 => __DIR__ . '/..' . '/wapmorgan/morphos/src',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Laminas\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/laminas/laminas-escaper/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc8338cf7be6facb464e380585dacc834::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc8338cf7be6facb464e380585dacc834::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc8338cf7be6facb464e380585dacc834::$classMap;

        }, null, ClassLoader::class);
    }
}
