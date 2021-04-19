<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card')]
class CardController extends AbstractController
{
    #[Route('/', name: 'card_index', methods: ['GET'])]
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'card_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card->setUser($this->getUser());
            $tz = new DateTimeZone("europe/paris");
            $card->setCreatedAt(new \DateTime('now', $tz));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/next', name: 'card_next', methods: ['GET'])]
    public function next(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $card = $em->getRepository(Card::class)->findOneBy(array('title' => 'Test'));
//        $cards = $em->getRepository(Card::class)->getAllButLiked(1);
//        dump($card);
//        die();
//        dd($cards);
//        dd($cards);

        $card_arr = [
            'user_id' => $card->getUser(),
            'title' => $card->getTitle(),
            'content' => $card->getContent(),
            'answer' => $card->getAnswer(),
            'created_at' => $card->getCreatedAt(),
            'type' => $card->getType(),
        ];
//        dd($card_arr);
        $card = json_encode($card_arr);

        return new JsonResponse($card);
    }

    #[Route('/{id}', name: 'card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    #[Route('/{id}/edit', name: 'card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index');
    }
}
