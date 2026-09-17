<?php

namespace App\Services\Image;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService extends ImageToolsService
{
    /**
     * Save image into public/ and return its relative web path.
     */
    public function save(): bool|string
    {
        $image = $this->getImage();

        $this->provider();

        $absolutePath = $this->getAbsoluteImagePath();
        $result = file_put_contents($absolutePath, file_get_contents($image->getRealPath()));

        if ($result === false) {
            return false;
        }

        @chmod($absolutePath, 0644);

        return $this->getImageAddress();
    }

    /**
     * Resize and save image into public/.
     *
     * @param  mixed  $width
     * @param  mixed  $height
     */
    public function fitAndSave($scale = 0.9): bool|string
    {
        $image = $this->getImage();

        $this->provider();

        [$width, $height] = getimagesize($image->getRealPath());

        $width = (int) floor($scale * $width);
        $height = (int) floor($scale * $height);

        $manager = new ImageManager(new Driver);

        $absolutePath = $this->getAbsoluteImagePath();
        $resizedImage = $manager->read($image->getRealPath())->resize($width, $height);
        $result = file_put_contents($absolutePath, (string) $resizedImage->encode());

        if ($result === false) {
            return false;
        }

        @chmod($absolutePath, 0644);

        return $this->getImageAddress();
    }

    /**
     * Delete Image
     *
     * @param  mixed  $image_path
     */
    public function deleteImage($image_path): void
    {
        $path = public_path(ltrim(str_replace('\\', '/', (string) $image_path), '/'));

        if (is_file($path)) {
            unlink($path);
        }
    }
}
