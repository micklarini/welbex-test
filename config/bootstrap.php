<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__)."/vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration([dirname(__DIR__)."/src"], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// database configuration parameters
$conn = array(
    'driver' => 'pdo_mysql',
    'url' => $_ENV['DATABASE_URL'],
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
