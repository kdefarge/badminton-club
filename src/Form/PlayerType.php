<?php

namespace App\Form;

use App\Config\PlayerGender;
use App\Config\PlayerSkill;
use App\Entity\Gender;
use App\Entity\Player;
use App\Entity\Skill;
use App\Repository\GenderRepository;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GenderRepository $genderRepository,
        private SkillRepository $skillRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('gender', EnumType::class, ['class' => PlayerGender::class, 'required' => false])
            ->add('skill', EnumType::class, ['class' => PlayerSkill::class, 'required' => false]);
        $builder->get('gender')->addModelTransformer(new CallbackTransformer(
            function ($gender): ?PlayerGender {
                if ($gender instanceof Gender)
                    return PlayerGender::tryFrom($gender->getName());
                return null;
            },
            function ($playerGender): ?Gender {
                if (!$playerGender instanceof PlayerGender)
                    return null;

                $gender = $this->genderRepository->findOneby(['name' => $playerGender->value]);
                if ($gender instanceof Gender)
                    return $gender;

                $gender = new Gender();
                $gender->setName($playerGender->value);
                $this->entityManager->persist($gender);

                return $gender;
            }
        ));
        $builder->get('skill')->addModelTransformer(new CallbackTransformer(
            function ($skill): ?PlayerSkill {
                if ($skill instanceof Skill)
                    return PlayerSkill::tryFrom($skill->getName());
                return null;
            },
            function ($playerSkill): ?Skill {
                if (!$playerSkill instanceof PlayerSkill)
                    return null;

                $skill = $this->skillRepository->findOneby(['name' => $playerSkill->value]);
                if ($skill instanceof Skill)
                    return $skill;

                $skill = new Skill();
                $skill->setName($playerSkill->value);
                $this->entityManager->persist($skill);

                return $skill;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
        ]);
    }
}
