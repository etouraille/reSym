<?php namespace Resource\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as builder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorizeFormType extends AbstractType
{
    public function buildForm(builder $builder, array $options)
    {
        $builder->add('allowAccess', 'checkbox', array(

        'label' => 'Allow access',

        ))
            ->add('authorize', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){

        $resolver->setDefaults(array(
            'data_class' => 'Resource\Bundle\UserBundle\Form\Model\Authorize'
        ));
    }

    public function getName()
    {
     return 'resource_oauth_server_authorize';
    }

}

?>