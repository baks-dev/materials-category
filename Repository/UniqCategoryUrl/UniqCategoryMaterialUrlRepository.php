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

namespace BaksDev\Materials\Category\Repository\UniqCategoryUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;

final class UniqCategoryMaterialUrlRepository implements UniqCategoryMaterialUrlInterface
{
    private CategoryMaterialEventUid|string|CategoryMaterialEvent $event;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function excludeEvent(CategoryMaterialEventUid|CategoryMaterialEvent|string $event): self
    {
        if($event instanceof CategoryMaterialEvent)
        {
            $event = $event->getId();
        }

        if(is_string($event))
        {
            $event = new CategoryMaterialEventUid($event);
        }

        $this->event = $event;
        return $this;
    }

    public function isExistUrl(string $url): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(CategoryMaterialInfo::class, 'info')
            ->where('info.url = :url')
            ->setParameter('url', $url);

        /** Исключить событие ( Exclude Event )*/
        if($this->event)
        {
            $dbal
                ->andWhere('info.event != :event')
                ->setParameter('event', $this->event, CategoryMaterialEventUid::TYPE);
        }

        return $dbal->fetchExist();
    }
}
