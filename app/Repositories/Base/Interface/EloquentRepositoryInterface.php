<?php

namespace App\Repositories\Base\Interface;

use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    public function newInstance(array $attributes = []): Model;

    public function fill(Model $object, array $attributes, $fillable = []);

    public function create(array $attributes, array $fillable = []): Model;

    public function update(Model $object, array $attributes, array $fillable = []): Model;

    public function delete(Model $object);
}
