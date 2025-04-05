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

    #[Assert\NotNull]
    public readonly ?array $members;

    // Propriétés présentent dans le retour API mais non utilisées pour le moment
    /*
    public readonly ?string $external_message;
    public readonly ?string $banner_color_id;
    public readonly ?string $banner_logo_id;
    public readonly ?int $enrollment_status;
    public readonly ?string $guild_type;
    public readonly ?int $level_requirement;*/

    /**
     * @param array<mixed> $apiGuildData
     */
    public function __construct(array $apiGuildData) {
        $defaults = [
            'name' => null,
            'id_swgoh' => null,
            'galactic_power' => null,
            'number_players' => null,
            'members' => []
        ];
        $apiGuildData = array_merge($defaults, $apiGuildData['data']);
        $this->name = $apiGuildData['name'];
        $this->id_swgoh = $apiGuildData['guild_id'];
        $this->galactic_power = $apiGuildData['galactic_power'];
        $this->number_players = $apiGuildData['member_count'];
        $this->members = $apiGuildData['members'];
    }
}