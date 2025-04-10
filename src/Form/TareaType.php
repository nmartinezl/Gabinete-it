<?php

namespace App\Form;

use App\Entity\Tarea;
use App\Entity\Equipo;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TareaType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('fechaLimite', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha límite',
            ])
            ->add('prioridad', ChoiceType::class, [
                'choices' => [
                    'Alta' => 'alta',
                    'Media' => 'media',
                    'Baja' => 'baja',
                ],
                'placeholder' => 'Seleccionar prioridad',
                'label' => 'Prioridad',
            ])
            ->add('equipo', EntityType::class, [
                'class' => Equipo::class,
                'choice_label' => fn(Equipo $e) => $e->getTipo() . ' - ' . $e->getMarca(),
                'placeholder' => 'Seleccionar equipo',
                'label' => 'Equipo relacionado',
                'required' => false, //  esto lo vuelve opcional
            ]);

        $user = $this->security->getUser();
        if ($user && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $builder->add('tecnico', EntityType::class, [
                'class' => Usuario::class,
                'choice_label' => fn(Usuario $u) => $u->getEmail(), // cambiar por getNombreCompleto() si lo tenés
                'placeholder' => 'Seleccionar técnico',
                'label' => 'Técnico asignado',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarea::class,
        ]);
    }
}
