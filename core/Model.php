<?php

namespace Core;

use RuntimeException;

abstract class Model
{
    protected static string $table;
    protected array $attributes = [];
    protected array $hidden = [];
    protected array $relations = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function __get(string $key)
    {
        if (isset($this->attributes[$key])) {
            return $this->getAttribute($key);
        }

        return $this->getRelationAttribute($key);
    }

    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]) || array_key_exists($key, $this->attributes);
    }


    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    protected function getRelationAttribute(string $key)
    {
        if (method_exists($this, $key)) {
            return $this->getRelationValue($key);
        }

        return null;
    }

    protected function getAttribute(string $key)
    {
        return $this->attributes[$key];
    }

    protected function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    protected function getRelationValue(string $key)
    {
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        $relation = $this->$key();
        return $this->relations[$key] = $relation;
    }

    public static function query(): Database
    {
        return Database::table(static::$table);
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public function save(): bool
    {
        $query = static::query();

        if (isset($this->attributes['id'])) {
            return $query->where('id', $this->attributes['id'])->update($this->attributes);
        }

        $id = $query->insertGetId($this->attributes);
        $this->attributes['id'] = $id;

        return true;
    }

    public function update(array $attributes): bool
    {
        $this->fill($attributes);
        return $this->save();
    }

    public function delete(): bool
    {
        if (isset($this->attributes['id'])) {
            return static::query()->where('id', $this->attributes['id'])->delete();
        }

        return false;
    }

    public static function find(int $id): ?static
    {
        $attributes = static::query()->where('id', $id)->first();
        return $attributes ? new static($attributes) : null;
    }

    public static function findOrFail(int $id): static
    {
        $model = static::find($id);

        if (!$model) {
            throw new RuntimeException("Model not found");
        }

        return $model;
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public function toArray(): array
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->hidden, true)) {
                continue;
            }
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    public function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id'): ?Model
    {
        $instance = new $related();
        return $instance::query()
            ->where($ownerKey, $this->getRelationAttribute($foreignKey))
            ->first();
    }

    public function hasMany(string $related, string $foreignKey, string $localKey = 'id'): array
    {
        $instance = new $related();
        $results = $instance::query()
            ->where($foreignKey, $this->getRelationAttribute($localKey))
            ->get();

        return array_map(static fn($attributes) => new $related($attributes), $results);
    }

    public function hasOne(string $related, string $foreignKey, string $localKey = 'id'): ?Model
    {
        $instance = new $related();
        $attributes = $instance::query()
            ->where($foreignKey, $this->getRelationAttribute($localKey))
            ->first();

        return $attributes ? new $related($attributes) : null;
    }
}
