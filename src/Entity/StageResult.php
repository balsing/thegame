<?php

namespace App\Entity;

use App\Repository\StageResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StageResultRepository::class)
 */
class StageResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Stage::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Stage $stage;

    /**
     * @ORM\ManyToOne(targetEntity=UsersToRoom::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?UsersToRoom $player;

    /**
     * @ORM\ManyToOne(targetEntity=Card::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Card $card;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isVoted;

    public function __toString(): string
    {
        $this->isVoted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getPlayer(): ?UsersToRoom
    {
        return $this->player;
    }

    public function setPlayer(?UsersToRoom $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getIsVoted(): ?bool
    {
        return $this->isVoted;
    }

    public function setIsVoted(?bool $isVoted): self
    {
        $this->isVoted = $isVoted;

        return $this;
    }
}
