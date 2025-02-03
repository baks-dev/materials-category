<?php

/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Materials\Category\Entity\Event;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Cover\CategoryMaterialCover;
use BaksDev\Materials\Category\Entity\Domains\CategoryMaterialDomain;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Entity\Landing\CategoryMaterialLanding;
use BaksDev\Materials\Category\Entity\Modify\CategoryMaterialModify;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSection;
use BaksDev\Materials\Category\Entity\Seo\CategoryMaterialSeo;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* События Category */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_event')]
#[ORM\Index(columns: ['category'])]
#[ORM\Index(columns: ['parent'])]
class CategoryMaterialEvent extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialEventUid::TYPE)]
    private readonly CategoryMaterialEventUid $id;

    /** ID Category */
    #[ORM\Column(type: CategoryMaterialUid::TYPE, nullable: false)]
    private ?CategoryMaterialUid $category = null;

    /** ID родительской Category */
    #[ORM\Column(type: ParentCategoryMaterialUid::TYPE, nullable: true)]
    private ?ParentCategoryMaterialUid $parent = null;

    /** Cover */
    #[ORM\OneToOne(targetEntity: CategoryMaterialCover::class, mappedBy: 'event', cascade: ['all'])]
    private ?CategoryMaterialCover $cover = null;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: CategoryMaterialTrans::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $translate;

    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
    private int $sort = 500;

    /** Торговые предложения */
    #[ORM\OneToOne(targetEntity: CategoryMaterialOffers::class, mappedBy: 'event', cascade: ['all'])]
    private ?CategoryMaterialOffers $offer;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: CategoryMaterialModify::class, mappedBy: 'event', cascade: ['all'])]
    private CategoryMaterialModify $modify;

    public function __construct(?ParentCategoryMaterialUid $parent = null)
    {
        $this->id = new CategoryMaterialEventUid();
        $this->modify = new CategoryMaterialModify($this);
        $this->parent = $parent;
    }


    public function __toString(): string
    {
        return $this->id;
    }

    public function getMain(): CategoryMaterialUid
    {
        return $this->category;
    }


    public function getId(): CategoryMaterialEventUid
    {
        return $this->id;
    }

    public function getNameByLocale(Locale $locale): ?string
    {
        $name = null;

        /** @var CategoryMaterialTrans $trans */
        foreach($this->translate as $trans)
        {
            if($name = $trans->getNameByLocale($locale))
            {
                break;
            }
        }

        return $name;
    }


    public function getCategory(): ?CategoryMaterialUid
    {
        return $this->category;
    }


    public function setMain(CategoryMaterial|CategoryMaterialUid $category): void
    {
        $this->category = $category instanceof CategoryMaterial ? $category->getId() : $category;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getUploadCover(): CategoryMaterialCover
    {
        return $this->cover ?: $this->cover = new CategoryMaterialCover($this);
    }

}
