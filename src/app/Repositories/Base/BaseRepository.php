<?php


namespace App\Repositories\Base;

use App\Exceptions\Repository\DeleteModelException;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

/**
 * @template TModel
 * @template-extends EloquentRepositoryInterface
 * @implements EloquentRepositoryInterface<TModel>
 */
class BaseRepository implements EloquentRepositoryInterface
{
    public string $model;
    public const DEFAULT_SORT_COLUMN = 'id';
    public const DEFAULT_SORT_COLUMN_DIRECTION = 'desc';

    /**
     * @throws BindingResolutionException
     */
    public function newInstance(array $attributes = []): Model
    {
        return app()->make($this->model, $attributes);
    }

    /**
     * @throws BindingResolutionException
     */
    public function newQuery(): Builder
    {
        return $this->newInstance()->newQuery();
    }

    public function fill(Model $object, array $attributes, $fillable = []): Model
    {
        if (!empty($fillable)) {
            $object->fillable($fillable);
        } else {
            $object->fillable($object->getFillable());
        }

        return $object->fill($attributes);
    }

    /**
     * @throws BindingResolutionException
     */
    public function create(array $attributes, array $fillable = []): Model
    {
        $object = $this->newInstance($attributes);
        $this->fill($object, $attributes, $fillable)->save();

        return $object;
    }

    /**
     * @throws BindingResolutionException
     */
    public function find(int $id): ?Model
    {
        return self::newQuery()->find($id);
    }

    public function update(Model $object, array $attributes, array $fillable = []): Model
    {
        $this->fill($object, $attributes, $fillable)->save();

        return $object;
    }

    /**
     * @throws DeleteModelException
     */
    public function delete(Model $object): ?bool
    {
        try {
            return $object->delete();
        } catch (Throwable $exception) {
            throw DeleteModelException::throw($exception)->params($object::class, $object->getKey());
        }
    }

    /**
     * @throws BindingResolutionException
     */
    public function all(): Collection
    {
        return $this->newQuery()->get();
    }


    public function index(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();
        if (isset($data['export']) && $data['export']) {
            return self::sortQuery($query)->get();
        }

        return self::paginate($query);
    }

    public function paginate(Builder $query): LengthAwarePaginator
    {
        return self::sortQuery($query)->paginate(
            get_paginate_params()['per_page']
        );
    }

    public function sortQuery(Builder $query): Builder
    {
        $query->orderBy(
            request('sort', self::DEFAULT_SORT_COLUMN),
            request('sort_direction', self::DEFAULT_SORT_COLUMN_DIRECTION),
        );

        return $query;
    }

    public function indexByIds(array $ids): Collection
    {
        return self::newQuery()
            ->whereIn('id', $ids)
            ->get();
    }

    public function insert(array $data): bool
    {
        return self::newQuery()
            ->insert($data);
    }

    /**
     * @param string $column
     * @param array $criteria
     * @param array $scopes
     * @return int|float
     */
    public function sum(string $column, array $criteria = [], array $scopes = []): float|int
    {
        return self::newQuery()
            ->where($criteria)
            ->scopes($scopes)
            ->sum($column);
    }
}
