<?php

namespace App\Controller;

use App\Dto\Request\LoginRequest;
use App\Entity\Player;
use App\Form\LobbyType;
use App\Services\GameLogicService;
use App\Services\UserService;
use App\Services\WebSocketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods="POST")
     */
    public function index(
        SerializerInterface $serializer,
        UserService         $userService,
        WebSocketService    $socketService,
        Request             $request
    ): Response
    {
        /** @var  LoginRequest $result */
        $result = $serializer->deserialize($request->getContent(), LoginRequest::class, 'json');

        $player = new Player($result->getName());
        $token = $socketService->generateToken($player);

        return $this->json([
            'player' =>  $player,
            'token' =>  $token,
        ]);
    }

    /**
     * @Route("/connect", name="connect", methods="POST")
     */
    public function connect(
        SerializerInterface $serializer,
        UserService         $userService,
        WebSocketService    $socketService,
        Request             $request
    ): Response
    {
        /** @var  Player $player */
        $player = $serializer->deserialize($request->getContent(), Player::class, 'json');

        $token = $socketService->generateToken($player);

        return $this->json([
            'token' =>  $token,
        ]);
    }
}