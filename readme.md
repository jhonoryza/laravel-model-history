# Laravel Model History

This package provides ability to generate `_logs` table from your model.

## Requirement

- PHP 8.1 - 8.4
- Laravel 9, 10, 11, 12

## Getting Started

install

```bash
composer require jhonoryza/laravel-model-history
```

## Usage

let's say you want to create table `category_logs`

we can achieve this with running this command:

```bash
php artisan model-history:generate categories
```

it will generate new migration for table `category_logs` and new model `CategoryLog`

then you can add to the log like this :

```php
<?php

use Jhonoryza\LaravelModelHistory\Factory\History;
use App\Models\CategoryLog;

History::make(CategoryLog::class)
    ->model($category)                      // required
    ->changeBy(auth()->user())              // optional
    ->old($oldData)                         // optional
    ->new($newData)                         // optional
    ->operation(History::UPDATE)            // required
    ->log('Category updated successfully'); // required
```

### Detect From Model Event

you can make adding log automatically by detecting from model event

like `created, updated, deleted, restored and force deleted`

by adding trait `ModelHistoryTrait` to your model, then it will create log entry automatically

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jhonoryza\LaravelModelHistory\Traits\ModelHistoryTrait;

class Category extends Model
{
    use ModelHistoryTrait;

    protected static string $logModel = CategoryLog::class;
}
```

## Security

If you've found a bug regarding security, please mail [jardik.oryza@gmail.com](mailto:jardik.oryza@gmail.com) instead of
using the issue tracker.

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
