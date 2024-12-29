<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
            'label' => 'Email'
            ])
            ->add('roles', ChoiceType::class, [
            'choices' => [
            'Admin' => 'ROLE_ADMIN',
            'Utilisateur' => 'ROLE_USER',
            ],
            'multiple' => true,
            'expanded' => false,
            'label' => 'Rôles'
            ])
            ->add('password', null, [
            'label' => 'Mot de passe'
            ])
            ->add('firstName', null, [
            'label' => 'Prénom'
            ])
            ->add('name', null, [
            'label' => 'Nom'
            ])
            ->add('phone', null, [
            'label' => 'Téléphone'
            ])
            ->add('type', null, [
            'label' => 'Type'
            ])
            ->add('status', ChoiceType::class, [
            'choices' => [
            'Activé' => 0,
            'Désactivé' => 1,
            ],
            'expanded' => false,
            'label' => 'Statut'
            ])
            ->add('sex', ChoiceType::class, [
            'choices' => [
            'Homme' => 0,
            'Femme' => 1,
            ],
            'expanded' => false,
            'label' => 'Sexe'
            ])
            ->add('employee', ChoiceType::class, [
            'choices' => [
            'Non' => 0,
            'Oui' => 1,
            ],
            'expanded' => false,
            'label' => 'Employé'
            ])
            ->add('responsible', EntityType::class, [
            'class' => User::class,
            'choice_label' => function (User $user) {
            return $user->getFirstName() . ' ' . $user->getName();
            },
            'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($options) {
            return $er->createQueryBuilder('u')
                ->where('u.id != :current_user_id')
                ->setParameter('current_user_id', $options['current_user_id']);
            },
            'label' => 'Responsable'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'current_user_id' => null, // Ajout d'une option pour l'utilisateur courant
        ]);
    }
}
