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

declare(strict_types=1);

namespace BaksDev\Materials\Category\Repository\CategoryCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use InvalidArgumentException;

final class CategoryMaterialCurrentEventRepository implements CategoryMaterialCurrentEventInterface
{
    private CategoryMaterialEventUid|false $event = false;

    private CategoryMaterialUid|false $main;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forEvent(CategoryMaterialEvent|CategoryMaterialEventUid|string $event): self
    {
        if(is_string($event))
        {
            $event = new CategoryMaterialEventUid($event);
        }

        if($event instanceof CategoryMaterialEvent)
        {
            $event = $event->getId();
        }

        $this->event = $event;

        return $this;
    }


    public function forMain(CategoryMaterial|CategoryMaterialUid|string $main): self
    {
        if(is_string($main))
        {
            $main = new CategoryMaterialUid($main);
        }

        if($main instanceof CategoryMaterial)
        {
            $main = $main->getId();
        }

        $this->main = $main;

        return $this;
    }

    /** Метод возвращает активное событие категории сырья */
    public function find(): CategoryMaterialEvent|false
    {
        if($this->event === false && $this->main === false)
        {
            throw new InvalidArgumentException('Invalid Argument CategoryMaterialEventUid or CategoryMaterialUid');
        }

        if($this->event !== false && $this->main !== false)
        {
            throw new InvalidArgumentException('Вызов двух аргументов CategoryMaterialUid и CategoryMaterialUid');
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        if($this->main !== false)
        {
            $qb
                ->from(CategoryMaterial::class, 'main')
                ->where('main.id = :main')
                ->setParameter('main', $this->main, CategoryMaterialUid::TYPE);
        }

        if($this->event !== false)
        {
            $qb
                ->from(CategoryMaterialEvent::class, 'event')
                ->where('event.id = :event')
                ->setParameter('event', $this->event, CategoryMaterialEventUid::TYPE);

            $qb->join(
                CategoryMaterial::class,
                'main',
                'WITH',
                'main.id = event.category'
            );
        }


        $qb
            ->select('current')
            ->leftJoin(
                CategoryMaterialEvent::class,
                'current',
                'WITH',
                'current.id = main.event'
            );

        return $qb->getQuery()->getOneOrNullResult() ?: false;
    }
}
