<?php

namespace App\Forms;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationForm extends AbstractType
{
    function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Имя: '
            ])
            ->add('surname', TextType::class,[
                'label' => 'Фамилия: '
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон: '
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Возраст: '
            ])
            ->add('email', TextType::class, [
                'label' => 'Email: '
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль: '
            ])
            ->add('buttonRegistration', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}