<?php

namespace App\Controller;

use App\Entity\CardList;
use App\Form\CardListType;
use App\Repository\CardListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card/list')]
class CardListController extends AbstractController
{
    #[Route('/', name: 'card_list_index', methods: ['GET'])]
    public function index(CardListRepository $cardListRepository): Response
    {
        return $this->render('card_list/index.html.twig', [
            'card_lists' => $cardListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'card_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $cardList = new CardList();
        $form = $this->createForm(CardListType::class, $cardList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cardList);
            $entityManager->flush();

            return $this->redirectToRoute('card_list_index');
        }

        return $this->render('card_list/new.html.twig', [
            'card_list' => $cardList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'card_list_show', methods: ['GET'])]
    public function show(CardList $cardList): Response
    {
        return $this->render('card_list/show.html.twig', [
            'card_list' => $cardList,
        ]);
    }

    #[Route('/{id}/edit', name: 'card_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CardList $cardList): Response
    {
        $form = $this->createForm(CardListType::class, $cardList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('card_list_index');
        }

        return $this->render('card_list/edit.html.twig', [
            'card_list' => $cardList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'card_list_delete', methods: ['POST'])]
    public function delete(Request $request, CardList $cardList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cardList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cardList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_list_index');
    }
}
