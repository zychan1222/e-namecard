<?php

namespace App\Traits\Models;

use Spatie\MediaLibrary\InteractsWithMedia;

trait HasMediaInteractions {
    use InteractsWithMedia;

    public function replaceMedia($collection_name, $media)
    {
        $this->clearMediaCollection($collection_name);
        $this->addMedia($media)->toMediaCollection($collection_name);
    }
}
