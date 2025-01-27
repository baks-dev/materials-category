<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit;

use BaksDev\Materials\Category\Repository\ParentCategoryChoiceForm\ParentCategoryMaterialChoiceInterface;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\CategoryMaterialOffersDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryMaterialForm extends AbstractType
{
    private ParentCategoryMaterialChoiceInterface $categoryParent;

    public function __construct(ParentCategoryMaterialChoiceInterface $categoryParent)
    {
        $this->categoryParent = $categoryParent;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**  Сортировка категории */
        $builder->add('sort', IntegerType::class);

        /** Обложка категории */
        $builder->add('cover', NewEdit\Cover\CategoryMaterialCoverForm::class);

        /** Неизменяемые свойства категории */
        $builder->add('info', NewEdit\Info\CategoryMaterialInfoForm::class);


        /** Идентификатор родительской категории */
        $builder->add(
            'parent',
            ChoiceType::class,
            [
                'label' => false,
                'required' => false,
                'choices' => $this->categoryParent->findAll(),
                'choice_value' => function(?ParentCategoryMaterialUid $type) {
                    return $type?->getValue();
                },
                'choice_label' => function(ParentCategoryMaterialUid $type) {
                    return (is_int($type->getLevel()) ? str_repeat(' - ', $type->getLevel() - 1) : '').$type->getOption();
                },
            ]
        );


        /** Настройки локали категории */
        $builder->add('translate', CollectionType::class, [
            'entry_type' => NewEdit\Trans\CategoryMaterialTransForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_translate__',
        ]);

        /** Настройки SEO категории */
        $builder->add('seo', CollectionType::class, [
            'entry_type' => NewEdit\Seo\CategoryMaterialSeoCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_seo__',
        ]);

        /** Посадочные блоки */
        $builder->add('landing', CollectionType::class, [
            'entry_type' => NewEdit\Landing\CategoryMaterialLandingCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_landing__',
        ]);

        /** Секции свойств продукта категории */
        $builder->add('section', CollectionType::class, [
            'entry_type' => NewEdit\Section\CategoryMaterialSectionCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_section__',
        ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {

            /** @var CategoryMaterialDTO $data */
            $data = $event->getData();

            if($data->getOffer() === null)
            {
                $data->setOffer(new CategoryMaterialOffersDTO());
            }

        });


        /** Товары в категории с торговым предложением */
        $builder->add('offer', NewEdit\Offers\CategoryMaterialOffersForm::class, ['label' => false]);


        /* Сохранить ******************************************************/
        $builder->add(
            'Save',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => CategoryMaterialDTO::class,
                'method' => 'POST',
                'attr' => ['class' => 'w-100'],
            ]
        );
    }

}
