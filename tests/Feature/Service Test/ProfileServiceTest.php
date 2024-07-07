<?php

use App\Services\ProfileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

test('uploads profile picture correctly', function () {
    Storage::fake('public'); 

    // Mocking a request with a file upload
    $file = UploadedFile::fake()->image('profile.jpg');

    // Mocking a request instance
    $request = $this->mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('hasFile')->with('profile_pic')->andReturn(true);
    $request->shouldReceive('file')->with('profile_pic')->andReturn($file);

    // Creating an instance of ProfileService
    $profileService = new ProfileService();

    // Handling profile picture upload
    $result = $profileService->handleProfilePictureUpload($request);

    // Asserting the result
    expect($result)->not()->toBeNull();
    expect($result)->toBeString();
    expect(Storage::disk('public')->exists('profile_pics/' . $result))->toBeTrue();
});
test('returns null when no file is uploaded', function () {
    // Mocking a request instance
    $request = $this->mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('hasFile')->with('profile_pic')->andReturn(false);

    // Creating an instance of ProfileService
    $profileService = new ProfileService();

    // Handling profile picture upload
    $result = $profileService->handleProfilePictureUpload($request);

    // Asserting the result
    expect($result)->toBeNull();
});
