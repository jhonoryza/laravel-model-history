# Laravel Model History

[![Latest Stable Version](http://poser.pugx.org/jhonoryza/laravel-model-history/v)](https://packagist.org/packages/jhonoryza/laravel-model-history)
[![Total Downloads](http://poser.pugx.org/jhonoryza/laravel-model-history/downloads)](https://packagist.org/packages/jhonoryza/laravel-model-history)
[![Latest Unstable Version](http://poser.pugx.org/jhonoryza/laravel-model-history/v/unstable)](https://packagist.org/packages/jhonoryza/laravel-model-history)
[![License](http://poser.pugx.org/jhonoryza/laravel-model-history/license)](https://packagist.org/packages/jhonoryza/laravel-model-history)
[![PHP Version Require](http://poser.pugx.org/jhonoryza/laravel-model-history/require/php)](https://packagist.org/packages/jhonoryza/laravel-model-history)

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

it will generate new migration for table `category_logs` and new model
`CategoryLog`

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

to get all the logs

```php
<?php

return History::make(CategoryLog::class)
    ->getLogs($category->id);
```

### Detect From Model Event

you can make adding log automatically by detecting from model event

like `created, updated, deleted, restored and force deleted`

by adding trait `ModelHistoryTrait` to your model, then it will create log entry
automatically

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jhonoryza\LaravelModelHistory\Traits\ModelHistoryTrait;

class Category extends Model
{
    use ModelHistoryTrait;

    protected static function historyModel(): string
    {
        return CategoryLog::class;
    }
}
```

### Laravel Eloquent Events Reference

Available events :

- retrieved
- creating / created
- updating / updated
- saving / saved
- deleting / deleted
- restoring / restored
- forceDeleted

#### Create & Update Operations

| Function                                           | Fires Events? | Events Triggered                         | Notes                           |
| -------------------------------------------------- | ------------- | ---------------------------------------- | ------------------------------- |
| `Model::create([...])`                             | ✅ Yes        | `creating`, `created`, `saving`, `saved` | Uses model instance             |
| `Model::query()->create([...])`                    | ✅ Yes        | Same as above                            | Proxy to `create()`             |
| `new Model([...])->save()`                         | ✅ Yes        | Same as `create()`                       | Instance save                   |
| `$model->save()` (new)                             | ✅ Yes        | `creating`, `created`, `saving`, `saved` | Insert                          |
| `$model->save()` (existing + dirty)                | ✅ Yes        | `updating`, `updated`, `saving`, `saved` | Update                          |
| `$model->save()` (existing + not dirty)            | ❌ No         | —                                        | No SQL → no events              |
| `$model->saveQuietly()`                            | ❌ No         | —                                        | Saves silently                  |
| `$model->update([...])`                            | ✅ Yes        | `updating`, `updated`, `saving`, `saved` | Instance update                 |
| `Model::query()->update([...])`                    | ❌ No         | —                                        | Bulk update, direct SQL         |
| `DB::table('table')->update([...])`                | ❌ No         | —                                        | Query Builder                   |
| `$model->updateQuietly([...])`                     | ❌ No         | —                                        | Silent update                   |
| `DB::table('table')->insert([...])`                | ❌ No         | —                                        | Pure Query Builder              |
| `DB::table('table')->insertGetId([...])`           | ❌ No         | —                                        | Pure Query Builder              |
| `Model::insert([...])` (mass insert array of rows) | ❌ No         | —                                        | Static `insert`, bypasses model |

#### Delete & Soft Delete

| Function                         | Fires Events? | Events Triggered           | Notes                                 |
| -------------------------------- | ------------- | -------------------------- | ------------------------------------- |
| `$model->delete()`               | ✅ Yes        | `deleting`, `deleted`      | SoftDeletes → soft delete             |
| `$model->forceDelete()`          | ✅ Yes        | `deleting`, `forceDeleted` | Permanent                             |
| `Model::destroy([1,2])`          | ✅ Yes        | Fires per model instance   |                                       |
| `Model::query()->delete()`       | ❌ No         | —                          | Bulk delete                           |
| `Model::query()->forceDelete()`  | ❌ No         | —                          | Bulk permanent delete                 |
| `DB::table('table')->delete()`   | ❌ No         | —                          | Query Builder                         |
| `DB::table('table')->truncate()` | ❌ No         | —                          | Direct truncate, no events            |
| `Model::truncate()`              | ❌ No         | —                          | Shortcut ke `DB::table()->truncate()` |

#### Restore (SoftDeletes only)

| Function                    | Fires Events? | Events Triggered        | Notes                    |
| --------------------------- | ------------- | ----------------------- | ------------------------ |
| `$model->restore()`         | ✅ Yes        | `restoring`, `restored` | Restores model           |
| `Model::query()->restore()` | ❌ No         | —                       | Bulk restore, direct SQL |

#### Retrieval

| Function                    | Fires Events? | Events Triggered        | Notes                       |
| --------------------------- | ------------- | ----------------------- | --------------------------- |
| `Model::find(1)`            | ✅ Yes        | `retrieved`             | Fires after hydrating model |
| `Model::all()`              | ✅ Yes        | `retrieved` (per model) | For each row                |
| `Model::query()->get()`     | ✅ Yes        | `retrieved` (per model) | Same as above               |
| `DB::table('table')->get()` | ❌ No         | —                       | Query Builder only          |

#### Other Methods

| Function                   | Fires Events? | Events Triggered                                                 | Notes                           |
| -------------------------- | ------------- | ---------------------------------------------------------------- | ------------------------------- |
| `$model->push()`           | ⚠️ Partial    | Parent: same as `save()`; Relations: saved directly, no events   |                                 |
| `$model->touch()`          | ✅ Yes        | `updating`, `updated`, `saving`, `saved`                         | Updates timestamps              |
| `$model->refresh()`        | ❌ No         | —                                                                | Reload from DB                  |
| `$model->replicate()`      | ❌ No         | —                                                                | Just copies attributes, no save |
| `$model->firstOrCreate()`  | ✅ Yes        | Either `retrieved` OR (`creating`, `created`, `saving`, `saved`) |                                 |
| `$model->updateOrCreate()` | ✅ Yes        | Either (`updating`, `updated`) OR (`creating`, `created`)        |                                 |

## Security

If you've found a bug regarding security, please mail
[jardik.oryza@gmail.com](mailto:jardik.oryza@gmail.com) instead of using the
issue tracker.

## License

The MIT License (MIT). Please see [License File](license.md) for more
information.
