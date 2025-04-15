<?php

namespace App\Dto;

class FileResponseData
{
    public function __construct(
        public string $filePath,
        public string $fileName
    ) {}
}