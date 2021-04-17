<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => "Titre"
            ])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Image' => "image",
                    'Musique' => "musique",
                    'Vidéo' => "video",
                ],
            ])
            ->add('content', null, [
                'label' => "URL du contenu"
            ])
            ->add('answer', null, [
                'label' => "Réponse"
            ])
            ->add('cardList', null, [
                'label' => "Liste"
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
