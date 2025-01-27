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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Fields;

use BaksDev\Core\Type\Field\InputField;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Section\Field\CategoryMaterialSectionFieldInterface;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\CategoryMaterialSectionFieldTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CategoryMaterialSectionField */
final class CategoryMaterialSectionFieldCollectionDTO implements CategoryMaterialSectionFieldInterface
{
    #[Assert\Uuid]
    private ?CategoryMaterialSectionFieldUid $const = null;

    /** Сортировка поля в секции */
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 100;

    /** Тип поля (input, select, textarea ....) */
    #[Assert\NotBlank]
    private InputField $type;

    /** Публичное свойство */
    private bool $public = false;

    /** Обязательное к заполнению */
    private bool $required = false;

    /** Участвует в фильтре */
    private bool $filter = false;

    /** Участвует в превью карточки */
    private bool $card = false;

    /** Участвует в названии */
    private bool $name = false;

    /** Участвует в фильтре альтернативных товаров */
    private bool $alternative = false;

    /** Отображать на фото в карточке */
    private bool $photo = false;

    /** Настройки локали */
    #[Assert\Valid]
    private ArrayCollection $translate;


    public function __construct()
    {
        $this->translate = new  ArrayCollection();
    }

    /**
     * Const
     */
    public function getConst(): ?CategoryMaterialSectionFieldUid
    {
        $this->const ?: $this->const = new CategoryMaterialSectionFieldUid();

        return $this->const;
    }


    /** Сортировка поля в секции */

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /** Тип поля (input, select, textarea ....) */

    public function getType(): InputField
    {
        return $this->type;
    }

    public function setType(InputField $type): void
    {
        $this->type = $type;
    }

    /** Публичное свойство */

    public function getPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }


    /** Обязательное к заполнению */

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }


    /** Учавствует в фильтре */

    public function getFilter(): bool
    {
        return $this->filter;
    }

    public function setFilter(bool $filter): void
    {
        $this->filter = $filter;
    }


    /** Настройки локали */

    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $SectionFieldTransDTO = new CategoryMaterialSectionFieldTransDTO();
            $SectionFieldTransDTO->setLocal($locale);
            $this->addTranslate($SectionFieldTransDTO);
        }

        return $this->translate;
    }


    public function addTranslate(CategoryMaterialSectionFieldTransDTO $trans): void
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

    public function removeTranslate(CategoryMaterialSectionFieldTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }


    /** Учавствует в превью карточки */

    public function getCard(): bool
    {
        return $this->card;
    }

    public function setCard(bool $card): void
    {
        $this->card = $card;
    }


    public function getName(): bool
    {
        return $this->name;
    }


    public function setName(bool $name): void
    {
        $this->name = $name;
    }


    /** Учавствует в фильтре альтернативных товаров */


    public function getAlternative(): bool
    {
        return $this->alternative;
    }

    public function setAlternative(bool $alternative): void
    {
        $this->alternative = $alternative;
    }

    /** Отображать на фото в карточке */

    public function getPhoto(): bool
    {
        return $this->photo;
    }


    public function setPhoto(bool $photo): void
    {
        $this->photo = $photo;
    }


}
