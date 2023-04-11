# Bookworm For Laravel (by shtayeb)

Bookworm estimates how much time is needed to read a certain piece of text.

## Installation

```shell
composer require shtayeb/bookworm
```

## Publish the config file

```shell
php artisan vendor:publish --provider="SHTayeb\Bookworm\BookwormServiceProvider"
```

## Usage

```php
<?php
use SHTayeb\Bookworm\Bookworm;

$text = '...';
$time = (new Bookworm())->estimate($text);
echo $time; // 5 minutes

$word_count = (new Bookworm())->countWords($text);
echo $word_count; // 1,000
```

## API

```php
(new Bookworm())->estimate(string $text, string|array|bool $units = [ ' minute', ' minutes' ]);
```

**Parameters**

- `$text` The piece of text which the estimation should be based upon.
- `$units = [ ' minute', ' minutes' ]` _Optional._ Set it false, to return just the number of minutes as an integer. If you provide a string, like `m` it will be used for singular and plural and produce `5m`. If you provide an array with two values, the first will be used for singular, the second for plural. `[ ' minute', ' minutes' ]` (not included leading whitespace) will produce `5 minutes`.

**Returns** `int` or `string`

## Configuration

You can configure Bookworm to react other than how it's shipped. You can change the average words per minute & the duration a user needs to look at an image. If you do not want images to factor into the reading time estimate, just set it to 0.

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Bookworm Options
    |--------------------------------------------------------------------------
    | Here you may specify the configuration options that should be used
    |
    */

    'words_per_minute' =>200,
    'codewords_per_minute' => 200,
    'seconds_per_image' => 12

];
```

**wordsPerMinute** The average amount of words a user will read per minute (_default 200_).

**codewordsPerMinute** The average amount of words in a code block, a user will read per minute (_default 200_).

**secondsPerImage** The average amount of seconds a user will spend looking at an image (_default 12_).

## License

This project is licensed under MIT license. For the full copyright and license information, please view the LICENSE file
that was distributed with this source code.

## Contributing

You may contribute in any way you want, as long as you agree that your code will be licensed under the same license as
the project itself.

Please make sure to run the tests before committing.

```bash
$ composer test
```
