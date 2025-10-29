<?php
namespace App\Form;
use App\Entity\Contacto;
use App\Entity\Provincia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactoFormType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options): void

    {

        $builder

            ->add('nombre')

            ->add('telefono')

            ->add('email', EmailType::class, array('label' => 'Correo electrónico'))

            ->add('provincia', EntityType::class, [

                'class' => Provincia::class,

                'choice_label' => 'nombre',

            ])

            ->add('save', SubmitType::class, array('label' => 'Enviar'));

        ;

    }



    public function configureOptions(OptionsResolver $resolver): void

    {

        $resolver->setDefaults([

            'data_class' => Contacto::class,

        ]);

    }

}
