<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\FormListenerFunction;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function __construct(private FormListenerFunction $formListenerFunction)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('summary')
            ->add('content')
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFunction->cleanInputText('title'))
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFunction->cleanInputText('summary'))
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFunction->cleanInputText('content'))
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFunction->autoSlug());
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
