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

namespace BaksDev\Materials\Category\Entity\Landing;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Посадочные блоки категории */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_landing')]
class CategoryMaterialLanding extends EntityState
{
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CategoryMaterialEvent::class, inversedBy: "landing")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private readonly CategoryMaterialEvent $event;

    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2)]
    private readonly Locale $local;

    /** Верхний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $header;

    /** Нижний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bottom;

    public function __construct(CategoryMaterialEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialLandingInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {

        if($dto instanceof CategoryMaterialLandingInterface || $dto instanceof self)
        {
            if(empty($dto->getHeader()) && empty($dto->getBottom()))
            {
                return false;
            }

            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }



    //    public function equals($dto) : bool
    //    {
    //        if($dto instanceof LandingInterface)
    //        {
    //            return  ($this->event->getId() === $dto->getEquals() &&
    //              $this->local->getValue() === $dto->getLocal()->getValue());
    //
    //        }
    //
    //        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    //    }


    //    public function updCategoryLanding(LandingInterface $landing) : void
    //    {
    //
    ////        if(property_exists($landing, 'header'))
    ////        {
    ////            $this->header = $landing->header;
    ////        }
    ////
    ////        if(property_exists($landing, 'bottom'))
    ////        {
    ////            $this->bottom = $landing->bottom;
    ////        }
    //    }

    //    public function getCategoryLanding(LandingInterface $landing) : LandingInterface
    //    {
    //        $oReflectionClass = new \ReflectionClass($landing);
    //
    //        foreach($oReflectionClass->getProperties() as $property)
    //        {
    //            $propertyName = $property->getName();
    //            $propertyNameSetter =  'set'.ucfirst($propertyName);
    //
    //            if(property_exists($this, $propertyName) && method_exists($landing, $propertyNameSetter))
    //            {
    //                $landing->$propertyNameSetter($this->{$propertyName});
    //            }
    //        }
    //
    //        return $landing;
    //    }


    //    /**
    //     * @param Event|CategoryEvent $event
    //     */
    //    public function __construct(Event|CategoryEvent $event, Locale $local)
    //    {
    //        $this->setEvent($event);
    //        $this->local = $local;
    //    }
    //
    //    public function clone(Event|CategoryEvent $event) : self
    //    {
    //        $clone = clone $this;
    //        $clone->setEvent($event);
    //        return $clone;
    //    }
    //
    //    /**
    //     * @param Event|CategoryEvent $event
    //     */
    //    private function setEvent(Event|CategoryEvent $event) : void
    //    {
    //        $this->event = $event instanceof Event ? $event->getId() : $event;;
    //    }
    //
    //    /**
    //     * @return Locale
    //     */
    //    public function getLocal() : Locale
    //    {
    //        return $this->local;
    //    }


    //    /**
    //     * @param Locale $local
    //     * @param string $header
    //     * @param string|null $bottom
    //     */
    //    public function addLanding(Locale $local, ?string $header, ?string $bottom)
    //    {
    //        $this->local = $local;
    //        $this->header = $header;
    //        $this->bottom = $bottom;
    //    }
    //
    //    /**
    //     * @return Locale
    //     */
    //    public function getLocal() : Locale
    //    {
    //        return $this->local;
    //    }


}
