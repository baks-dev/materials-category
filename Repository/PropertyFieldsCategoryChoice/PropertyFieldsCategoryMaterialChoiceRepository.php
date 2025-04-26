<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Materials\Category\Repository\PropertyFieldsCategoryChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSection;
use BaksDev\Materials\Category\Entity\Section\Field\CategoryMaterialSectionField;
use BaksDev\Materials\Category\Entity\Section\Field\Trans\CategoryMaterialSectionFieldTrans;
use BaksDev\Materials\Category\Entity\Section\Trans\CategoryMaterialSectionTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;
use Generator;

final class PropertyFieldsCategoryMaterialChoiceRepository implements PropertyFieldsCategoryMaterialChoiceInterface
{
    private ?CategoryMaterialUid $category = null;

    public function __construct(
        private readonly ORMQueryBuilder $ORMQueryBuilder,
        private readonly DBALQueryBuilder $DBALQueryBuilder
    ) {}

    public function category(CategoryMaterial|CategoryMaterialUid|string $category): self
    {
        if($category instanceof CategoryMaterial)
        {
            $category = $category->getId();
        }

        if(is_string($category))
        {
            $category = new CategoryMaterialUid();
        }

        $this->category = $category;
        return $this;
    }

    /**
     * Метод возвращает список всех свойств
     */
    public function getPropertyFieldsCollection(): ?array
    {
        $qb = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'NEW %s(
              field.const,
              field.id,
              field_trans.name,
              field.type
          )',
            CategoryMaterialSectionFieldUid::class,
        );

        $qb->select($select);

        $qb->from(CategoryMaterial::class, 'category');

        if($this->category)
        {
            $qb
                ->where('category.id = :category')
                ->setParameter(
                    key: 'category',
                    value: $this->category,
                    type: CategoryMaterialUid::TYPE
                );
        }

        $qb->join(
            CategoryMaterialEvent::class,
            'category_event',
            'WITH',
            'category_event.id = category.event',
        );


        $qb->orderBy('section.sort', 'ASC');
        $qb->addOrderBy('field.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }


    /**
     * Метод возвращает список всех свойств
     */
    public function newPropertyFieldsCollection(): Generator|false
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
                    key: 'category',
                    value: $this->category,
                    type: CategoryMaterialUid::TYPE
                );
        }

        $dbal->join(
            'category',
            CategoryMaterialEvent::class,
            'category_event',
            'category_event.id = category.event',
        );


        $dbal->orderBy('section.sort', 'ASC');
        $dbal->addOrderBy('field.sort', 'ASC');


        /** Параметры конструктора объекта гидрации */
        $dbal->select('field.const AS value');
        $dbal->addSelect('field.id AS const');
        $dbal->addSelect('field_trans.name AS attr');
        $dbal->addSelect('field.type AS property');

        return $dbal->fetchAllHydrate(CategoryMaterialSectionFieldUid::class);
    }
}
