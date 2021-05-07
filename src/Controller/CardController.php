<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Comment;
use App\Form\CardType;
use App\Form\CommentType;
use App\Repository\CardRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

#[Route('/card')]
class CardController extends AbstractController
{
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

        $cards = $cardRepository->findByCardsButLikedWeekOld($this->getUser()->getId());

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
    public function generate(Request $request, UserRepository $userRepository, CardRepository $cardRepository): Response
    {
        $params = $request->query;

        if(!empty($params->get('pos')) && $params->get('pos') == 'sidebar'){
            if(!empty($params->get('id'))){
                $card = $cardRepository->findOneBy(['id'=>$params->get('id')]);

                $media = '';
                if($card->getType() === 'video' || $card->getType() === 'musique') {
                    $media = '
                    <div class="video-responsive">
                        <iframe src="https://www.youtube.com/embed/'.substr($card->getContent(), -11) .'"  allow="fullscreen;"></iframe>
                    </div>
                    ';
                }else{
                    $media = '
                    <img src="'.$card->getContent().'" alt="'. $card->getTitle() .'">
                    ';
                }

                return new Response(
                    $this->render('sidebar/card.html.twig', [
                        'user' => $card->getUser()->getPseudo(),
                        'id' => $card->getId(),
                        'title' => $card->getTitle(),
                        'content' => $card->getAnswer(),
                        'type' => $card->getType(),
                        'media' => $media,
                    ])->getContent()
                );
            }else{
                return new Response('NOP');
            }
        }else{
            $user = $userRepository->findOneBy(['id'=>$params->get('user')]);

            return new Response(
                $this->render('home/card.html.twig', [
                    'user' => $user->getPseudo(),
                    'id' => $params->get('id'),
                    'title' => $params->get('title'),
                    'content' => $params->get('content'),
                    'type' => $params->get('type'),
                    'media' => $params->get('media'),
                ])->getContent()
            );
        }


    }

//    #[Route('/generate/')]

    #[Route('/liked', name: 'card_liked', methods: ['GET'])]
    public function liked(Request $request, LikeRepository $likeRepository)
    {
        $cards = $likeRepository->findByCardsButLiked($this->getUser()->getId());
        dd($cards);
    }

    #[Route('/{id}', name: 'card_show', methods: ['GET', 'POST'])]
    public function show(Card $card, CommentRepository $commentRepository, $id): Response
    {
        $commentForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_new', ['cardId' => $id]))
            ->add('title', null, [
                'required' => true,
                'label' => 'Titre'
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => "Contenu"
            ])
            ->getForm();
        $comments = $commentRepository->findByCard($id);

        return $this->render('card/show.html.twig', [
            'card' => $card,
            'comments' => $comments,
            'form' => $commentForm->createView()
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

    #[Route('/delete/{id}', name: 'card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('my_cards', ['username' => $this->getUser()->getPseudo()]);
    }

    #[Route('/{username}/liked_cards', name: 'liked_cards', methods: ['GET'])]
    public function likedCards($username, UserRepository $userRepository, CardRepository $cardRepository){
        $user = $userRepository->findOneBy(['pseudo' => $username]);
        $likedCards = $cardRepository->findByLiked($user->getId());
        //dd($likedCards);

        return $this->render('card/liked_cards.html.twig', ['likedCards' => $likedCards]);
    }

    #[Route('/{id}/comment', name: 'comment_card', methods: ['POST', 'GET'])]
    public function comment_card(Card $id, Request $request, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(['card'=>$id]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $tz = new DateTimeZone("europe/paris");

            $newComment = new Comment();

            $newComment->setUser($this->getUser());
            $newComment->setCard($id);
            $newComment->setContent($data->getContent());
            $newComment->setTitle($data->getTitle());
            $newComment->setCreatedAt(new \DateTime('now', $tz));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        return $this->render('comment/show.html.twig', [
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

}