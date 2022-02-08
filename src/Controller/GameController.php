<?php

namespace App\Controller;

use App\Dto\Game\GameStartRequest;
use App\Dto\Request\LoginRequest;
use App\Entity\Player;
use App\Entity\Room;
use App\Form\LobbyType;
use App\Repository\RoomRepository;
use App\Services\GameLogicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/game", name="game_")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/start", name="start", methods="POST")
     */
    public function start(
        SerializerInterface $serializer,
        GameLogicService $gameLogicService,
        Request $request
    ): Response
    {
        /** @var  GameStartRequest $gameStartRequest */
        $gameStartRequest = $serializer->deserialize($request->getContent(), GameStartRequest::class, 'json');

        $room = $gameLogicService->startGame($gameStartRequest);

        return $this->json([
            'room' => $room
        ]);
    }
}
