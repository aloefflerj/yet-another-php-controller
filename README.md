# Yet Another PHP Controller 🐘🚦
## Here is how it works (for now):
1. Try the code below
```php
    $app = new BaseController();
    $app->get('/', function ($req, $res, $headerParams, $functionParams) {
        echo 'Welcome home';
    });
```
2. Go to your domain home page (example: `localhost:8000/`)
> outputs: 'Welcome home'

----------------------------------

**You can pass header parameters...**
```php
    $app->get('mascots/{mascot}', function ($req, $res, $headerParams, $functionParams) {
        echo "PHP {$headerParams->mascot} mascot is awesome";
    });
```
url: `localhost:8000/mascots/elephant`
> outputs: 'PHP elephant mascot is awesome'

**And function params**
```php
    $app->get('/mascots', function ($req, $res, $headerParams, $functionParams) {
        echo "PHP {$functionParams->mascot} mascot is awesome";
    }, ['mascot' => 'elephant']);
```
url: `localhost:8000/elephant`
> outputs: 'PHP elephant mascot is awesome'
--------------------------------------
`$req` and `$res` are waiting for the implementation of psr7, you can just ignore them for now. So in order to get the body on `post`, `put` and `delete` verbs, you must do:
```php
    $app->post('/users', function ($req, $res, $body, $headerParams, $functionParams) {
        print_r(json_decode($body));
    });
```
Sending a body like this:
```json
    "mascots": [
        {
            "id": 1,
            "language": "php",
            "mascot": "elephant"
        },
        {
            "id": 2,
            "language": "go",
            "mascot": "gopher"
        }
    ]
```
> outputs: stdClass Object ( [mascots] => Array ( [0] => stdClass Object ( [id] => 1 [language] => php [mascot] => elephant ) [1] => stdClass Object ( [id] => 2 [language] => go [mascot] => gopher ) ) )

###### Obs: the whole purpose of this repo is just for me to study and have fun. But you can try it if you want :v
