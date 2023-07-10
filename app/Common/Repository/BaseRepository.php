<?php


namespace App\Common\Repository;

use App\Common\Repository\Interface\EloquentRepositoryInterface;
use App\Exceptions\Repository\DeleteModelException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @template TModel
 * @template-extends \App\Common\Repository\Interface\EloquentRepositoryInterface
 * @implements \App\Common\Repository\Interface\EloquentRepositoryInterface<TModel>
 */
class BaseRepository implements EloquentRepositoryInterface
{
    public string $model;

    public array $fillable = [];

    /**
     * @throws BindingResolutionException
     */
    public function newInstance(array $attributes = []): Model
    {
        return app()->make($this->model, $attributes);
    }

    public function fill(Model $object, array $attributes, $fillable = []): Model
    {
        if (!empty($fillable)) {
            $object->fillable($fillable);
        } else {
            $object->fillable($this->fillable);
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
}
