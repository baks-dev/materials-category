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

namespace BaksDev\Materials\Category\Repository\CategoryPropertyById;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSection;
use BaksDev\Materials\Category\Entity\Section\Field\CategoryMaterialSectionField;
use BaksDev\Materials\Category\Entity\Section\Field\Trans\CategoryMaterialSectionFieldTrans;
use BaksDev\Materials\Category\Entity\Section\Trans\CategoryMaterialSectionTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;

final class CategoryMaterialPropertyByIdRepository implements CategoryMaterialPropertyByIdInterface
{
    private CategoryMaterialUid|false $category = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    public function forCategory(CategoryMaterial|CategoryMaterialUid|string|null $category): self
    {
        if($category === null)
        {
            return $this;
        }

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

    public function findAll(): false|array
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CategoryMaterial::class, 'category');

        if($this->category)
        {
            $dbal
                ->where('category.id = :category')
                ->setParameter(
                    'category',
                    $this->category,
                    CategoryMaterialUid::TYPE
                );
        }

        $dbal->leftJoin(
            'category',
            CategoryMaterialEvent::class,
            'category_event',
            'category_event.id = category.event'
        );


        /* Секции свойств */
        $dbal->leftJoin(
            'category',
            CategoryMaterialSection::class,
            'section',
            'section.event = category.event'
        );


        /* Перевод секции */
        $dbal->leftJoin(
            'section',
            CategoryMaterialSectionTrans::class,
            'section_trans',
            'section_trans.section = section.id AND section_trans.local = :local'
        );


        /* Перевод полей */
        //$orm->addSelect('field.id');
        $dbal->join(
            'section',
            CategoryMaterialSectionField::class,
            'field',
            'field.section = section.id AND field.const IS NOT NULL'
        );


        $dbal->leftJoin(
            'field',
            CategoryMaterialSectionFieldTrans::class,
            'field_trans',
            'field_trans.field = field.id AND field_trans.local = :local'
        );


        $dbal->orderBy('section.sort', 'ASC');
        $dbal->addOrderBy('field.sort', 'ASC');


        $dbal->select('field.const');

        $dbal->addSelect('section.id AS section');
        $dbal->addSelect('section_trans.name AS section_trans');
        $dbal->addSelect('field.const AS field');
        $dbal->addSelect('field_trans.name AS field_trans');
        $dbal->addSelect('field.type AS field_type');
        $dbal->addSelect('field.required AS required');
        $dbal->addSelect('field_trans.description AS description');

        return $dbal->fetchAllIndexHydrate(CategoryMaterialPropertyDTO::class);

    }
}
