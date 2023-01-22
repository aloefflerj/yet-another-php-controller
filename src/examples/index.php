<?php

declare(strict_types=1);


ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Aloefflerj\YetAnotherController\Controller\BaseController;
use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
// use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;
// use Aloefflerj\YetAnotherController\Test\UserClass;

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

$app->get('/', function ($req, $res, $headerParams, $functionParams) {
    echo json_encode("Bem vindo, {$functionParams->name}", JSON_PRETTY_PRINT);
}, ['name' => 'Anderson']);

$app->get('/users', function ($req, $res, $headerParams, $functionParams) {

    echo json_encode($functionParams->users, JSON_PRETTY_PRINT);
}, ['users' => $users]);

$app->get('/users/{id}', function ($req, $res, $headerParams, $functionParams) {
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

    foreach ($users as $user) {
        if ($user['id'] === (int)$headerParams->id) {
            echo json_encode($user, JSON_PRETTY_PRINT);
        }
    }
});

$app->post('/users', function ($req, $res, $body, $headerParams, $functionParams) {

    echo json_encode([
        'method' => 'post',
        'body' => json_decode($body),
        'functionParams' => $functionParams->value
    ], JSON_PRETTY_PRINT);
}, ['value' => 'functionParams']);

$app->put('/users/{id}', function ($req, $res, $body, $headerParams, $functionParams) {

    echo json_encode([
        'method' => 'put',
        'body' => json_decode($body),
        'headerParamsId' => $headerParams->id,
        'functionParams' => $functionParams->value
    ], JSON_PRETTY_PRINT);
}, ['value' => 'functionParams']);

$app->delete('/users/{id}', function ($req, $res, $body, $headerParams, $functionParams) {

    echo json_encode([
        'method' => 'delete',
        'body' => json_decode($body),
        'headerParamsId' => $headerParams->id,
        'functionParams' => $functionParams->value
    ], JSON_PRETTY_PRINT);
}, ['value' => 'functionParams']);

$app->get('/test', function ($req, $res, $params) {

    $stream = new Stream(fopen('./resource.txt', 'r'));

    $stream->write('Hello');

    $message = new Message();

    $message = $message->withBody($stream);

    echo $message->getBody()->getContents();
});

$app->get('/params/{param}', function ($req, $res, $headerParams, $functionParams) {
    echo "url params => $headerParams->param";
    echo "<br>";
    echo "function params => $functionParams->param";
}, ['param' => 'esse é um function param']);

$app->get('/http', function ($req, $res) {
    echo 'http home';
});

$app->get('/http/message', function ($req, $res) {
    $message = new Message();
    echo "getProtocolVersion() =>  {$message->getProtocolVersion()}<br>";

    $message = $message->withProtocolVersion('2.0');
    echo "withProtocolVerion() =>  {$message->getProtocolVersion()}<br>";

    $message = $message->withHeader('Content-Type', 'application/json');
    $hasHeader = $message->hasHeader('content-type') === true ? 1 : 0;
    echo "hasHeader() => {$hasHeader}";

    dump($message->getHeaders());
});

$app->get('/http/uri', function ($req, $res) {

    $uri = new Uri('http://localhost:80/users/1');
    $uriUser = new Uri('http://aloefflerj:12345@github.com/repositories');
    $uriQuery = new Uri('https://www.teste.com/users?p=113&foo=bar#yes-i-do');
    printLine("<h3>For these two cases</h3>");
    $cases = <<<CASES
        <li>http://localhost:80/users/1</li>
        <li>http://aloefflerj:12345@github.com/repositories</li>
        <li>https://www.teste.com/users?p=113&foo=bar#yes-i-do</li>
    CASES;

    printLine($cases);
    printLine("Here is the output:");
    printLine('');

    printLine("__to_string() => {$uri} - {$uriUser} - {$uriQuery}");
    printLine("getScheme() => {$uri->getScheme()} - {$uriUser->getScheme()} - {$uriQuery->getScheme()}");
    printLine("getAuthority() => {$uri->getAuthority()} - {$uriUser->getAuthority()} - {$uriQuery->getAuthority()}");

    printLine("getUserInfo() => {$uri->getUserInfo()} - {$uriUser->getUserInfo()} - {$uriQuery->getUserInfo()}");

    printLine("getHost() => {$uri->getHost()} - {$uriUser->getHost()} - {$uriQuery->getHost()}");

    printLine("getPort() => {$uri->getPort()} - {$uriUser->getPort()} - {$uriQuery->getPort()}");

    printLine("getPath() => {$uri->getPath()} - {$uriUser->getPath()} - {$uriQuery->getPath()}");

    printLine("getQuery() => {$uri->getQuery()} - {$uriUser->getQuery()} - {$uriQuery->getQuery()}");

    printLine("getFragment() => {$uri->getFragment()} - {$uriUser->getFragment()} - {$uriQuery->getFragment()}");

    $uriClone = $uri->withScheme('https');
    echo $uriClone->getScheme();
});

$app->dispatch();

function printLine($string)
{
    if (defined('STDIN')) {
        echo $string . \PHP_EOL;
        return;
    }

    echo $string, "<br>";
}

function dump($dump)
{
    echo '<pre>', var_dump($dump), '</pre>';
}
