<?php
namespace Trivia\MessengerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Trivia\MessengerBundle\Entity\Users;

class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper){
        $formMapper
            ->add('username')
            ->add('email')
            ->add('password')
            ->add('emailToken', null, array('required' => false))
        ;
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('password')
            ->add('emailToken')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('password')
            ->add('emailToken')
            ;
    }

}