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

namespace BaksDev\Materials\Category\Entity\Offers\Variation\Modification;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Type\Offers\Modification\CategoryMaterialModificationUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/*/ Множественные варианты в торговом предложении */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_modification')]
class CategoryMaterialModification extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialModificationUid::TYPE)]
    private readonly CategoryMaterialModificationUid $id;

    /** ID множественного варианта */
    #[ORM\OneToOne(targetEntity: CategoryMaterialVariation::class, inversedBy: 'modification')]
    #[ORM\JoinColumn(name: 'variation', referencedColumnName: 'id')]
    private CategoryMaterialVariation $variation;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: Trans\CategoryMaterialModificationTrans::class, mappedBy: 'modification', cascade: ['all'])]
    private Collection $translate;

    /** Справочник */
    #[ORM\Column(type: InputField::TYPE, length: 32, nullable: true, options: ['default' => 'input'])]
    private ?InputField $reference = null;

    /** Загрузка пользовательских изображений */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $image = false;

    /** Модификация с ценой */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $price = false;

    /** Количественный учет товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $quantitative = false;

    /** Модификация с артикулом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $article = false;

    /** Модификация с постфиксом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $postfix = false;


    public function __construct(CategoryMaterialVariation $variation)
    {
        $this->id = new CategoryMaterialModificationUid();
        $this->variation = $variation;

    }

    public function __toString(): string
    {
        return (string) $this->id;
    }


    public function getId(): CategoryMaterialModificationUid
    {
        return $this->id;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialModificationInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialModificationInterface || $dto instanceof self)
        {
            if($dto->isModification())
            {
                return parent::setEntity($dto);
            }

            return false;
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
