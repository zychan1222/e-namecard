<?php

use App\Models\Race;
use App\Repositories\RaceRepository;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->masterRaceRepository = app(RaceRepository::class);

    app()->setLocale('en');

    $this->locale = app()->getLocale();

    $this->race_table_name = resolve(Race::class)->getTable();
});

test('getModelClass()', function () {
    $response = $this->masterRaceRepository->getModelClass();

    expect($response)->toEqual(Race::class);
});

test('getAll()', function () {
    $first_race = Race::factory()->create([
        'name' => 'English'
    ]);

    $second_race = Race::factory()->create([
        'name' => 'English 2'
    ]);

    $third_race = Race::factory()->create([
        'name' => 'Tamil'
    ]);

    $response = $this->masterRaceRepository->getAll()->toArray();

    expect($response)->toEqual([
        $first_race->toArray(),
        $second_race->toArray(),
        $third_race->toArray(),
    ]);
});

test('getAllPaginated()', function () {
    $first_race = Race::factory()->create([
        'name' => 'English'
    ]);

    $second_race = Race::factory()->create([
        'name' => 'English 2'
    ]);

    $third_race = Race::factory()->create([
        'name' => 'Tamil'
    ]);

    //Filter by partial name = English
    $response = $this->masterRaceRepository->getAllPaginated(['name' => 'English'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$first_race->id, $second_race->id]);

    //Filter by name = English 2
    $response = $this->masterRaceRepository->getAllPaginated(['name' => 'English 2'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$second_race->id]);

    //Filter by non-existing name = English 3
    $response = $this->masterRaceRepository->getAllPaginated(['name' => 'English 3'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([]);

    //sort by name asc
    $response = $this->masterRaceRepository->getAllPaginated([
        'order_by' => ['name' => 'asc'],
    ])->toArray();

    expect($response['data'][0]['name'][$this->locale])->toEqual('English')
        ->and($response['data'][1]['name'][$this->locale])->toEqual('English 2')
        ->and($response['data'][2]['name'][$this->locale])->toEqual('Tamil');

    //sort by name desc
    $response = $this->masterRaceRepository->getAllPaginated([
        'order_by' => ['name' => 'desc'],
    ])->toArray();

    expect($response['data'][0]['name'][$this->locale])->toEqual('Tamil')
        ->and($response['data'][1]['name'][$this->locale])->toEqual('English 2')
        ->and($response['data'][2]['name'][$this->locale])->toEqual('English');

    //sort by id asc
    $response = $this->masterRaceRepository->getAllPaginated([
        'order_by' => ['id' => 'asc'],
    ])->toArray();

    expect($response['data'][0]['id'])->toEqual($first_race->id)
        ->and($response['data'][1]['id'])->toEqual($second_race->id)
        ->and($response['data'][2]['id'])->toEqual($third_race->id);

    //sort by id desc
    $response = $this->masterRaceRepository->getAllPaginated([
        'order_by' => ['id' => 'desc'],
    ])->toArray();

    expect($response['data'][0]['id'])->toEqual($third_race->id)
        ->and($response['data'][1]['id'])->toEqual($second_race->id)
        ->and($response['data'][2]['id'])->toEqual($first_race->id);
});
