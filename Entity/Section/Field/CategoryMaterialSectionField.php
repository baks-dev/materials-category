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

namespace BaksDev\Materials\Category\Entity\Section\Field;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSection;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Поля секции */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_section_field')]
class CategoryMaterialSectionField extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialSectionFieldUid::TYPE)]
    private readonly CategoryMaterialSectionFieldUid $id;

    /** Связь на секцию */
    #[ORM\ManyToOne(targetEntity: CategoryMaterialSection::class, inversedBy: "field")]
    #[ORM\JoinColumn(name: 'section', referencedColumnName: 'id', nullable: true)]
    private ?CategoryMaterialSection $section;

    /** ID */
    #[ORM\Column(type: CategoryMaterialSectionFieldUid::TYPE, nullable: true)]
    private CategoryMaterialSectionFieldUid $const;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: Trans\CategoryMaterialSectionFieldTrans::class, mappedBy: 'field', cascade: ['all'])]
    private Collection $translate;

    /** Тип поля (input, select, textarea ....)  */
    #[ORM\Column(type: InputField::TYPE, length: 32, options: ['default' => 'input_field'])]
    private InputField $type;

    /** Публичное свойство */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $public = true;

    /** Обязательное к заполнению */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $required = true;

    /** Учавствует в названии */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $name = false;

    /** Учавствует в фильтре */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $filter = false;

    /** Учавствует в фильтре альтернативных товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $alternative = false;

    /** Отображается на фото в карточке */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $photo = false;

    /** Учавствует в превью карточки */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $card = false;

    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 100])]
    private int $sort = 100;

    public function __construct(CategoryMaterialSection $section)
    {
        $this->id = new CategoryMaterialSectionFieldUid();
        $this->section = $section;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): CategoryMaterialSectionFieldUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialSectionFieldInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialSectionFieldInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    //    public function removeElement() : void
    //    {
    //        $this->section = null;
    //    }
    //
    //
    //    public function equals($dto) : bool
    //    {
    //        if($dto instanceof FieldInterface)
    //        {
    //            return $this->id === $dto->getEquals();
    //        }
    //
    //        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    //    }
    //


    //    public function updSectionField(FieldInterface $field) : void
    //    {
    //        if(property_exists($field, 'trans'))
    //        {
    //            foreach($field->trans as $trans)
    //            {
    //                $categorySectionTrans = new Field\Trans($this, $trans->local);
    //                $categorySectionTrans->updFieldTrans($trans);
    //                $this->addTrans($categorySectionTrans);
    //            }
    //        }
    //
    //        if(property_exists($field, 'type'))
    //        {
    //            $this->type = $field->type;
    //        }
    //
    //        if(property_exists($field, 'public'))
    //        {
    //            $this->public = $field->public;
    //        }
    //
    //        if(property_exists($field, 'required'))
    //        {
    //            $this->required = $field->required;
    //        }
    //
    //        if(property_exists($field, 'sort'))
    //        {
    //            $this->sort = $field->sort;
    //        }
    //    }
    //
    //    /** Добавляем перевод поля
    //     * @param Section\Trans $trans
    //     * @return void
    //     */
    //    public function addTrans(Field\Trans $trans) : void
    //    {
    //        if(!$this->trans->contains($trans))
    //        {
    //            $this->trans[] = $trans;
    //        }
    //    }
    //
    //
    //
    //
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getTrans() : Collection
    //    {
    //        /* Вычисляем расхождение и добавляем неопределенные локали */
    //        foreach(Locale::diffLocale($this->trans) as $locale)
    //        {
    //            $this->addTrans(new Field\Trans($this, $locale));
    //        }
    //
    //        return $this->trans;
    //    }
    //
    //
    //    /** Добавляем перевод категории
    //     * @param Trans $trans
    //     * @return void
    //     */
    //    public function addTrans(Field\Trans $trans) : void
    //    {
    //        if(!$this->trans->contains($trans))
    //        {
    //            $this->trans[] = $trans;
    //        }
    //    }
    //
    //

    /**
     * @param SectionUid $section
     */
    /**
     * @param SectionUid $section
     */
    //    public function __construct(Section|SectionUid $section) {
    //        $this->id = new FieldUid();
    //        $this->section = $section instanceof Section ? $section->getId() : $section;
    //    }

    //    /**
    //     * @param int $sort
    //     * @param InputField $type
    //     * @param bool $public
    //     * @param bool $required
    //     */
    //    public function addSectionField(InputField $type, int $sort = 100, bool $public = true, bool $required = true)
    //    {
    //        $this->sort = $sort;
    //        $this->type = $type;
    //        $this->public = $public;
    //        $this->required = $required;
    //    }
    //
    //    /**
    //     * @return FieldUid
    //     */
    //    public function getId() : FieldUid
    //    {
    //        return $this->id;
    //    }

}
