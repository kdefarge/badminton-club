<?php

namespace App\Form;

use App\Entity\Encounter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('team1won', ChoiceType::class, [
                'choices' => [
                    'Non terminé' => null,
                    'Victoire équipe 1' => true,
                    'Victoire équipe 2' => false
                ],
                'label' => 'Rencontre terminé ?'
            ])->setDataMapper($this);
    }

    public function mapDataToForms($encounter, \Traversable $forms): void
    {
        if (null === $encounter)
            return;

        if (!$encounter instanceof Encounter)
            throw new UnexpectedTypeException($encounter, Encounter::class);

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['team1won']->setData($encounter->isTeam1Won());
    }

    public function mapFormsToData(\Traversable $forms, &$encounter): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        if (!$encounter instanceof Encounter)
            throw new UnexpectedTypeException($encounter, Encounter::class);

        $isTeam1Won = $forms['team1won']->getData();
        if (is_null($isTeam1Won)) {
            $encounter->setIsFinished(false);
        } else {
            $encounter->setIsTeam1Won($isTeam1Won);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('empty_data', null);
    }
}
