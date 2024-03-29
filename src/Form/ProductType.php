<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\KeyWord;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name", TextType::class, [
            "label" => "Nazwa produktu: "
        ])
            ->add("price", MoneyType::class,
                [
                    "label" => "Cena ",
                    "currency" => "PLN",
                    "grouping" => true,
                    "scale" => 2
                ])
            ->add("description", TextareaType::class, [
                "label" => "Opis: "
            ])
            ->add("category", null,
                [
                    "placeholder" => "Wybierz kategorie",
                    "label" => "Kategoria: "
                ])
                ->add("keyWords", EntityType::class, [
                    "class" => "App:KeyWord",
                    "label" => "Wybierz słowa kluczowe: ",
                    "query_builder" => function (EntityRepository $er) {
                        return $er->createQueryBuilder("g")
                            ->orderBy("g.word", "DESC");
                    },
                    "choice_label" => "word",
                    "multiple"=>true,
                    "expanded"=>true,
                    "by_reference" => false
                ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => Product::class
            ]
        );
    }
}