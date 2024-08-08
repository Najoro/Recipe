<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title' , TextType::class,[
                "required" => false,
            ])
            ->add('slug' , TextType::class)
            ->add('content', TextareaType::class)
            ->add('durations', IntegerType::class)
            ->add('save' , SubmitType::class ,  [
                'label' => "Creer"
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT ,$this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->generateDateTime(...) )
        ;
    }

    private function autoSlug(PreSubmitEvent $event) : void {
        $data = $event->getData();
        if(empty($data["slug"])){
            $slugger = new AsciiSlugger();
            $data["slug"] = strtolower($slugger->slug($data["title"]));
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
            'data_class' => Recipe::class,
        ]);
    }
}
