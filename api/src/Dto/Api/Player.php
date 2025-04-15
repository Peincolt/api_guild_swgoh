<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class Player
{
    #[Assert\NotNull]
    public readonly ?string $last_updated;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $id_swgoh;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $name;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $level;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $galactic_power;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $heroes_galactic_power;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $ships_galactic_power;

    #[Assert\NotNull]
    #[Assert\Type("integer")]
    public readonly ?int $gear_given;

    // Propriétés présentent dans le retour API mais non utilisées pour le moment
    /* public readonly ?int $lifetime_season_score;
    // public readonly ?int $member_level;
    // public readonly ?string $league_id;
    // public readonly ?string $league_name;
    // public readonly ?string $league_frame_image;
    // public readonly ?string $portrait_image;
    // public readonly ?string $title;*/

    /**
     * @param array<mixed> $apiPlayerData
     */
    public function __construct(array $apiPlayerData) {
        $defaults = [
            'last_updated' => null,
            'ally_code' => null,
            'name' => null,
            'level' => null,
            'galactic_power' => null,
            'character_galactic_power' => null,
            'ship_galactic_power' => null,
            'guild_exchange_donations' => null
        ];
        $apiPlayerData = array_merge($defaults, $apiPlayerData['data']);
        $this->last_updated = $apiPlayerData['last_updated'];
        $this->id_swgoh = $apiPlayerData['ally_code'];
        $this->name = $apiPlayerData['name'];
        $this->level = $apiPlayerData['level'];
        $this->galactic_power = $apiPlayerData['galactic_power'];
        $this->heroes_galactic_power = $apiPlayerData['character_galactic_power'];
        $this->ships_galactic_power = $apiPlayerData['ship_galactic_power'];
        $this->gear_given = $apiPlayerData['guild_exchange_donations'];
    }
}
