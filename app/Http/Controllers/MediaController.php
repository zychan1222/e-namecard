<?php

namespace App\Http\Controllers;

use App\Models\Media; // Import the Media model
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index()
    {
        $mediaItems = Media::all();
        return $mediaItems;
    }
}
