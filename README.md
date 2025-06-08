# File Hooker for PHP

> A simple Hooking System based on files.
> This means that each callback is located in a PHP file.

## 🫡 Usage

### 🚀 Installation

You can install the package via composer:

```bash
composer require nabeghe/file-hooker
```

### 📁 Hooks Directory

Create a directory for your hooks.

The name of each php file in this directory without suffix (.php) will be the name of your hook.
Subdirectories are also allowed.

Each file returns a callback. This callback receives two arguments, data and angler.
Data is the items sent to the callback. In filters, it must be an array, but in actions, it can be anything.
Index 0 of data in filters is what is filtered. But in callbacks, the array itself must be returned.

### Example

```php
<?php

use Nabeghe\FileHooker\FileHooker;

// This is a custom object called angler. It is sent as the second argument to the callbacks.
$angler = new stdClass();
$hooker = new FileHooker($angler);

// Add a new path where the hooks are located.
$hooker->add(__DIR__.'/hooks');

// Action
$hooker->action('print', ['text' => 'Hi']);

// Filter
$result = $hooker->filter('remove_spaces', ['Hadi Akbarzadeh']);
echo $result;
```

Create a file named `print.php` in the hooks directory:

```php
<?php

return function ($data, $angler) {
    echo $data['text'];
};
```

Create a file named `remove_spaces.php` in the hooks directory:

```php
<?php

return function ($data, $angler) {
    $data = str_replace(' ', '', $data[0]);
    return $data;
};
```

## 📖 License

Copyright (c) 2024 Hadi Akbarzadeh

Licensed under the MIT license, see [LICENSE.md](LICENSE.md) for details.