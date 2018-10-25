<?php

namespace App\Services;

use Pimple\ServiceProviderInterface;
use Pimple\Container as Pimple;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

class EloquentServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $pimple A container instance
     */
    public function register(Pimple $pimple)
    {
        $settings = $pimple['settings']['database'];
        
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => $settings['driver'],
            'host'      => $settings['host'],
            'database'  => $settings['database'],
            'username'  => $settings['username'],
            'password'  => $settings['password'],
            'charset'   => $settings['charset'],
            'collation' => $settings['collation'],
            'prefix'    => $settings['prefix'],
        ]);
        
        $capsule->setEventDispatcher(new Dispatcher);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        
        $connection = $capsule->getConnection();
        $schema = $connection->getSchemaBuilder();

        // https://laravel.com/docs/master/migrations#creating-indexes
        // Index Lengths & MySQL / MariaDB
        // Laravel uses the utf8mb4 character set by default,
        // which includes support for storing "emojis" in the database.
        // If you are running a version of MySQL older than the 5.7.7 release or
        // MariaDB older than the 10.2.2 release, you may need to manually configure
        // the default string length generated by migrations in order for MySQL
        // to create indexes for them. You may configure this by calling the
        // Schema::defaultStringLength method within your AppServiceProvider:

        $schema->defaultStringLength(190);

        // Enable query log

        // if ( ! is_null($this->logger)) {
        //     $connection->enableQueryLog();
        //     $connection->listen(function ($query) {
        //         $this->logger->addInfo($query->sql);
        //     });
        // }
        
        // Relation::morphMap([
        //     'action' => App\Models\Action::class,
        // ]);

        $pimple['db'] = function ($c) use ($connection) {
            return $connection;
        };
    }
}