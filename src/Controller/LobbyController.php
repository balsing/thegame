<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use App\Form\LobbyType;
use App\Services\GameLogic\GameLogicService;
use App\Services\WebSocketService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="lobby_")
 */
class LobbyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        GameLogicService $gameLogicService,
        WebSocketService $socketService,
        Request          $request
    ): Response
    {
        $form = $this->createForm(LobbyType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new \Exception('User is not instance of User:class');
            }
            $token = $socketService->generateToken($user);

            if ($form->getClickedButton()->getName() === 'new_game') {
                $room = $gameLogicService->createNewGame($user);
            } else {
                $code = $form->get('code')->getViewData();
                $room = $gameLogicService->enterToGame($user, $code);
            }
            $response = $this->redirectToRoute('lobby_room', ['room' => $room->getId()]);
            $cookie = new Cookie('token', $token, 0, '/', null, null, false);
            $response->headers->setCookie($cookie);

            return $response;
        }

        return $this->render('lobby.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/room/{room<\d+>}", name="room")
     */
    public function room(
        Room             $room
    ): Response
    {
        return $this->render('room.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/room/{room<\d+>}/host", name="start")
     */
    public function start(
        WebSocketService $service,
        Room                $room
    ): Response
    {
        if ($room->getOwner() !== $this->getUser()) {
            return $this->redirectToRoute('lobby_game', ['room' => $room->getId()]);
        }

        foreach ($room->getUsersToRooms() as $player){
            $service->sendMessageToUser($player->getPlayer(), WebSocketService::START_GAME_COMMAND);
        }


        return $this->render('game/host.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/room/{room<\d+>}/client", name="game")
     */
    public function game(
        Room                $room
    ): Response
    {
        return $this->render('game/game.html.twig', [
            'room' => $room,
        ]);
    }
}
