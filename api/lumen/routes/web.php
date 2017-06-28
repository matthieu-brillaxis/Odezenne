<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api/v1/'], function ($app) {

    $app->group(['prefix' => 'socials/'], function ($app) {
        $app->get('/', 'SocialController@index');

        $app->group(['prefix' => 'twitter/'], function ($app) {
            $app->get('/feed', 'SocialController@twitterFeed');
            $app->get('/fans', 'SocialController@fanTweets');
        });

        $app->group(['prefix' => 'youtube/'], function ($app) {
            $app->get('/', 'SocialController@youtubeFeed');
        });

        $app->group(['prefix' => 'instagram/'], function ($app) {
            $app->get('/', 'SocialController@instagramFeed');
        });

        $app->group(['prefix' => 'soundcloud/'], function ($app) {
            $app->get('/', 'SocialController@soundcloudFeed');
        });
    });

    $app->group(['prefix' => 'tools/'], function ($app) {
      $app->group(['prefix' => 'analytics/'], function ($app) {
          $app->get('/', 'ToolController@Analytics');
      });
    });

    $app->group(['prefix' => 'guests/'], function ($app) {
        $app->post('/', 'GuestController@createGuest');
        $app->post('/verify', 'GuestController@verifyGuest');
    });

    $app->group(['prefix' => 'settings/'], function ($app) {
        $app->get('/', 'SettingsController@index');
        $app->post('/', 'SettingsController@setSettings');
    });

    $app->group(['prefix' => 'tickets/'], function ($app) {
        $app->get('/', 'TicketsController@index');

        $app->group(['prefix' => 'checkout/'], function ($app) {
            $app->post('/', 'TicketsController@createOrder');
            $app->put('/', 'TicketsController@editOrder');

            $app->group(['prefix' => 'payment/'], function ($app) {
                $app->post('/create', 'TicketsController@createPayment');
                $app->post('/execute', 'TicketsController@executePayment');
            });
        });

    });

});
