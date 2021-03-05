<?php

namespace App\Form;

use App\Entity\Dialogs;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class DialogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('user', Select2EntityType::class, [
                'mapped' => false,
                'multiple' => false ,
                'allow_clear' => true,
                'placeholder' => 'Найдите пользователя по email',
                'class' => 'App\Entity\User',
                'remote_route' => 'ajax_users_list',
                'primary_key' => 'id',
                'text_property' => 'email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dialogs::class,
        ]);
    }
}
