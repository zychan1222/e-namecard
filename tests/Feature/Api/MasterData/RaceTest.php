<?php

use App\Models\Race;
use App\Models\User;
use Database\Seeders\InternationalizationSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed([
        InternationalizationSeeder::class,
        PermissionSeeder::class,
    ]);

    app()->setLocale('en');

    $this->withHeaders([
        'Accept-Language' => 'en'
    ]);

    $user = User::factory()->create([
        'password' => Hash::make('123456'),
    ]);

    $user->assignRole('Super Admin');

    Sanctum::actingAs($user);

    $this->routeName = 'master-data.races';
});

test('index', function () {
    $first_race = Race::factory()->create([
        'name' => 'English'
    ]);

    $second_race = Race::factory()->create([
        'name' => 'English 2'
    ]);

    $third_race = Race::factory()->create([
        'name->en' => 'Tamil',
        'name->zh' => '印度人'
    ]);

    //Filter by partial name = English
    $response = $this->json('GET', route($this->routeName . '.index'), ['name' => 'English'])->json();

    expect($response)->toHaveSuccessGeneralResponse();

    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$first_race->id, $second_race->id]);

    //Filter by name = English 2
    $response = $this->json('GET', route($this->routeName . '.index'), ['name' => 'English 2'])->json();

    expect($response)->toHaveSuccessGeneralResponse();


    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([$second_race->id]);

    //Filter non-existing name = English 3
    $response = $this->json('GET', route($this->routeName . '.index'), ['name' => 'English 3'])->json();

    expect($response)->toHaveSuccessGeneralResponse();


    $respIds = Arr::pluck($response['data'], 'id');
    sort($respIds);

    expect($respIds)->toEqual([]);

    //sort by name asc
    $response = $this->json('GET', route($this->routeName . '.index'), [
        'order_by' => ['name' => 'asc'],
    ])->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'][0]['name'])->toEqual('English')
        ->and($response['data'][1]['name'])->toEqual('English 2')
        ->and($response['data'][2]['name'])->toEqual('Tamil');

    //sort by name desc
    $response = $this->json('GET', route($this->routeName . '.index'), [
        'order_by' => ['name' => 'desc'],
    ])->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'][0]['name'])->toEqual('Tamil')
        ->and($response['data'][1]['name'])->toEqual('English 2')
        ->and($response['data'][2]['name'])->toEqual('English');

    //sort by id asc
    $response = $this->json('GET', route($this->routeName . '.index'), [
        'order_by' => ['id' => 'asc'],
    ])->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'][0]['id'])->toEqual($first_race->id)
        ->and($response['data'][1]['id'])->toEqual($second_race->id)
        ->and($response['data'][2]['id'])->toEqual($third_race->id);

    //sort by id desc
    $response = $this->json('GET', route($this->routeName . '.index'), [
        'order_by' => ['id' => 'desc'],
    ])->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'][0]['id'])->toEqual($third_race->id)
        ->and($response['data'][1]['id'])->toEqual($second_race->id)
        ->and($response['data'][2]['id'])->toEqual($first_race->id);

    //Test pattern
    $response = $this->json('GET', route($this->routeName . '.index'), ['name' => 'Tamil'])->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'][0])->toMatchArray([
            'id' => $third_race->id,
            'name' => $third_race->name,
            'translations' => [
                'name' => [
                    'en' => 'Tamil',
                    'zh' => '印度人'
                ]
            ]
        ]);
});

test('show', function () {
    $master_race = Race::factory()->create();

    //test with id exist
    $response = $this->json('GET', route($this->routeName . '.show', ['master_race' => $master_race->id]))->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'])->toMatchArray([
            'id' => $master_race->id,
            'name' => $master_race->name,
            'translations' => $master_race->translations
        ]);

    //test with id not exist
    $response = $this->json('GET', route($this->routeName . '.show', ['master_race' => 9999]))->json();

    expect($response)->toHaveModelResourceNotFoundResponse();
});

test('store', function () {
    //store success
    $this->assertDatabaseCount('master_races', 0);

    $payload = [
        'name' => [
            'en' => fake()->name,
            'zh' => fake()->name,
        ],
    ];

    $response = $this->json('POST', route($this->routeName . '.create'), $payload)->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data'])->toMatchArray([
            'name' => $payload['name']['en'],
            'translations' => [
                'name' => [
                    'en' => $payload['name']['en'],
                    'zh' => $payload['name']['zh'],
                ]
            ]
        ]);

    $this->assertDatabaseCount('master_races', 1);

    $this->assertDatabaseHas(resolve(Race::class)->getTable(), [
        'name->en' => $payload['name']['en'],
        'name->zh' => $payload['name']['zh'],
    ]);
});

test('update', function () {
    $first_race = Race::factory()->create([
        'name->en' => 'Test EN',
        'name->zh' => 'Test ZH',
    ]);

    //update with id exist
    $this->assertDatabaseCount('master_races', 1);
    $payload = [
        'name' => [
            'en' => 'Test 2',
            'zh' => 'Test 3',
        ],
    ];

    $response = $this->json('PUT', route($this->routeName . '.update', ['master_race' => $first_race->id]), $payload)->json();

    expect($response)->toHaveSuccessGeneralResponse()
        ->and($response['data']['name'])->toEqual($payload['name']['en'])
        ->and($response['data']['translations']['name'])->toEqual($payload['name']);

    $this->assertDatabaseCount(resolve(Race::class)->getTable(), 1);
    $this->assertDatabaseHas(resolve(Race::class)->getTable(), [
        'name->en' => $payload['name']['en'],
        'name->zh' => $payload['name']['zh'],
    ]);

    //update with id not exist
    $payload = [
        'name' => [
            'en' => 'Test 3',
        ],
    ];

    $response = $this->json('PUT', route($this->routeName . '.update', ['master_race' => 9999]), $payload)->json();

    expect($response)->toHaveModelResourceNotFoundResponse();

    // Assert nothing updated
    $this->assertDatabaseCount('master_races', 1);
    $this->assertDatabaseHas(resolve(Race::class)->getTable(), [
        'name->en' => 'Test 2',
        'name->zh' => 'Test 3',
    ]);
});

test('destroy', function () {
    $first_race = Race::factory()->create();
    $other_races = Race::factory(3)->create();

    $this->assertDatabaseCount('master_races', 4);

    //id not exist
    $response = $this->json('DELETE', route($this->routeName . '.destroy', ['master_race' => 9999]))->json();
    expect($response)->toHaveModelResourceNotFoundResponse();

    $this->assertDatabaseCount('master_races', 4);

    //delete success
    $response = $this->json('DELETE', route($this->routeName . '.destroy', ['master_race' => $first_race->id]))->json();
    expect($response)->toHaveSuccessGeneralResponse();

    $this->assertDatabaseCount('master_races', 3);
    $this->assertDatabaseMissing('master_races', ['id' => $first_race->id]);

    foreach ($other_races as $other_race) {
        $this->assertDatabaseHas('master_races', ['id' => $other_race->id]);
    }
});
