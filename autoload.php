<?php

use Composer\Autoload\ClassLoader;

$ns_base = "DISMaja\\Webtrees\Module\\UnlinkedIndividual\\";

$loader = new ClassLoader();
$loader->addPsr4($ns_base, __DIR__);
$loader->addPsr4($ns_base . "Http\\", __DIR__ . "/Http");

#$classMap = array();
#
#$loader->addClassMap($classMap);
$loader->register();
