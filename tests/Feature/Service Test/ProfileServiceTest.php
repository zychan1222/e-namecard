<?php

use App\Services\ProfileService;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->employeeRepository = Mockery::mock(EmployeeRepository::class);
    $this->userRepository = Mockery::mock(UserRepository::class);

    $this->profileService = new ProfileService($this->employeeRepository, $this->userRepository);
});

it('retrieves employee by id', function () {
    $employeeId = 1;
    $employee = (object) ['id' => $employeeId];

    $this->employeeRepository->shouldReceive('findById')
        ->with($employeeId)
        ->andReturn($employee);

    $result = $this->profileService->getEmployeeById($employeeId);

    expect($result)->toBe($employee);
});

it('retrieves user email by user id', function () {
    $userId = 1;
    $user = (object) ['id' => $userId, 'email' => 'john@example.com'];

    $this->userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn($user);

    $email = $this->profileService->getUserEmail($userId);

    expect($email)->toBe('john@example.com');
});

it('returns "Email not available" if user not found', function () {
    $userId = 1;

    $this->userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn(null);

    $email = $this->profileService->getUserEmail($userId);

    expect($email)->toBe('Email not available');
});

it('updates employee profile and deletes old profile picture', function () {
    $employeeId = 1;
    $oldProfilePic = 'old_profile.jpg';
    $newProfilePic = UploadedFile::fake()->image('new_profile.jpg');
    $employee = (object) ['id' => $employeeId, 'profile_pic' => $oldProfilePic];
    $data = ['name' => 'John Doe', 'profile_pic' => $newProfilePic];

    $this->employeeRepository->shouldReceive('findById')
        ->with($employeeId)
        ->andReturn($employee);

    $this->employeeRepository->shouldReceive('update')
        ->with($employee, ['name' => 'John Doe', 'profile_pic' => 'new_profile.jpg'])
        ->once();

    // Call the method
    $this->profileService->updateProfile($employee, $data);

    // Assert that the old profile picture was deleted
    expect(file_exists(public_path('storage/profile_pics/' . $oldProfilePic)))->toBeFalse();
});
