<?php

namespace App\Form;

use DateTimeImmutable;
use App\Entity\Category;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class , [
                "label" => "name",

            ])
            ->add('slug', TextType::class , [
                "label" => "slug",
                "required" => false,
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('save', SubmitType::class , [
                'label' => "enregistrer",
                'attr' => [
                    'class' => "btn btn-success"
                ]
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT ,$this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->generateDateTime(...) )
        ;
    }
    private function autoSlug(PreSubmitEvent $event) : void {
        $data = $event->getData();
        if(empty($data["slug"])){
            $slugger = new AsciiSlugger();
            $data["slug"] = strtolower($slugger->slug($data["name"]));
            $event->setData($data);
        }
    }
    private function generateDateTime(PostSubmitEvent $event) : void
    {
        $data = $event->getData();
        
        $data->setUpdatedAt(new DateTimeImmutable());
        if(!$data->getId())
        {
            $data->setCreatedAt(new DateTimeImmutable());
        }

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
