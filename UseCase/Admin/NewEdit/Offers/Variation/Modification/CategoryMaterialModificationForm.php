<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification;

use BaksDev\Core\Services\Reference\ReferenceChoice;
use BaksDev\Core\Services\Reference\ReferenceChoiceInterface;
use BaksDev\Core\Type\Field\InputField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryMaterialModificationForm extends AbstractType
{
    public function __construct(
        private readonly ReferenceChoice $reference,
        private readonly TranslatorInterface $translator
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /** Торговое предложение - Справочник */
        $builder->add('isReference', CheckboxType::class, ['mapped' => false, 'required' => false,]);


        /** Если ранее выбран справочник - выделяем чекбокс  */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $material = $event->getData();
            $form = $event->getForm();

            if($material && $material->getReference() !== null)
            {
                $form->add(
                    'isReference',
                    CheckboxType::class,
                    ['mapped' => false, 'required' => false, 'data' => true,]
                );
            }
        });


        /** Справочники */
        $builder->add(
            'reference',
            ChoiceType::class,
            [
                'required' => false,
                'choices' => $this->reference->getReference(),
                'choice_value' => function($choice) {
                    return $choice instanceof ReferenceChoiceInterface ? $choice?->type() : $choice;
                },
                'choice_label' => function($choice) {
                    return $this->translator->trans('label', domain: $choice->domain());
                },
            ]
        );

        $builder->get('reference')->addModelTransformer(
            new CallbackTransformer(
                function($type) {
                    return $type; // instanceof FieldsChoiceInterface ? $type->type() : $type;
                },
                function($type) {
                    return $type instanceof ReferenceChoiceInterface ? new InputField($type) : null;
                }
            )
        );

        //		$builder->add
        //		(
        //			'reference',
        //			ChoiceType::class,
        //			[
        //				'required' => false,
        //				'choices' => $this->reference->getReference(),
        //				'choice_value' => function($choice) {
        //					return $choice instanceof ReferenceChoiceInterface ? $choice?->type() : $choice;
        //				},
        //				'choice_label' => function($choice) {
        //					return $this->translator->trans('label', domain: $choice->domain());
        //				},
        //			]
        //		);
        //
        //		$builder->get('reference')->addModelTransformer(
        //			new CallbackTransformer(
        //				function($reference) {
        //					return $reference;
        //				},
        //				function($reference) {
        //					return $reference instanceof ReferenceChoiceInterface ? $reference->type() : $reference;
        //				}
        //			)
        //		);


        $builder->add('image', CheckboxType::class, ['required' => false]);

        $builder->add('price', CheckboxType::class, ['required' => false]);

        $builder->add('quantitative', CheckboxType::class, ['required' => false]);

        $builder->add('article', CheckboxType::class, ['required' => false]);

        /* Offers Trans */
        $builder->add('translate', CollectionType::class, [
            'entry_type' => Trans\CategoryMaterialModificationTransForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__modification_translate__',
        ]);

        /** Флаг, то что торговое предложение имеет множественные варианты
         * !!! Должен распологаться ниже свойства translate
         */
        $builder->add('modification', CheckboxType::class, ['required' => false, 'label' => false]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryMaterialModificationDTO::class,
        ]);
    }
}
