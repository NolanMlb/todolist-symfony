<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => false,
                'mapped'=>false,
                'attr' => array('accept' => 'image/jpeg,image/png,image/jpg')
            ])
            ->add('status',ChoiceType::class, [
                'placeholder' => 'Sélectionnez le statut de votre tâche',
                'choices'=>[
                    'À faire'=>'À faire',
                    'En cours'=>'En cours',
                    'Effectué'=>'Effectué'
                ],
                'required' => true,
            ])
            ->add('endDate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
