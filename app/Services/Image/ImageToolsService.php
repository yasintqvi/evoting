<?php

namespace App\Services\Image;

use Illuminate\Http\UploadedFile;

class ImageToolsService
{
    private ?UploadedFile $image;

    private ?string $exclusive_directory = null;

    private ?string $image_directory = null;

    private ?string $image_name = null;

    private ?string $imageFormat = null;

    private ?string $final_image_directory = null;

    private ?string $final_image_name = null;

    public function setImage(UploadedFile $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    public function getExclusiveDirectory(): ?string
    {
        return $this->exclusive_directory;
    }

    public function setExclusiveDirectory($exclusive_directory): static
    {
        $this->exclusive_directory = trim($exclusive_directory, '/\\');

        return $this;
    }

    public function getImageDirectory(): ?string
    {
        return $this->image_directory;
    }

    public function setImageDirectory($image_directory): static
    {
        $this->image_directory = trim($image_directory, '/\\');

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName($image_name): static
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function setCurrentImageName()
    {
        return ! empty($this->image) ? $this->setImageName(pathinfo($this->image->getClientOriginalName(), PATHINFO_FILENAME)) : false;
    }

    public function getImageFormat(): ?string
    {
        return $this->imageFormat;
    }

    public function setImageFormat($imageFormat): static
    {
        $this->imageFormat = $imageFormat;

        return $this;
    }

    public function getFinalImageDirectory(): ?string
    {
        return $this->final_image_directory;
    }

    public function setFinalImageDirectory($final_image_directory): static
    {
        $this->final_image_directory = $final_image_directory;

        return $this;
    }

    public function getFinalImageName(): ?string
    {
        return $this->final_image_name;
    }

    public function setFinalImageName($final_image_name): static
    {
        $this->final_image_name = $final_image_name;

        return $this;
    }

    protected function checkDirectory($image_directory): void
    {
        if (! file_exists($image_directory)) {
            mkdir($image_directory, 0755, true);
        }
    }

    public function getImageAddress(): ?string
    {
        return $this->final_image_directory . DIRECTORY_SEPARATOR . $this->final_image_name;
    }

    protected function provider()
    {
        $this->getImageDirectory() ?? $this->setImageDirectory(date('Y') . DIRECTORY_SEPARATOR . date('m'));

        $this->getImageName() ?? $this->setImageName(time());

        $this->getImageFormat() ?? $this->setImageFormat($this->image->extension());

        $final_image_directory = empty($this->getExclusiveDirectory()) ? $this->getImageDirectory() : $this->getExclusiveDirectory() . DIRECTORY_SEPARATOR . $this->getImageDirectory();
        $this->setFinalImageDirectory($final_image_directory);

        $this->setFinalImageName($this->getImageName() . '.' . $this->getImageFormat());

        $this->checkDirectory($this->getFinalImageDirectory());
    }
}
