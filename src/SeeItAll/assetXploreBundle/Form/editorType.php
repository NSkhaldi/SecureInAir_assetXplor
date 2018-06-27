<?php

namespace SeeItAll\assetXploreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class editorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function editorForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('input',   FileType::class)
                 ->add('undo',      ButtonType::class)
                 ->add('redo',      ButtonType::class)

                 ->add('rectangle',      ButtonType::class)
                 ->add('circle',      ButtonType::class)
                 ->add('text',      ButtonType::class)
                 ->add('arrow',      ButtonType::class)
                 ->add('pen',      ButtonType::class)

                 ->add('undo',      ButtonType::class)
                ->add('save',      SubmitType::class);
                 
                
      
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SeeItAll\assetXploreBundle\Entity\building'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'seeitall_assetxplorebundle_building';
    }


}
