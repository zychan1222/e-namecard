<?php

use App\Repositories\AdminRepository;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    $this->adminRepository = new AdminRepository();
    $this->admin = Admin::factory()->create(['employee_id' => 1]);
});

it('can find an admin by employee ID', function () {
    $result = $this->adminRepository->findByEmployeeId(1);
    expect($result)->toBeInstanceOf(Admin::class);
    expect($result->employee_id)->toEqual(1);
});

it('returns null if no admin is found for the given employee ID', function () {
    $result = $this->adminRepository->findByEmployeeId(999);
    expect($result)->toBeNull();
});
