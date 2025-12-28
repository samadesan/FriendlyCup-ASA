<?php

namespace App\Form;

use App\Entity\Torneo;
use App\Entity\LigaFantasy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class LigaFantasyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('minimoJugadores', IntegerType::class, [
                'label' => 'Mínimo de jugadores en plantilla',
                'data' => 0,
                'attr' => ['class' => 'form-control']
            ])
            ->add('presupuestoInicial', IntegerType::class, [
            'mapped' => false, 
            'label' => 'Presupuesto inicial',
            'required' => true,
            ])
            ->add('segundosMercado', IntegerType::class, [
                'mapped' => false,
                'label' => 'Duración del mercado (segundos)',
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Ej: 3600 (1 hora)'
                ],
                'required' => true,
            ])
            ->add('crear', SubmitType::class, [
                'label' => 'Crear Competición',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigaFantasy::class,
        ]);
    }
}
