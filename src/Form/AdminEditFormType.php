<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('photo', FileType::class, [
                'label' => 'Photo (Image)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'veuillez uploader une image valide (JPEG, PNG, GIF).',
                    ]),
                ],
            ]);

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
