<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('image')
            ->add('time', ChoiceType::class, [
                "choices"=> array(
                  '5 minutes' => 5,
                  '10 minutes' => 10,
                  '15 minutes' => 15,
                  '20 minutes' => 20,
                  '25 minutes' => 25,
                  '30 minutes' => 30,
                )
              ])
            ->add('difficulty', ChoiceType::class, [
                "choices"=> array(
                  'Facile' => 'Facile',
                  'Moyenne' => 'Moyenne',
                  'Difficile' => 'Difficile',
                )
              ])
            ->add('favorite', ChoiceType::class, [
                "choices"=> array(
                  'Oui' => '1',
                  'Non' => '0',
                )
              ])
            ->add('portions', ChoiceType::class, [
                "choices"=> array(
                  '1 portion' => 1,
                  '2 portions' => 2,
                  '3 portions' => 3,
                  '4 portions' => 4,
                  '5 portions' => 5,
                )
              ])
            ->add('category', EntityType::class, [
                "class" => Category::class,
                "choice_label" => 'title',
              ])
             ->add('save', SubmitType::class)                    
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
