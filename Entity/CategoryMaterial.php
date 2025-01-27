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

namespace BaksDev\Materials\Category\Entity;

use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Event\Event;
use BaksDev\Materials\Category\Type\Event\CategoryEvent;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Id\CategoryUid;
use Doctrine\ORM\Mapping as ORM;

/* Категории продуктов */

#[ORM\Entity]
#[ORM\Table(name: 'material_category')]
class CategoryMaterial
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialUid::TYPE)]
    private CategoryMaterialUid $id;

    /** ID События */
    #[ORM\Column(type: CategoryMaterialEventUid::TYPE, unique: true, nullable: false)]
    private ?CategoryMaterialEventUid $event = null;

    public function __toString(): string
    {
        return (string) $this->id;
    }


    public function __construct()
    {
        $this->id = new CategoryMaterialUid();
    }


    public function getId(): CategoryMaterialUid
    {
        return $this->id;
    }


    public function restore(CategoryMaterialUid $id): void
    {
        $this->id = $id;
    }


    public function getEvent(): ?CategoryMaterialEventUid
    {
        return $this->event;
    }


    public function setEvent(CategoryMaterialEvent|CategoryMaterialEventUid $event): void
    {
        $this->event = $event instanceof CategoryMaterialEvent ? $event->getId() : $event;
    }

}
