<?php

namespace App\Form;

use App\Entity\CardList;
use App\Repository\CardListRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CardListType extends AbstractType
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
            ->add('name', null, [
                'required' => true
            ])
            ->add('description', null, [
                'required' => true
            ])
            ->add('parent', EntityType::class, [
                'class' => CardList::class,
                'required' => false,
                'choices' => $this->cardListRepository->findByUser($this->security->getUser()->getId())
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CardList::class,
        ]);
    }
}
