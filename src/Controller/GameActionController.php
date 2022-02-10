<?php

namespace App\Controller;

use App\Entity\Room;
use App\Services\GameLogic\GameLogicService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/action", name="action_")
 */
class GameActionController extends AbstractController
{
    /**
     * @Route("/{room<\d+>}/next", name="next_round")
     */
    public function index(
        GameLogicService $gameLogicService,
        Room $room
    )
    {
        $gameLogicService->nextQuestion($room);

        return $this->json([]);
    }

    /**
     * @Route("/{room<\d+>}/get_cards", name="get_cards")
     */
    public function getCards(
        GameLogicService $gameLogicService,
        Room $room
    )
    {
        $cards = $gameLogicService->getCards($room, $this->getUser());

        return $this->json($cards);
    }

    /**
     * @Route("/{room<\d+>}/choice", name="choice")
     */
    public function choice(
        GameLogicService $gameLogicService,
        Room $room,
        Request $request
    )
    {
        $cardId = $request->request->get('card', 0);
        $result = $gameLogicService->choice($room, $this->getUser(), (int) $cardId);


        return $this->json([], $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }
}