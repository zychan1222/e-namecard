<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Admin;
use App\Services\OrganizationService;
use App\Repositories\OrganizationRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    // Create mock repositories
    $this->organizationRepository = Mockery::mock(OrganizationRepository::class);
    $this->employeeRepository = Mockery::mock(EmployeeRepository::class);
    $this->userRepository = Mockery::mock(UserRepository::class);

    // Create an instance of the OrganizationService with mocked repositories
    $this->organizationService = new OrganizationService(
        $this->organizationRepository,
        $this->employeeRepository,
        $this->userRepository
    );

    // Create test user
    $this->user = User::factory()->create();
});

it('updates an organization and handles logo upload', function () {
    $organizationData = ['name' => 'Updated Organization', 'logo' => UploadedFile::fake()->image('logo.png')];
    $organization = new Organization(['id' => 1, 'logo' => 'old_logo.png']);

    // Mock organization retrieval
    $this->organizationRepository->shouldReceive('find')
        ->with(1)
        ->andReturn($organization);

    // Mock organization update
    $this->organizationRepository->shouldReceive('update')
        ->with($organization, ['name' => 'Updated Organization', 'logo' => 'logo.png'])
        ->andReturn(true); // Simulate successful update

    // Call the method
    $result = $this->organizationService->updateOrganization(1, $organizationData);

    // Assert that the organization was updated successfully
    expect($result)->toBe(true);
});

it('updates an organization without logo', function () {
    $organizationData = ['name' => 'Updated Organization'];
    $organization = new Organization(['id' => 1, 'logo' => 'old_logo.png']);
    
    // Mock organization retrieval
    $this->organizationRepository->shouldReceive('find')
        ->with(1)
        ->andReturn($organization);

    // Mock organization update
    $this->organizationRepository->shouldReceive('update')
        ->with($organization, ['name' => 'Updated Organization'])
        ->andReturn(true); // Simulate successful update

    // Call the method
    $result = $this->organizationService->updateOrganization(1, $organizationData);

    // Assert that the organization was updated successfully
    expect($result)->toBe(true);
});
