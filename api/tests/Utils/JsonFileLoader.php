<?php

namespace App\Tests\Utils;

class JsonFileLoader
{
    public static function getArrayFromJson(string $filePath): array|string
    {
        if (!file_exists($filePath)) {
            return "Le fichier suivant n'existe pas : ".$filePath;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if ($data !== false) {
            return $data;
        }

        return "Une erreur est survenue lors de la conversion du JSON en array";
    }
}