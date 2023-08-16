<?php

namespace App\Repositories\Base\Interface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface EloquentRepositoryInterface
{
    public function newInstance(array $attributes = []): Model;

    public function newQuery(): Builder;

    public function fill(Model $object, array $attributes, $fillable = []);

    public function create(array $attributes, array $fillable = []): Model;
    public function find(int $id): Model;

    public function update(Model $object, array $attributes, array $fillable = []): Model;

    public function delete(Model $object);

    public function all(): Collection;

    public function paginate(Builder $query): LengthAwarePaginator;

    public function findManyByCriteria(
        $criteria,
        $limit,
        $paginate = [],
        $relations = [],
        $scopes = [],
        $columns = ['*'],
        $sortColumn = 'id',
        $sortDirection = 'desc'
    );

    public function indexByIds(array $ids): Collection;
}
