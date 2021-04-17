<?php

namespace App\Controller;

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
}
