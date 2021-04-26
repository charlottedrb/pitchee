<?php

namespace App\Controller;

use App\Repository\CardListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CardRepository;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/{username}/cards', name: "my_cards", methods: ['GET'])]
    public function userCards(CardRepository $cardRepository): Response
    {
        return $this->render('profile/my_cards.html.twig', [
            'cards' => $cardRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/{username}/lists', name: "my_lists", methods: ['GET'])]
    public function userLists(CardListRepository $cardListRepository): Response
    {
        $cardLists = $cardListRepository->findByUser($this->getUser());

        return $this->render('profile/my_lists.html.twig', [
            'cardLists' => $cardListRepository->findByUser($this->getUser()),
        ]);
    }
}
