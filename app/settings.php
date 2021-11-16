<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                // setting database toko
                'db' => [
                    'host' => 'localhost',
                    'user' => 'root',
                    'pass' => '',
                    'dbname' => 'db_slim_test_api',
                    'driver' => 'mysql'
                ],                
            ]);
        }
    ]);

    // database toko
    $container['db'] = function ($c){
        $settings = $c->get('settings')['db'];
        $server = $settings['driver'].":host=".$settings['host'].";dbname=".$settings['dbname'];
        $conn = new PDO($server, $settings["user"], $settings["pass"]);  
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    };
};
