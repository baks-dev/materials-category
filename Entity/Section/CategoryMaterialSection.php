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

namespace BaksDev\Materials\Category\Entity\Section;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Type\Section\Id\CategoryMaterialSectionUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Секуция торгового предложения */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_section')]
class CategoryMaterialSection extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialSectionUid::TYPE)]
    private readonly CategoryMaterialSectionUid $id;

    /** Связь на событие */
    #[ORM\ManyToOne(targetEntity: CategoryMaterialEvent::class, inversedBy: "section")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id', nullable: true)]
    private ?CategoryMaterialEvent $event;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: Trans\CategoryMaterialSectionTrans::class, mappedBy: 'section', cascade: ['all'])]
    private Collection $translate;

    /** Поля секции */
    #[ORM\OneToMany(targetEntity: Field\CategoryMaterialSectionField::class, mappedBy: 'section', cascade: ['all'])]
    #[ORM\OrderBy(['sort' => 'ASC'])]
    private Collection $field;

    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false, options: ['default' => 100])]
    private int $sort = 100;

    public function __construct(CategoryMaterialEvent $event)
    {
        $this->id = new CategoryMaterialSectionUid();
        $this->event = $event;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): CategoryMaterialSectionUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialSectionInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialSectionInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}
