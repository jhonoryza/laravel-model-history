<?php

namespace Jhonoryza\ModelHistory\Traits;

use Illuminate\Support\Facades\Auth;
use Jhonoryza\ModelHistory\Factory\History;

trait ModelHistoryTrait
{
    abstract protected static function logModel(): string;

    public static function bootModelHistoryTrait(): void
    {
        static::created(function ($model) {
            History::make(static::logModel())
                ->model($model)
                ->changeBy(Auth::id() ?? null)
                ->new($model->toArray())
                ->operation(History::INSERT)
                ->log('Created '.class_basename($model));
        });

        static::updated(function ($model) {
            History::make(static::logModel())
                ->model($model)
                ->changeBy(Auth::id() ?? null)
                ->old($model->getOriginal())
                ->new($model->getAttributes())
                ->operation(History::UPDATE)
                ->log('Updated '.class_basename($model));
        });

        static::deleted(function ($model) {
            History::make(static::logModel())
                ->model($model)
                ->changeBy(Auth::id() ?? null)
                ->old($model->getOriginal())
                ->operation(History::DELETE)
                ->log('Deleted '.class_basename($model));
        });

        static::restored(function ($model) {
            History::make(static::logModel())
                ->model($model)
                ->changeBy(Auth::id() ?? null)
                ->new($model->toArray())
                ->operation(History::RESTORE)
                ->log('Restored '.class_basename($model));
        });

        static::forceDeleted(function ($model) {
            History::make(static::logModel())
                ->model($model)
                ->changeBy(Auth::id() ?? null)
                ->old($model->getOriginal())
                ->operation(History::FORCE_DELETE)
                ->log('Force deleted '.class_basename($model));
        });
    }
}
