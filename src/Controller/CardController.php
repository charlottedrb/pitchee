<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

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

            return $this->redirectToRoute('my_cards', ['username' => $this->getUser()->getPseudo()]);
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/next', name: 'card_next', methods: ['GET'])]
    public function next(Request $request, CardRepository $cardRepository): JsonResponse
    {
        $session = new Session();

        $cards = $cardRepository->findByButLiked($this->getUser()->getId());

        if(!empty($cards)){
            $rand_card = array_rand($cards, 1);
            $card = [];

            $previous_card = null;
            if($session->get('previous_card') !== null){
                while($rand_card )
                    $previous_card = $session->get('previous_card');
            }

            if(!empty($cards)){
                $card = $cards[$rand_card];
//            $session->set('previous_card', $card->getId());
            }
        }else{
            return new JsonResponse('NOP');
        }
//        dd($cards);

        return new JsonResponse(json_encode($card));
    }

    #[Route('/generate', name: 'card_generate', methods: ['GET'])]
    public function generate(Request $request, UserRepository $userRepository): Response
    {
        $params = $request->query;
        $user = $userRepository->findOneBy(['id'=>$params->get('user')]);

        return new Response(
            $this->render('card/template.html.twig', [
                'user' => $user->getPseudo(),
                'id' => $params->get('id'),
                'title' => $params->get('title'),
                'content' => $params->get('content'),
                'type' => $params->get('type'),
                'media' => $params->get('media'),
            ])->getContent()
        );
    }

    #[Route('/liked', name: 'card_liked', methods: ['GET'])]
    public function liked(Request $request, LikeRepository $likeRepository)
    {
        $cards = $likeRepository->findAllCardsButLiked($this->getUser()->getId());
        dd($cards);
    }

    #[Route('/{id}', name: 'card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    #[Route('/{id}/edit', name: 'card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('my_cards', ['username' => $this->getUser()->getPseudo()]);
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
