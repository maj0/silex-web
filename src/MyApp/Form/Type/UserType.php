<?php

namespace MyApp\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as Type;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //echo "<pre>Passord:",json_encode($options),"</pre></br>\n";
        $builder
            ->add('name', Type\TextType::class, array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('address', Type\TextareaType::class, array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('email', Type\EmailType::class, array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('telephone')
            ->add('employeeID')
            ->add('role')
            ->add('organisationID')
            ->add('birthdate')
            ->add('probation', Type\CheckboxType::class, array(
                'required' => false,
            ))
            ->add('password', Type\PasswordType::class, array(
               'constraints' => new Assert\NotBlank(),
            ))
            ->add('save', Type\SubmitType::class);
    }

    public function getName()
    {
        return 'user';
    }
}
