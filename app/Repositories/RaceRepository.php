<?php

namespace App\Repositories;

use App\Models\Race;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RaceRepository extends BaseRepository
{
    public function getModelClass(): string {
        return Race::class;
    }

    public function getAll(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    public function getAllPaginated(array $filters = []): LengthAwarePaginator
    {
        return $this
            ->getQuery($filters)
            ->when(!empty($filters['name']), function (Builder $query) use ($filters) {
                $query->where($this->getTranslatableKey('name'), 'LIKE', "%" . $filters['name'] . "%");
            })
            ->paginate($this->per_page);
    }
}
