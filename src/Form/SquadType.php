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

class SquadType extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('used_for', ChoiceType::class, [
                'choices' => [
                    'attack' => 'attack',
                    'defense' => 'defense'
                ],
                'invalid_message' => 'Seul les valeurs "attack" ou "defense" sont acceptées.'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'hero' => 'hero',
                    'ship' => 'ship'
                ],
                'invalid_message' => 'Seul les valeurs "hero" ou "ship" sont acceptées.'
            ])
            ->add('guild', EntityType::class, [
                'class' => Guild::class,
                'mapped' => false,
                'invalid_message' => 'Erreur lors de la récupération des informations de la guilde'
            ])
            ->add('units', ChoiceType::class, [
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->getUnitBaseId(),
                'invalid_message' => 'Erreur lors de la récupération de l\'unité'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Squad::class,
            'csrf_protection' => false
        ]);
    }

    private function getUnitBaseId()
    {
        $arrayReturn = array();
        $units = $this->entityManager->getRepository(Unit::class)
            ->findAll();
        foreach($units as $unit) {
            $arrayReturn[$unit->getBaseId()] = $unit->getBaseId();
        }
        return $arrayReturn;
    }
}
