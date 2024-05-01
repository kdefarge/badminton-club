<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlayerListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player', EntityType::class, [
                'class' => Player::class,
                'placeholder' => 'Prénom et nom du joueur',
                'autocomplete' => true,
            ]);
    }
}
