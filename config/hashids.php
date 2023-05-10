<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@doubledip.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt' => md5('lJ}o@}RHJOsju=]{y[!nfsWQ+w![Hb++M4`p~K N3wE4-1m=2ulia#:,t@{tdSLH'),
            'length' => '25',
        ],

        'alternative' => [
            'salt' => md5('C.}fb)D&IA*dQQ]5|+;KEWjHKD}iCnJDCS0i|8_e:z/2r,.UVxU7_d<:]CDTe_ Q'),
            'length' => '25',
        ],

    ],

];
