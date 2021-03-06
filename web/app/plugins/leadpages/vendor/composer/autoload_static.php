<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit992fdebb08c02c905beb24c817d4c26d
{
    public static $files = array (
        'ad155f8f1cf0d418fe49e248db8c661b' => __DIR__ . '/..' . '/react/promise/src/functions_include.php',
        '603ce470d3b0980801c7a6185a3d6d53' => __DIR__ . '/..' . '/icanboogie/inflector/lib/helpers.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
            'TheLoop\\' => 8,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'R' => 
        array (
            'React\\Promise\\' => 14,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'L' => 
        array (
            'Leadpages\\Pages\\' => 16,
            'Leadpages\\Leadboxes\\' => 20,
            'Leadpages\\' => 10,
            'LeadpagesWP\\' => 12,
            'LeadpagesMetrics\\' => 17,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Stream\\' => 18,
            'GuzzleHttp\\Ring\\' => 16,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'TheLoop\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Framework',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'React\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/react/promise/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Leadpages\\Pages\\' => 
        array (
            0 => __DIR__ . '/..' . '/leadpages/pages/src/Pages',
        ),
        'Leadpages\\Leadboxes\\' => 
        array (
            0 => __DIR__ . '/..' . '/leadpages/leadboxes/src/Leadboxes',
        ),
        'Leadpages\\' => 
        array (
            0 => __DIR__ . '/..' . '/leadpages/leadpages-auth/src',
        ),
        'LeadpagesWP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
        'LeadpagesMetrics\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App/Lib/LeadpagesMetrics',
        ),
        'GuzzleHttp\\Stream\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/streams/src',
        ),
        'GuzzleHttp\\Ring\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/ringphp/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'R' => 
        array (
            'Requests' => 
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/library',
            ),
        ),
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static $classMap = array (
        'ICanBoogie\\Inflections' => __DIR__ . '/..' . '/icanboogie/inflector/lib/inflections.php',
        'ICanBoogie\\Inflector' => __DIR__ . '/..' . '/icanboogie/inflector/lib/inflector.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit992fdebb08c02c905beb24c817d4c26d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit992fdebb08c02c905beb24c817d4c26d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit992fdebb08c02c905beb24c817d4c26d::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit992fdebb08c02c905beb24c817d4c26d::$classMap;

        }, null, ClassLoader::class);
    }
}
