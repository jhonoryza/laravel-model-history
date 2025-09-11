<?php

namespace Jhonoryza\ModelHistory\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Jhonoryza\ModelHistory\Factory\History;

trait ModelHistoryTrait
{
    abstract protected static function historyModel(): string;

    protected static function currentUser(): ?Model
    {
        $guards = array_keys(config('auth.guards'));
        foreach ($guards as $guard) {
            if ($user = Auth::guard($guard)->user()) {
                return $user;
            }
        }

        return null;
    }

    public static function bootModelHistoryTrait(): void
    {
        static::created(function ($model) {
            History::make(static::historyModel())
                ->model($model)
                ->changeBy(static::currentUser())
                ->new($model->toArray())
                ->operation(History::INSERT)
                ->log('Created '.class_basename($model));
        });

        static::updated(function ($model) {
            History::make(static::historyModel())
                ->model($model)
                ->changeBy(static::currentUser())
                ->old($model->getOriginal())
                ->new($model->getAttributes())
                ->operation(History::UPDATE)
                ->log('Updated '.class_basename($model));
        });

        static::deleted(function ($model) {
            History::make(static::historyModel())
                ->model($model)
                ->changeBy(static::currentUser())
                ->old($model->getOriginal())
                ->operation(History::DELETE)
                ->log('Deleted '.class_basename($model));
        });

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                History::make(static::historyModel())
                    ->model($model)
                    ->changeBy(static::currentUser())
                    ->new($model->toArray())
                    ->operation(History::RESTORE)
                    ->log('Restored '.class_basename($model));
            });

            static::forceDeleted(function ($model) {
                History::make(static::historyModel())
                    ->model($model)
                    ->changeBy(static::currentUser())
                    ->old($model->getOriginal())
                    ->operation(History::FORCE_DELETE)
                    ->log('Force deleted '.class_basename($model));
            });
        }
    }
}
