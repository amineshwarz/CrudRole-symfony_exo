<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('nom')
            ->add('prenom');

            if ($options['allow_roles_edit'] ?? false){
                $builder->add('roles', ChoiceType::class, [
                    'label' => 'Rôles Utilisateur',
                    'choices' => [   
                        'Administrateur' => 'ROLE_ADMIN',
                        // Autres rôles...
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ]);
            }
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_roles_edit' => false, // Par défaut, l'édition des rôles est désactivée
        ]);
    }
}
