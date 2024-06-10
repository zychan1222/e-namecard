<?php

namespace App\Traits;

use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Crudable
 * Putting all CRUD methods in this trait
 */
trait Crudable
{
    public function create(array $data): ?Model
    {
        $model = resolve($this->getModelClass());

        $model = $model->create($data);

        if(!$model) {
            return null;
        }

        return $model;
    }

    /**
     * @throws RepositoryException
     */
    public function update($keyOrModel, array $data): ?Model
    {
        $model = $this->resolveModel($keyOrModel);

        $update_status = $model->update($data);
        if(!$update_status) {
            return null;
        }

        return $model->refresh();
    }

    /**
     * @throws RepositoryException
     */
    public function delete($keyOrModel): bool
    {
        $model = $this->resolveModel($keyOrModel);

        return $model->delete();
    }

    /**
     * @throws RepositoryException
     */
    public function forceDelete($keyOrModel): bool
    {
        $model = $this->resolveModel($keyOrModel);

        return $model->forceDelete();
    }

    public function updateOrCreateFirstModel(array $data): Model
    {
        $model = resolve($this->getModelClass());

        $existing_model = $model->first();

        if($existing_model) {
            return $this->update($existing_model, $data);
        } else {
            return $this->create($data);
        }
    }

    public function updateOrCreate(array $attributes, array $values = []): ?Model
    {
        $model = resolve($this->getModelClass());

        return $model->updateOrCreate($attributes, $values);
    }
}
