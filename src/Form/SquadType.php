<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Guild;
use App\Entity\Squad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SquadType extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add(
                'used_for',
                ChoiceType::class,
                [
                    'choices' => [
                        'attack' => 'attack',
                        'defense' => 'defense'
                    ],
                    'invalid_message' => 'Seul les valeurs "attack" ou "defense" sont acceptées.'
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'hero' => 'hero',
                        'ship' => 'ship'
                    ],
                    'invalid_message' => 'Seul les valeurs "hero" ou "ship" sont acceptées.'
                ]
            )
            ->add(
                'guild',
                EntityType::class,
                [
                    'class' => Guild::class,
                    'mapped' => false,
                    'invalid_message' => 'Erreur lors de la récupération des informations de la guilde'
                ]
            )
            /*// Voir pour mettre une modif pré/post validation car le multiselect respecte pas l'ordre qui a été reçu lors du call API
            // Event pre-set data qui permet d'ajouter le champ en mode dynamic et dont les éléments correspondent aux units qu'on a pu trouver via les datas passées par l'API ?
            ->add('units', ChoiceType::class, [
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->getUnitBaseId(),
                'invalid_message' => 'Erreur lors de la récupération de l\'unité'
            ])*/
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $data = $event->getData();
                    $form = $event->getForm();
                    $unitRepository = $this->entityManager
                        ->getRepository(Unit::class);
                    $choiceUnitsOption = array();
                    if (!empty($data['units']) > 0) {
                        foreach ($data['units'] as $unitDesiredBaseId) {
                            $unit = $unitRepository->findOneBy(
                                [
                                    'base_id' => $unitDesiredBaseId
                                ]
                            );
                            if (!empty($unit)) {
                                $choiceUnitsOption[$unit->getBaseId()] = $unit->getBaseId();
                            }
                        }

                        if (count($choiceUnitsOption) > 0) {
                            $form->add(
                                'units', ChoiceType::class, [
                                    'mapped' => false,
                                    'multiple' => true,
                                    'expanded' => true,
                                    'choices' => $choiceUnitsOption,
                                    'invalid_message' => 'Erreur lors de la récupération de l\'unité'
                                ]
                            );
                        }
                    }
                }
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Squad::class,
                'csrf_protection' => false
            ]
        );
    }

    private function getUnitBaseId()
    {
        $arrayReturn = array();
        $units = $this->entityManager->getRepository(Unit::class)
            ->findAll();
        foreach ($units as $unit) {
            $arrayReturn[$unit->getBaseId()] = $unit->getBaseId();
        }
        return $arrayReturn;
    }
}
