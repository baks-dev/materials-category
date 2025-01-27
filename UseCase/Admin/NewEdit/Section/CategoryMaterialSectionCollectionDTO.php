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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSectionInterface;
use BaksDev\Materials\Category\Type\Section\Id\CategoryMaterialSectionUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Fields\CategoryMaterialSectionFieldCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Trans\CategoryMaterialSectionTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CategoryMaterialSection */
final class CategoryMaterialSectionCollectionDTO implements CategoryMaterialSectionInterface
{
    #[Assert\Uuid]
    private ?CategoryMaterialSectionUid $id = null;


    /** Сортировка секции свойств продукта категории */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 100;

    /** Настройки локали секции */
    #[Assert\Valid]
    private ArrayCollection $translate;

    /** Коллекция свойств продукта в секции */
    #[Assert\Valid]
    private ArrayCollection $field;

    public function __construct()
    {
        $this->translate = new ArrayCollection();
        $this->field = new ArrayCollection();
    }

    /** Сортировка секции свойств продукта категории */

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }


    /** Настройки локали секции */

    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $SectionTransDTO = new CategoryMaterialSectionTransDTO();
            $SectionTransDTO->setLocal($locale);
            $this->addTranslate($SectionTransDTO);
        }

        return $this->translate;
    }

    public function addTranslate(CategoryMaterialSectionTransDTO $trans): void
    {
        if(empty($trans->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->translate->contains($trans))
        {
            $this->translate->add($trans);
        }
    }

    public function removeTranslate(CategoryMaterialSectionTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }

    /** Коллекция свойств продукта в секции */

    public function getField(): ArrayCollection
    {
        return $this->field;
    }

    public function addField(CategoryMaterialSectionFieldCollectionDTO $field): void
    {
        if(!$this->field->contains($field))
        {
            $this->field->add($field);
        }
    }

    public function removeField(CategoryMaterialSectionFieldCollectionDTO $field): void
    {
        $this->field->removeElement($field);
    }

}
