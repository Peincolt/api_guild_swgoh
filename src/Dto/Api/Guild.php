<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class Guild
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $id_swgoh;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $name;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $galactic_power;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $number_players;

    // Propriétés présentent dans le retour API mais non utilisées pour le moment
    /*
    public readonly ?string $external_message;
    public readonly ?string $banner_color_id;
    public readonly ?string $banner_logo_id;
    public readonly ?int $enrollment_status;
    public readonly ?string $guild_type;
    public readonly ?int $level_requirement;*/

    /**
     * @var array<string, null>
     */
    private array $defaults = [
        'name' => null,
        'id_swgoh' => null,
        'galactic_power' => null,
        'number_players' => null
    ];

    /**
     * @param array<mixed> $apiGuildData
     */
    public function __construct(array $apiGuildData) {
        $apiGuildData = array_merge($this->defaults, $apiGuildData);
        $this->name = $apiGuildData['name'];
        $this->id_swgoh = $apiGuildData['guild_id'];
        $this->galactic_power = $apiGuildData['galactic_power'];
        $this->number_players = $apiGuildData['member_count'];
    }
}