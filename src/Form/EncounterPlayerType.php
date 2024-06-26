<?php

namespace App\Form;

use App\Entity\EncounterPlayer;
use App\Entity\Player;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterPlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player', EntityType::class, [
                'class' => Player::class,
                'placeholder' => 'Prénom et nom du joueur',
                'autocomplete' => true,
            ])
            ->add('isTeam1', ChoiceType::class, [
                'choices' => [
                    'Equipe 1' => true,
                    'Equipe 2' => false
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EncounterPlayer::class,
        ]);
    }
}
