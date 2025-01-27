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

namespace BaksDev\Materials\Category\Entity\Offers\Variation;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Variation\Modification\CategoryMaterialModification;
use BaksDev\Materials\Category\Type\Offers\Variation\CategoryMaterialVariationUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/*/ Множественные варианты в торговом предложении */


#[ORM\Entity]
#[ORM\Table(name: 'material_category_variation')]
class CategoryMaterialVariation extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialVariationUid::TYPE)]
    private readonly CategoryMaterialVariationUid $id;

    /** ID торгового предложения */
    #[ORM\OneToOne(targetEntity: CategoryMaterialOffers::class, inversedBy: 'variation')]
    #[ORM\JoinColumn(name: 'offer', referencedColumnName: 'id')]
    private CategoryMaterialOffers $offer;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: Trans\CategoryMaterialVariationTrans::class, mappedBy: 'variation', cascade: ['all'])]
    private Collection $translate;

    /** Справочник */
    #[ORM\Column(type: InputField::TYPE, length: 32, nullable: true, options: ['default' => 'input'])]
    private ?InputField $reference = null;

    /** Загрузка пользовательских изображений */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $image = false;

    /** Вариант с ценой */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $price = false;

    /** Количественный учет товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $quantitative = false;

    /** Вариант с артикулом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $article = false;

    /** Вариант с постфиксом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $postfix = false;

    /** Модификации в множественном варианте */
    #[ORM\OneToOne(targetEntity: CategoryMaterialModification::class, mappedBy: 'variation', cascade: ['all'])]
    private ?CategoryMaterialModification $modification;


    public function __construct(CategoryMaterialOffers $offer)
    {
        $this->id = new CategoryMaterialVariationUid();
        $this->offer = $offer;
    }


    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Id
     */
    public function getId(): CategoryMaterialVariationUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialVariationInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialVariationInterface || $dto instanceof self)
        {
            if($dto->isVariation())
            {
                return parent::setEntity($dto);
            }

            return false;
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}
