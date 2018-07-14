<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array('required' => true, 'label' => 'First Name'))
            ->add('lastName', TextType::class, array('required' => true, 'label' => 'Last Name'))
            ->add('email', TextType::class, array('required' => true, 'label' => 'Email'))
            ->add('amount', TextType::class, array('required' => true, 'label' => 'Amount'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payment',
        ));
    }

    public function getName()
    {
        return 'payment';
    }
}