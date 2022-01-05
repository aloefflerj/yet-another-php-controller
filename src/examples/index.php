<?php

declare(strict_types=1);

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Aloefflerj\YetAnotherController\Controller\BaseController;
use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;
use Aloefflerj\YetAnotherController\Test\UserClass;

// $fp = fopen('php://output', 'w');
// fwrite($fp, 'Hello World');
// fclose($fp);

// header('Content-Type: application/json');

$users = [
    [
        'id' => 1,
        'name' => 'anderson',
        'pet' => 'rex'
    ],
    [
        'id' => 2,
        'name' => 'juarez',
        'pet' => 'fluffy'
    ]
];

$app = new BaseController();

$app->get('/', function ($req, $res, $params) {
    echo json_encode("Bem vindo, {$params->name}", JSON_PRETTY_PRINT);
}, ['name' => 'Anderson']);

$app->get('/users', function ($req, $res, $params) {
    
    echo json_encode($params->users, JSON_PRETTY_PRINT);

}, ['users' => $users]);

$app->get('/users/{id}', function($req, $res, $params) {

    $users = [
        [
            'id' => 1,
            'name' => 'anderson',
            'pet' => 'rex'
        ],
        [
            'id' => 2,
            'name' => 'juarez',
            'pet' => 'fluffy'
        ]
    ];
    
    foreach($users as $user) {
        if($user['id'] === (int)$params->id) {
            echo json_encode($user, JSON_PRETTY_PRINT);
        }
    }

});

$app->post('/users', function ($req, $res, $body, $params) {

    echo $body;
    
});

$app->get('/test', function($req, $res, $params) {
    
    $stream = new Stream(fopen('./resource.txt', 'r'));

    $stream->write('Hello');

    $message = new Message();
    
    $message = $message->withBody($stream);

    echo $message->getBody()->getContents();
});

$app->dispatch();

if ($app->error()) {
    echo $app->error()->getMessage();
}
