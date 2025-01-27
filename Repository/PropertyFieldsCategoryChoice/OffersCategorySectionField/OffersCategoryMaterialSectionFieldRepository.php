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

namespace BaksDev\Materials\Category\Repository\PropertyFieldsCategoryChoice\OffersCategorySectionField;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Trans\CategoryMaterialOffersTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;

final class OffersCategoryMaterialSectionFieldRepository implements OffersCategoryMaterialSectionFieldInterface
{
    private ?CategoryMaterialUid $category = null;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

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


    public function findAll(): CategoryMaterialSectionFieldUid|false
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

        $dbal->join(
            'category',
            CategoryMaterialOffers::class,
            'category_offers',
            'category_offers.event = category.event',
        );

        $dbal->leftJoin(
            'category_offers',
            CategoryMaterialOffersTrans::class,
            'category_offers_tarns',
            'category_offers_tarns.offer = category_offers.id AND category_offers_tarns.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_offers.id AS value');
        $dbal->addSelect('category.event AS const');
        $dbal->addSelect('category_offers_tarns.name AS attr');
        $dbal->addSelect('category_offers.reference AS property');

        return $dbal->fetchHydrate(CategoryMaterialSectionFieldUid::class);
    }
}
