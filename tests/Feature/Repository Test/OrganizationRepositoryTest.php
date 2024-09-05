<?php

use App\Repositories\OrganizationRepository;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new OrganizationRepository();
});

it('can create an organization', function () {
    $data = [
        'name' => 'Test Organization',
        'address' => '123 Test St',
        'email' => 'test@organization.com',
        'phoneNo' => '123-456-7890',
    ];

    $organization = $this->repository->create($data);

    expect($organization)->toBeInstanceOf(Organization::class);
    expect($organization->name)->toEqual($data['name']);
    expect($organization->address)->toEqual($data['address']);
    expect($organization->email)->toEqual($data['email']);
    expect($organization->phoneNo)->toEqual($data['phoneNo']);
});

it('can find an organization by id', function () {
    $organization = Organization::factory()->create();

    $foundOrganization = $this->repository->findById($organization->id);

    expect($foundOrganization)->toBeInstanceOf(Organization::class);
    expect($foundOrganization->id)->toEqual($organization->id);
});

it('returns null when organization not found by id', function () {
    $foundOrganization = $this->repository->findById(999);

    expect($foundOrganization)->toBeNull();
});

it('can save an organization', function () {
    $organization = Organization::factory()->create(['name' => 'Old Name']);

    $organization->name = 'New Name';
    $this->repository->save($organization);

    $organization->refresh();

    expect($organization->name)->toEqual('New Name');
});

it('can find an organization or fail', function () {
    $organization = Organization::factory()->create();

    $foundOrganization = $this->repository->find($organization->id);

    expect($foundOrganization)->toBeInstanceOf(Organization::class);
    expect($foundOrganization->id)->toEqual($organization->id);
});

it('throws an exception when organization not found by id', function () {
    $this->repository->find(999);
})->throws(ModelNotFoundException::class, 'No query results for model [App\Models\Organization] 999');

it('can update an organization', function () {
    $organization = Organization::factory()->create(['name' => 'Old Name']);

    $this->repository->update($organization, ['name' => 'New Name']);

    $organization->refresh();

    expect($organization->name)->toEqual('New Name');
});