<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => $this->translator->trans('users.property.first_name'),
                'attr' => [],
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->translator->trans('users.property.last_name'),
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('users.property.email'),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => $this->translator->trans('users.form.password.first'),
                    'attr' => [],
                ],
                'second_options' => [
                    'label' => $this->translator->trans('users.form.password.second'),
                    'attr' => [],
                ],
                'required' => 'add' === $options['form_mode'],
            ])
            ->add('role', ChoiceType::class, [
                'label' => $this->translator->trans('users.property.role'),
                'choices' => UserRole::cases(),
                'choice_label' => fn (UserRole $role) => $this->translator->trans($role->label()),
                'choice_value' => fn (?UserRole $role) => $role?->value,
                'placeholder' => $this->translator->trans('users.form.role.placeholder'),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'form_mode' => 'add',
        ]);
    }
}
