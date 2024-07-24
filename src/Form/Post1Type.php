<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Form\FormListenerFunction;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Post1Type extends AbstractType
{
    public function __construct(private FormListenerFunction $formListenerFunction)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('summary')
            ->add('slug')
            ->add('content')
            ->add('published_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
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
