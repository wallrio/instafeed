# instaFeed
captures instagram user posts


## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Directly.

```bash
$ composer require wallrio/instafeed "*"
```


## Usage

### Capture data from account
To capture information and publications from a user use the Profile class with the get

- $profile->username(USERNAME_OF_USER);
- $profile->get(CALLBACK_OPTIONAL);

#### Example

```
	use InstaFeed\Profile as Profile;
	$profile = new Profile();

	$profile->username('wallace.rio');
	$instaData = $profile->get();

	print_r($instaData);

```



### Use cache

- $profile->useCache = true;
- $profile->cache->dir(DIRECTORY_OF_CACHE);  (optional)

if method '$profile->cache->dir' is omitted, the directory will be the temporary directory (/tmp/instaFeed).


```
use InstaFeed\Profile as Profile;
$profile = new Profile();

$profile->useCache = true;
$profile->cache->dir(__DIR__DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);

$profile->username('wallace.rio');
$instaData = $profile->get();

```


### run callback if instagram feed has updated

```
use InstaFeed\Profile as Profile;
$profile = new Profile();

$profile->useCache = true;

$profile->username('wallace.rio');
$instaData = $profile->get(function(){
	// code to run if instagram feed has updated
});

```




### Clean cache

- $profile->cache->clear(TIME_TO_CLEAN);

- TIME_TO_CLEAN: is optional and allows as string or integer value.
	- integer: the integer will be defined per second.
	- string: enter a number and a letter referring to the specification of days, hours, minutes, and seconds.

#### Example of time:
```
	$profile->cache->clear('30m');		// 30 minutes
	$profile->cache->clear('2d');		// 2 days
	$profile->cache->clear('5h');		// 5 hours

```

#### Example implementation:
```
use InstaFeed\Profile as Profile;
$profile = new Profile();

$profile->useCache = true;
$profile->cache->dir(__DIR__DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
$profile->cache->clear();

$profile->username('wallace.rio');
$instaData = $profile->get();

```



## License

The instaFeed is licensed under the MIT license. See [License File](LICENSE) for more information.