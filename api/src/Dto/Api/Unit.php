<?php

namespace App\Dto\Api;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Unit
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $name;
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $base_id;

    public readonly array $categories;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $image;

    public function __construct(array $apiUnitData) {
        $defaults = [
            'name' => null,
            'base_id' => null,
            'categories' => [],
            'image' => null
        ];
        $apiUnitData = array_merge($defaults, $apiUnitData);
        $this->name = $apiUnitData['name'];
        $this->base_id = $apiUnitData['base_id'];
        $this->categories = $apiUnitData['categories'];
        $this->image = $apiUnitData['image'];
    }
}