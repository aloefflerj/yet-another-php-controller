# Yet Another PHP Controller 
## Here is how it works (for now):

### Installation
`composer require aloefflerj/yet-another-controller`

### Quick Usage

1. Simple Setup

    > index.php
    ```php
    <?php
    
    declare(strict_types=1);
    
    use Aloefflerj\YetAnotherController\Controller\Controller;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    
    require_once '/vendor/autoload.php';
    
    $controller = new Controller('http://localhost:8000');
    ```

2. Adding a simple **GET** route
    > index.php
    ```php
    $controller->get('/', function (RequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write('Welcome home');
        return $response->getBody();
    });
    ```
    
3. Dispatching routes
   > index.php
   ```php
    $controller->dispatch();
   ```
    
4. Start your webserver

    _A simple way to do it would be with the [PHP built-in web server](https://www.php.net/manual/en/features.commandline.webserver.php)_:
   
   Run `php -S localhost:8000 -t path/to/your/index`

6. Go to your domain home page `http://localhost:8000/`
    > outputs: 'Welcome home'

----------------------------------

### Request and Response

The `$request` and `$response` arguments on `closure` _output_ are [PSR-7](https://www.php-fig.org/psr/psr-7/) implementations.

The return of the `closure` must be a `StreamInterface` implementation for the output to work.

```php
$controller->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write('Welcome home');
    return $response->getBody();
});
```

Since the return of `$response->getBody()` is a `StreamInterface` implementation the code above will print:
 > outputs: 'Welcome home'

Alternatively you could simply do `echo $response->getBody();`

----------------------------------

### Uri Params Usage

You can pass uri params wiht the syntax `/route/{param}`.

For the `param` to be accessed, you need a third param on `get` method: `\stdClass $args`.

To access it inside the `closure` _output_, you must use `$args->param`. See the example below:

```php
$controller->get('/mascots/{mascot}', function (RequestInterface $request, ResponseInterface $response, \stdClass $args) {
    $response->getBody()->write("PHP {$args->mascot} mascot is awesome");
    return $response->getBody();
});
```
url: `localhost:8000/mascots/elephant`
> outputs: 'PHP elephant mascot is awesome'

----------------------------------

### Getting the Request Body
To get the body from a request you can access it from the `$request` implementation with `$request->getBody()` (_a `StreamInterface` implementation_).

The example below will print the request body.

```php
$controller->put('/mascot', function (RequestInterface $request, ResponseInterface $response) {
    $requestBody = $request->getBody();
    return $requestBody;
});
```
If I request a **POST** with the following body:
```json
{
"mascots": [
    {
        "lang": "php",
        "mascot": "elephant"
    },
    {
        "lang": "go",
        "mascot": "gopher"
    }
]
}
```

The return should be the same:
```json
{
"mascots": [
    {
        "lang": "php",
        "mascot": "elephant"
    },
    {
        "lang": "go",
        "mascot": "gopher"
    }
]
}
```

----------------------------------------

###### Obs: although you could try this controller out if you want, this project is just an excuse for me to study and have fun :v
