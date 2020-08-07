[![Actions Status](https://github.com/Symandy/SymandyDuration/workflows/CI/badge.svg)](https://github.com/Symandy/SymandyDuration/actions)

<h1 align="center">Symandy Duration</h1>

<p align="center">PHP package to represent durations</p>

Installation
------------
- Add package to your project using composer 

    `$ composer require symandy/duration`
    

Usage
-----
- Instantiate `Symandy\Component\Duration\Duration` class

```php
use Symandy\Component\Duration\Duration;

$duration = new Duration('4 minutes 40 seconds');
$duration = new Duration('4m 40s');
```


- Display at any format

```php
use Symandy\Component\Duration\Duration;

$duration = new Duration('4 minutes 40 seconds');

echo $duration->format(); // Default : 0:04:40
echo $duration->format('%h:%m:%s'); // 0:04:40
echo $duration->format('%mm%ss'); // 4m 40s
```


- Add duration changes

```php
use Symandy\Component\Duration\Duration;

$duration = new Duration('4 minutes 40 seconds');
$duration->addMinutes(10);

echo $duration->format('%mm%ss'); // 14m 40s
```
