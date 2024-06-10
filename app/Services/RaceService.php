<?php

namespace App\Services;

use App\Repositories\RaceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RaceService
{
    private RaceRepository $raceRepository;

    public function __construct(RaceRepository $raceRepository)
    {
        $this->raceRepository = $raceRepository;
    }

    public function getAllPaginated($filters = []): LengthAwarePaginator
    {
        return $this->raceRepository->getAllPaginated($filters);
    }

    public function create($data): ?Model
    {
        $created_race = null;

        DB::transaction(function () use ($data, &$created_race) {
            $created_race = $this->raceRepository->create($data);
        });

        return $created_race;
    }

    public function update($id, $data): ?Model
    {
        $updated_race = null;

        DB::transaction(function () use ($id, $data, &$updated_race) {
            $updated_race = $this->raceRepository->update($id, $data);
        });

        return $updated_race;
    }

    public function delete($id): bool
    {
        return $this->raceRepository->delete($id);
    }
}
