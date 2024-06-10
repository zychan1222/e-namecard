<?php

use App\Models\Race;
use App\Services\RaceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->masterRaceService = app(RaceService::class);

    app()->setLocale('en');

    $this->locale = app()->getLocale();

    $this->race_table_name = resolve(Race::class)->getTable();
});

test('getAllPaginatedRaces()', function () {
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
    $response = $this->masterRaceService->getAllPaginatedRaces(['name' => 'English'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$first_race->id, $second_race->id]);

    //Filter by name = English 2
    $response = $this->masterRaceService->getAllPaginatedRaces(['name' => 'English 2'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$second_race->id]);

    //Filter by non-existing name = English 3
    $response = $this->masterRaceService->getAllPaginatedRaces(['name' => 'English 3'])->toArray();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([]);

    //sort by name asc
    $response = $this->masterRaceService->getAllPaginatedRaces([
        'order_by' => ['name' => 'asc'],
    ])->toArray();

    expect($response['data'][0]['name'][$this->locale])->toEqual('English');

    //sort by name desc
    $response = $this->masterRaceService->getAllPaginatedRaces([
        'order_by' => ['name' => 'desc'],
    ])->toArray();

    expect($response['data'][0]['name'][$this->locale])->toEqual('Tamil');

    //sort by id asc
    $response = $this->masterRaceService->getAllPaginatedRaces([
        'order_by' => ['id' => 'asc'],
    ])->toArray();

    expect($response['data'][0]['id'])->toEqual($first_race->id);

    //sort by id desc
    $response = $this->masterRaceService->getAllPaginatedRaces([
        'order_by' => ['id' => 'desc'],
    ])->toArray();

    expect($response['data'][0]['id'])->toEqual($third_race->id);
});

test('createRace()', function () {
    //store success
    $this->assertDatabaseCount($this->race_table_name, 0);
    $payload = [
        'name' => [
            'en' => fake()->name,
            'fr' => fake()->name
        ],
    ];

    $response = $this->masterRaceService->createRace($payload)->toArray();

    expect($response['name'])->toEqual($payload['name']);

    $this->assertDatabaseCount($this->race_table_name, 1);
    $this->assertDatabaseHas($this->race_table_name, [
        'name->en' => $payload['name']['en'],
        'name->fr' => $payload['name']['fr'],
    ]);
});

test('updateRace()', function () {
    $first_race = Race::factory()->create([
        'name->en' => 'Chinese',
        'name->zh' => '华人',
    ]);

    //update with id exist
    $this->assertDatabaseCount($this->race_table_name, 1);
    $payload = [
        'name' => [
            'en' => 'Malay',
            'zh' => '马来人',
        ],
    ];

    $response = $this->masterRaceService->updateRace($first_race->id, $payload)->toArray();

    expect($response['name'])->toEqual($payload['name']);
    $this->assertDatabaseCount($this->race_table_name, 1);

    $this->assertDatabaseHas($this->race_table_name, [
        'name->en' => $payload['name']['en'],
        'name->zh' => $payload['name']['zh'],
    ]);

    //update with id not exist
    $payload = [
        'name' => [
            'en' => 'Test 3',
        ],
    ];

    $this->expectException(ModelNotFoundException::class);
    $this->masterRaceService->updateRace(9999, $payload)->toArray();

    $this->assertDatabaseCount($this->race_table_name, 1);
});

test('deleteRace()', function () {
    $first_race = Race::factory()->create();
    $other_racess = Race::factory(3)->create();

    $this->assertDatabaseCount($this->race_table_name, 4);

    //delete success
    $this->masterRaceService->deleteRace($first_race->id);

    $this->assertDatabaseCount($this->race_table_name, 3);
    $this->assertDatabaseMissing($this->race_table_name, ['id' => $first_race->id]);

    foreach ($other_racess as $other_race) {
        $this->assertDatabaseHas($this->race_table_name, ['id' => $other_race->id]);
    }

    //id not exist
    $this->expectException(ModelNotFoundException::class);
    $this->masterRaceService->deleteRace(9999);
});
