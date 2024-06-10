<?php

namespace App\Repositories;

use App\Traits\Crudable;
use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\RepositoryException;

abstract class BaseRepository
{
    use Crudable;
    use Queryable;

    public const PAGINATION_PER_PAGE = 25;

    abstract public function getModelClass(): string;

    /**
     * @throws RepositoryException
     */
    public function resolveModel($keyOrModel): Model
    {
        if ($keyOrModel instanceof Model) {
            $modelClass = $this->getModelClass();
            if (!$keyOrModel instanceof $modelClass) {
                throw new RepositoryException("Model is not an entity of repository model class");
            }
            return $keyOrModel;
        }

        $model = resolve($this->getModelClass());

        return $model->findOrFail($keyOrModel);
    }
}
