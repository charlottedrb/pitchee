<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Card;
use App\Entity\User;
use App\Form\LikeType;
use App\Repository\CardRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/like')]
class LikeController extends AbstractController
{
    #[Route('/', name: 'like_index', methods: ['GET'])]
    public function index(LikeRepository $likeRepository): Response
    {
        return $this->render('like/index.html.twig', [
            'likes' => $likeRepository->findAll(),
        ]);
    }

    #[Route('/sidebar', name: 'like_sidebar', methods: ['GET'])]
    public function sidebar(LikeRepository $likeRepo, CardRepository $cardRepo, Request $request): Response
    {
        $likes = $likeRepo->findAllByUser($this->getUser());
        $likesArray = [];
        $maxNbr = 5;
        $i = 0;
        foreach($likes as $like){
            $i++;
            if($i > $maxNbr){break;}
            $card = $like->getCard();
            $media = '';
            if($card->getType() == 'video' || $card->getType() == 'musique'){
                $media = '
                <div class="video-responsive">
                    <iframe src="https://www.youtube.com/embed/' . substr($card->getContent(), -11) . '"  allow="fullscreen;"></iframe>
                </div>
                ';
            }else{
                $media = '
                <img src="' . $card->getContent() . '" alt="' . $card->getTitle() . '">
                ';
            };

            $html = $this->render('sidebar/card.html.twig', [
                'title' => $card->getTitle(),
                'content' => $card->getAnswer(),
                'type' => $card->getType(),
                'media' => $media,
            ])->getContent();

            array_push($likesArray, $html);
        }
        return new Response(json_encode($likesArray));
    }

    #[Route('/new', name: 'like_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $like = new Like();
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($like);
            $entityManager->flush();

            return $this->redirectToRoute('like_index');
        }

        return $this->render('like/new.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new/{id}', name: 'like_new_card', methods: ['GET'])]
    public function likeNewCard(Request $request, Card $card, CardRepository $cardRepo, LikeRepository $likeRepo, UserRepository $userRepo): Response
    {
        $params = $request->query;

        if(empty($params->get('liked'))){return new Response('Il manque un paramètre');}

        $liked = true;
        $liked_sent = $params->get('liked');
        if($liked_sent === 'true'){
            $liked = true;
        }elseif($liked_sent === 'false'){
            $liked = false;
        }else{
            return new Response('Type liked faux');
        }

        $user = $userRepo->findOneBy(['email' => $this->getUser()->getUsername()]);

        $likeSearch = $likeRepo->findOneBy(['user' => $user, 'card' => $card]);

        if($likeSearch === null){
            $newLike = new Like();

            $newLike->setCard($card);
            $newLike->setUser($user);
            $newLike->setLiked($liked);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newLike);
            $entityManager->flush();

            return new Response('OK');
        }else{
            return new Response('Card déjà likée');
        }
    }

    #[Route('/{id}', name: 'like_show', methods: ['GET'])]
    public function show(Like $like): Response
    {
        return $this->render('like/show.html.twig', [
            'like' => $like,
        ]);
    }

    #[Route('/{id}/edit', name: 'like_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Like $like): Response
    {
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('like_index');
        }

        return $this->render('like/edit.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'like_delete', methods: ['POST'])]
    public function delete(Request $request, Like $like): Response
    {
        if ($this->isCsrfTokenValid('delete'.$like->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($like);
            $entityManager->flush();
        }

        return $this->redirectToRoute('like_index');
    }
}
