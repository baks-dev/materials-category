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

namespace BaksDev\Materials\Category\Repository\CategoryChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use Generator;
use InvalidArgumentException;

final class CategoryMaterialChoiceRepository implements CategoryMaterialChoiceInterface
{
    /**
     * Только активные разделы
     */
    private bool $active = false;

    /**
     * Идентификатор категории
     */
    private ?CategoryMaterialUid $category = null;


    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function onlyActive(): self
    {
        $this->active = true;
        return $this;
    }

    /** Фильтр по идентификатору категории */
    public function category(CategoryMaterial|CategoryMaterialUid|string $category): self
    {
        if($category instanceof CategoryMaterial)
        {
            $category = $category->getId();
        }

        if(is_string($category))
        {
            $category = new CategoryMaterialUid($category);
        }

        $this->category = $category;

        return $this;
    }


    public function findAll(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            //->addSelect('category.event')
            ->from(CategoryMaterial::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryMaterialEvent::class,
                'category_event',
                'category_event.id = category.event'
            );

        $dbal
            ->addSelect('category_trans.name')
            ->leftJoin(
                'category',
                CategoryMaterialTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local'
            );


        /* Категория с определенным идентификатором */
        if($this->category)
        {
            $dbal
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryMaterialUid::TYPE);
        }


        /* Выбираем только активные */
        if($this->active)
        {
            $dbal->join(
                'category',
                CategoryMaterialInfo::class,
                'info',
                'info.event = category.event AND info.active = true',
            );
        }

        $result = $dbal
            ->enableCache('materials-category')
            ->findAllRecursive(['parent' => 'id']);

        if(false === $result)
        {
            return false;
        }

        foreach($result as $item)
        {
            yield new CategoryMaterialUid($item['id'], $item['name'], $item['level']);
        }

    }

    public function find(): CategoryMaterialUid|false
    {
        if(empty($this->category))
        {
            throw new InvalidArgumentException('Invalid Argument category');
        }

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CategoryMaterial::class, 'category');

        /* Категория с определенным идентификатором */
        $dbal
            ->where('category.id = :category')
            ->setParameter('category', $this->category, CategoryMaterialUid::TYPE);

        /* Выбираем только активные */
        if($this->active)
        {
            $dbal->join(
                'category',
                CategoryMaterialInfo::class,
                'info',
                'info.event = category.event AND info.active = true',
            );
        }

        $dbal->leftJoin(
            'category',
            CategoryMaterialTrans::class,
            'trans',
            'trans.event = category.event AND trans.local = :local',
        );

        /** Свойства конструктора объекта гидрации */
        $dbal->select('category.id AS value');
        $dbal->addSelect('trans.name AS options');

        return $dbal
            ->enableCache('materials-category', 86400)
            ->fetchHydrate(CategoryMaterialUid::class);
    }

}
