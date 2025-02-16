<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Parcelle;

class ParcelleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la parcelle',
            ])
            ->add('superficie', TextType::class, [
                'label' => 'Superficie (ha)',
            ])
            ->add('typeCulture', TextType::class, [
                'label' => 'Type de culture',
                'required' => false,
            ])
            ->add('nombreCapteurs', IntegerType::class, [
                'label' => 'Nombre de capteurs',
                'required' => true, // Assurez-vous qu'il soit toujours rempli
                'mapped' => true, // Il est bien mappé à l'entité
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parcelle::class,
        ]);
    }
}
