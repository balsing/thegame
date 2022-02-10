<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment":"Идентификатор"})
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Символьный код комнаты"})
     */
    private string $code;

    /**
     * @ORM\ManyToOne(targetEntity=RoomStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private RoomStatus $status;

    /**
     * @ORM\OneToMany(targetEntity=UsersToRoom::class, mappedBy="room")
     */
    private Collection $usersToRooms;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Stage::class)
     */
    private ?Stage $lastStage;

    public function __construct()
    {
        $this->usersToRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getStatus(): ?RoomStatus
    {
        return $this->status;
    }

    public function setStatus(?RoomStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ?Player[]
     */
    public function getPlayers(): ?array
    {
        return $this->players;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

        return $this;
    }

    public function addPlayer(Player $player)
    {
        $this->players[$player->getUuid()->toRfc4122()] = $player;

        return $this;
    }

    public function getQuestions(): ?array
    {
        return $this->questions;
    }

    public function setQuestions(?array $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function getCards(): ?array
    {
        return $this->cards;
    }

    public function setCards(?array $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    /**
     * @return Collection|UsersToRoom[]
     */
    public function getUsersToRooms(): Collection
    {
        return $this->usersToRooms;
    }

    public function addUsersToRoom(UsersToRoom $usersToRoom): self
    {
        if (!$this->usersToRooms->contains($usersToRoom)) {
            $this->usersToRooms[] = $usersToRoom;
            $usersToRoom->setRoom($this);
        }

        return $this;
    }

    public function removeUsersToRoom(UsersToRoom $usersToRoom): self
    {
        if ($this->usersToRooms->removeElement($usersToRoom)) {
            // set the owning side to null (unless already changed)
            if ($usersToRoom->getRoom() === $this) {
                $usersToRoom->setRoom(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLastStage(): ?Stage
    {
        return $this->lastStage;
    }

    public function setLastStage(?Stage $lastStage): self
    {
        $this->lastStage = $lastStage;

        return $this;
    }
}
