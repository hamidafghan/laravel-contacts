<?php

namespace Jenishev\Laravel\Contacts\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records the authenticated user that created or last updated a model.
 */
trait Blameable
{
    public static function bootBlameable(): void
    {
        static::creating(function (self $model): void {
            $model->setBlameableAttribute('createdBy');
        });

        static::saving(function (self $model): void {
            $model->setBlameableAttribute('updatedBy');
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo($this->blameableUserModel(), $this->blameableAttribute('createdBy'));
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo($this->blameableUserModel(), $this->blameableAttribute('updatedBy'));
    }

    public function scopeCreatedBy(Builder $query, int|string|Model $user): Builder
    {
        return $this->scopeByBlameableAttribute($query, $user, 'createdBy');
    }

    public function scopeUpdatedBy(Builder $query, int|string|Model $user): Builder
    {
        return $this->scopeByBlameableAttribute($query, $user, 'updatedBy');
    }

    protected function scopeByBlameableAttribute(Builder $query, int|string|Model $user, string $key): Builder
    {
        $userId = $user instanceof Model ? $user->getKey() : $user;

        return $query->where($this->qualifyColumn($this->blameableAttribute($key)), $userId);
    }

    protected function setBlameableAttribute(string $key): void
    {
        $user = auth()->user();
        $userModel = $this->blameableUserModel();

        if ($user instanceof $userModel && is_int($user->getKey())) {
            $this->setAttribute($this->blameableAttribute($key), $user->getKey());
        }
    }

    protected function blameableAttribute(string $key): string
    {
        return $this->blameable()[$key];
    }

    protected function blameableUserModel(): string
    {
        return $this->blameable()['user'];
    }
}
