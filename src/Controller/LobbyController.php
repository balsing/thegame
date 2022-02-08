<?php

namespace App\Controller;

use App\Dto\Lobby\LobbyCreateRequest;
use App\Dto\Lobby\LobbyJoinRequest;
use App\Dto\Request\LoginRequest;
use App\Entity\Player;
use App\Entity\Room;
use App\Entity\User;
use App\Form\LobbyType;
use App\Repository\RoomRepository;
use App\Services\GameLogicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/lobby", name="lobby_")
 */
class LobbyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        GameLogicService $gameLogicService,
        Request $request
    ): Response
    {
        $form = $this->createForm(LobbyType::class);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            if(!$user instanceof User){
                throw new \Exception('User is not instance of User:class');
            }
            if($form->getClickedButton()->getName() === 'new_game') {
                $room = $gameLogicService->createNewGame($user);
            } else {
                $code = $form->get('code')->getViewData();
                $room = $gameLogicService->enterToGame($user, $code);
            }

            return $this->redirectToRoute('lobby_room', ['room' => $room->getId()]);
        }

        return $this->render('lobby.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/room/{room<\d+>}", name="room")
     */
    public function room(
        GameLogicService $gameLogicService,
        Request $request,
        Room $room
    ): Response
    {
        return $this->render('room.html.twig', [
            'room' => $room,
        ]);
    }
}
