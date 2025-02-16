<?php
// src/Form/CapteurType.php
namespace App\Form;

use App\Entity\Capteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CapteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du Capteur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', TextType::class, [
                'label' => 'Type de Capteur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valeur', TextType::class, [
                'label' => 'Valeur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('topic', TextType::class, [
                'label' => 'Topic MQTT',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Capteur::class,
        ]);
    }
}