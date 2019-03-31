<?php

namespace App\Form;

use App\Entity\KeyWord;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class KeyWordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("word", TextType::class, [
            "label" => "Słowo: "
        ])
            ->add("products", EntityType::class, [
                "class" => "App:Product",
                "label" => "Wybierz pasujące produkty: ",
                "query_builder" => function (EntityRepository $er) {
                    return $er->createQueryBuilder("g")
                        ->orderBy("g.name", "DESC");
                },
                "choice_label" => "name",
                "multiple"=>true,
                "expanded"=>true,
                "by_reference" => false
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => KeyWord::class
            ]
        );
    }
}