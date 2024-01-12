<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\MeetingRoom;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReservationDeleteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('cancelReason', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer le motif d\'annulation',
                    ]),
                ],
                'attr' => ['placeholder' => 'Indiquez le motif d\'annulation'],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
