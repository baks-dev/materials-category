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

namespace BaksDev\Materials\Category\Entity\Offers;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Event\Event;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;
use BaksDev\Materials\Category\Type\Offers\Id\OffersUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

// Торговые предложения

#[ORM\Entity]
#[ORM\Table(name: 'material_category_offers')]
class CategoryMaterialOffers extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialOffersUid::TYPE)]
    private readonly CategoryMaterialOffersUid $id;

    /** Связь на событие */
    #[ORM\OneToOne(targetEntity: CategoryMaterialEvent::class, inversedBy: 'offer')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id', nullable: true)]
    private ?CategoryMaterialEvent $event;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: Trans\CategoryMaterialOffersTrans::class, mappedBy: 'offer', cascade: ['all'])]
    private Collection $translate;

    /** Справочник */
    #[ORM\Column(type: InputField::TYPE, length: 32, nullable: true, options: ['default' => 'input'])]
    private ?InputField $reference = null;

    /** Загрузка пользовательских изображений */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $image = false;

    /** Торговое предложение с ценой */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $price = false;

    /** Количественный учет товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $quantitative = false;

    /** Торговое предложение с артикулом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $article = false;

    /** Торговое предложение с постфиксом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $postfix = false;

    /** Множественные варианты в торговом предложении */
    #[ORM\OneToOne(targetEntity: CategoryMaterialVariation::class, mappedBy: 'offer', cascade: ['all'])]
    private ?CategoryMaterialVariation $variation;

    public function __construct(CategoryMaterialEvent $event)
    {
        $this->id = new CategoryMaterialOffersUid();
        $this->event = $event;
    }


    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): CategoryMaterialOffersUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialOffersInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialOffersInterface || $dto instanceof self)
        {


            if($dto->isOffer())
            {
                return parent::setEntity($dto);
            }

            return false;
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
