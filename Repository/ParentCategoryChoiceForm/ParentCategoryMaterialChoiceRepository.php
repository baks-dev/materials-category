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

namespace BaksDev\Materials\Category\Repository\ParentCategoryChoiceForm;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use Generator;

final readonly class ParentCategoryMaterialChoiceRepository implements ParentCategoryMaterialChoiceInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод получает рекурсивно список категорий согласно вложенности
     */
    public function findAll(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->addSelect('category.event')
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

        $result = $dbal
            ->enableCache('materials-category')
            ->findAllRecursive(['parent' => 'id']);

        if(false === $result)
        {
            return false;
        }

        foreach($result as $item)
        {
            yield new ParentCategoryMaterialUid($item['id'], $item['name'], $item['level']);
        }
    }

}
