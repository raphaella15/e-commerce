<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    /**
     * Undocumented variable
     *
     * @var SluggerInterface
     */
    private $slugger;
    public  function __construct (SluggerInterface $slugger){
        $this->slugger = $slugger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextareaType::class)
            ->add('image',FileType::class,[
                'required' => false,
                'data_class' => null
            ])
            ->add('sold')
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event){
                /** @var Product */
                $product = $event->getData();
                if(null !== $productName = $product->getName()){
                    $product->setSlug($this->slugger->slug($productName));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'forms'
        ]);
    }
}
