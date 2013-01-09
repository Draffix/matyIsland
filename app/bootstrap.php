<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;
use Nette\Utils\Strings;

// Load Nette Framework or autoloader generated by Composer
require LIBS_DIR . '/autoload.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode(TRUE); 
$configurator->setDebugMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
        ->addDirectory(APP_DIR)
        ->addDirectory(LIBS_DIR)
        ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

Route::addStyle('titleProduct');
Route::setStyleProperty('titleProduct', Route::FILTER_OUT, function($url) {
            return Strings::webalize($url);
        });

Route::setStyleProperty('titleProduct', Route::FILTER_IN, function($url) {
            return Strings::webalize($url);
        });

Route::addStyle('titleCategory');
Route::setStyleProperty('titleCategory', Route::FILTER_OUT, function($url) {
            return Strings::webalize($url);
        });

Route::setStyleProperty('titleCategory', Route::FILTER_IN, function($url) {
            return Strings::webalize($url);
        });

// Admin base router
$container->router[] = new Route('admin[/<presenter>[/<action>[/<id>]]]?strana=<paginator-page> ', array(
            'module' => "admin",
            'presenter' => array(
                Route::VALUE => 'Homepage',
                Route::FILTER_TABLE => array(
                    // řetězec v URL => presenter
                    'produkt' => 'Product',
                    'objednavka' => 'Order'                   
                ),
            ),
            'action' => array(
                Route::VALUE => 'default',
                Route::FILTER_TABLE => array(
                    // řetězec v URL => akce
                    'ukaz' => 'show',
                ),
            ),
            'id' => NULL,
        ));

$container->router[] = new Route('admin/produkt/<id>', array(
            'module' => "admin",
            'presenter' => 'Product',
            'action' => 'default'
        ));

$container->router[] = new Route('admin/objednavka/<id>', array(
            'module' => "admin",
            'presenter' => 'Order',
            'action' => 'default'
        ));


// Setup router
$container->router[] = new Route('index.php', array(
            'presenter' => 'Homepage',
            'action' => 'default',
                ), Route::ONE_WAY);

$container->router[] = new Route('kategorie/<id>-<titleCategory>', array(
            'presenter' => 'Category',
            'action' => 'default'
        ));

$container->router[] = new Route('produkt/<id>-<titleProduct>', array(
            'presenter' => 'Product',
            'action' => 'default'
        ));

$container->router[] = new Route('[<presenter>[/<action>[/<id>]]]?strana=<paginator-page> ', array(
            'presenter' => array(
                Route::VALUE => 'Homepage',
                Route::FILTER_TABLE => array(
                    // řetězec v URL => presenter
                    'produkt' => 'Product',
                    'kosik' => 'Basket',
                    'kategorie' => 'Category',
                    'objednavka' => 'Order',
                    'prihlaseni' => 'Login',
                    'registrace' => 'Registration',
                    'vyhledavani' => 'Search',
                ),
            ),
            'action' => array(
                Route::VALUE => 'default',
                Route::FILTER_TABLE => array(
                    // řetězec v URL => akce
                    'platba-doprava' => 'paymentDelivery',
                    'shrnuti' => 'summary',
                    'dokonceni' => 'complete',
                    'obchodni-podminky' => 'tradeTerms',
                    'napiste-nam' => 'tellUs',
                    'kontakt' => 'contact',
                    'novinky' => 'news',
                ),
            ),
            'id' => NULL,
        ));

// Configure and run the application!
$container->application->run();