<?php
/**
 * Created by PhpStorm.
 * User: Paulisse
 * Date: 25/09/2015
 * Time: 11:23
 */
namespace Resource\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as builder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class LoginType extends AbstractType
{
    public function buildForm(builder $builder, array $options) {

        $builder->add('username', 'text', array(
            'attr' => array('name' => '_username')
            ))
                ->add('password','text', array(
            'attr' => array('name' => '_password')
            ))
                ->add('target_path','hidden', array(
                'attr' => array('name' => '_target_path')
            ));
        $builder->setAction('resource_oauth_server_auth_login_check')
                ->setMethod('POST');
    }

    public function getName(){

        return 'Login';
    }
}







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