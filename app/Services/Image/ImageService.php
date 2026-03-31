<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService extends ImageToolsService
{
    /**
     * Save image
     */
    public function save(): bool|string
    {
        $image = $this->getImage();

        $this->provider();

        $file_path = $this->getFinalImageDirectory().'/'.$this->getFinalImageName();

        $result = Storage::put($file_path, file_get_contents($image));

        return $result ? $this->getImageAddress() : false;
    }

    /**
     * Resize and save image
     *
     * @param  mixed  $width
     * @param  mixed  $height
     */
    public function fitAndSave($scale = 0.9): bool|string
    {
        $image = $this->getImage();

        $this->provider();

        [$width, $height] = getimagesize($image);

        $width = (int) floor($scale * $width);
        $height = (int) floor($scale * $height);

        $manager = new ImageManager(new Driver);

        $file_path = $this->getFinalImageDirectory().'/'.$this->getFinalImageName();

        $resizedImage = $manager->read($image->getRealPath())->resize($width, $height);

        $result = Storage::put($file_path, $resizedImage->encode());

        return $result ? $this->getImageAddress() : false;
    }

    /**
     * Delete Image
     *
     * @param  mixed  $image_path
     */
    public function deleteImage($image_path): void
    {
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
}
