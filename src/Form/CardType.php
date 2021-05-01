<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\CardList;
use App\Repository\CardListRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Security;

class CardType extends AbstractType
{
    private $cardListRepository;
    private $security;

    public function __construct(Security $security, CardListRepository $cardListRepository)
    {
        $this->security = $security;
        $this->cardListRepository = $cardListRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => "Titre",
                'required' => true
            ])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Image' => "image",
                    'Musique' => "musique",
                    'Vidéo' => "video",
                ],
                'required' => true
            ])
            ->add('content', null, [
                'label' => "URL du contenu",
                'required' => true
            ])
            ->add('answer', null, [
                'label' => "Réponse",
                'required' => true
            ])
            ->add('cardList', null, [
                'label' => "Liste",
                'choices' => $this->cardListRepository->findByUser($this->security->getUser()->getId()),
                'help' => 'Tu peux sélectionner plusieurs listes ou aucune.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
