<?php

namespace App\Http\Controllers\Api\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MasterData\RaceCreateRequest;
use App\Http\Requests\Api\MasterData\RaceIndexRequest;
use App\Http\Requests\Api\MasterData\RaceUpdateRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\RaceResource;
use App\Models\Race;
use App\Services\RaceService;
use Illuminate\Http\JsonResponse;

class RaceController extends Controller
{
    protected RaceService $raceService;

    public function __construct(RaceService $race_service)
    {
        $this->raceService = $race_service;
    }

    public function index(RaceIndexRequest $request): JsonResponse
    {
        $input = $request->validated();

        $data = $this->raceService->getAllPaginatedRaces($input);

        return (new ApiResponse())
            ->setMessage(__('api.common.success'))
            ->setCode(200)
            ->setData(RaceResource::collection($data))
            ->setPagination($data)
            ->getResponse();
    }

    public function show(Race $master_race): JsonResponse
    {
        return (new ApiResponse())
            ->setMessage(__('api.common.success'))
            ->setCode(200)
            ->setData(new RaceResource($master_race))
            ->getResponse();
    }

    public function create(RaceCreateRequest $request): JsonResponse
    {
        $input = $request->validated();

        $master_race = $this->raceService->createRace($input);

        return (new ApiResponse())
            ->setMessage(__('api.common.success'))
            ->setCode(200)
            ->setData(new RaceResource($master_race))
            ->getResponse();
    }

    public function update(Race $master_race, RaceUpdateRequest $request): JsonResponse
    {
        $input = $request->validated();

        $master_race = $this->raceService->updateRace($master_race, $input);

        return (new ApiResponse())
            ->setMessage(__('api.common.success'))
            ->setCode(200)
            ->setData(new RaceResource($master_race))
            ->getResponse();
    }

    public function destroy(Race $master_race): JsonResponse
    {
        $this->raceService->deleteRace($master_race);

        return (new ApiResponse())
            ->setMessage(__('api.common.success'))
            ->setCode(200)
            ->getResponse();
    }
}
