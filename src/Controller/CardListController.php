<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\CardList;
use App\Form\CardListType;
use App\Repository\CardListRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card_list')]
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
            $cardList->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cardList);
            $entityManager->flush();

            return $this->redirectToRoute('my_lists', ['username' => $this->getUser()->getPseudo()]);
        }

        return $this->render('card_list/new.html.twig', [
            'card_list' => $cardList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{card}/list', name: 'card_list_get_card_list', methods: ['GET'])]
    public function card_list_card(Card $card, CardListRepository $cardListRepo): Response
    {
//        dd($card);

        $rawLists = $cardListRepo->findAll();
//        $rawAssignedLists = $cardListRepo->findBy(['cards' => $card]);
//        dd($rawAssignedLists);
        $lists = [];
        $activeLists = [];
//        dd($lists);

        foreach($rawLists as $list){
            foreach($list->getCards() as $cards){
                if($cards->getId() == $card->getId()) array_push($activeLists, $list->getName());
            }
        }

//        dd($activeLists);

        foreach($rawLists as $list){
            array_push($lists, $list->getName());
        }

//        dd($lists);

        $listResult = ['list' => $lists, 'active' => $activeLists];

        $select = $this->render('card_list/select.html.twig', [
            'lists' => $rawLists,
            'active' => $activeLists,
            'id' => $card->getId()
        ])->getContent();

        return new Response($select);
    }

    #[Route('/{card}/save', name: 'card_list_card_save', methods: ['GET'])]
    public function save(Card $card, Request $request, CardListRepository $cardListRepo, UserRepository $userRepo): Response
    {
        $params = $request->query;
        $t = '';

        $userLists = $userRepo->findOneBy(['pseudo' => $this->getUser()->getPseudo()])->getCardLists();
        $userListsFilter = [];
//        dd($userLists);

        foreach($userLists as $list){
            array_push($userListsFilter, $list->getName());
        }

        foreach($params->get('selected') as $selected){
//            $t .= $selected;
            if(!in_array($selected, $userListsFilter)){
                return new Response($selected.' ne fait pas parti de vos list');
            }
        }

        $em = $this->getDoctrine()->getManager();

        foreach($params->get('selected') as $selected){
            $list = $cardListRepo->findOneBy(['name' => $selected]);

            $listEm = $em->getRepository(CardList::class)->find($list->getId());
//            dd($listEm);
            $listEm->addCard($card);
            $em->flush();
//            $cards = $list->getCards();
//            $list->addCard($card);
//            $cardListRepo
        }
//        dd($params);
        return new Response($t);
    }

    #[Route('/{id}', name: 'card_list_show', methods: ['GET'])]
    public function show(CardList $cardList): Response
    {
        $parents = [];
        $initialCard = $cardList;

        // On récupère tous les parents de la card_list
        while ($cardList->getParent() !== null) {
            $parents[] = $cardList->getParent();
            $cardList = $cardList->getParent();
        }

        // On inverse la tableau pour qu'il apparaisse par ordre de d'ascendance
        $sortedParents = array_reverse($parents);

        //dd($parents);
        return $this->render('card_list/show.html.twig', [
            'card_list' => $initialCard,
            'parents' => $sortedParents
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

        return $this->redirectToRoute('my_lists', ['username' => $this->getUser()->getPseudo()]);
    }
}
