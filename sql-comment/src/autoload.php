<?php
require __DIR__ . '/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'S2Container'     => __DIR__.'/../vendor/s2dao/lib',
    'S2Dao'           => __DIR__.'/../vendor/s2dao/lib',
));

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

