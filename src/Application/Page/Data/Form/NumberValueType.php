<?php

namespace App\Application\Page\Data\Form;

use App\Application\Page\Data\Dto\NumberValueDTO;
use App\Application\Page\Data\Enum\ValueSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NumberValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('source', EnumType::class, ['class' => ValueSource::class, 'expanded' => true])
            ->add('value', IntegerType::class, ['required' => false])
            ->add('sourceKey', null, ['label' => 'Clé de source', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => NumberValueDTO::class]);
    }
}
