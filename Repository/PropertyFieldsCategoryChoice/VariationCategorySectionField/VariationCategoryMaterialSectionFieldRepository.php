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

namespace BaksDev\Materials\Category\Repository\PropertyFieldsCategoryChoice\VariationCategorySectionField;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Entity\Offers\Variation\Trans\CategoryMaterialVariationTrans;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;

final class VariationCategoryMaterialSectionFieldRepository implements VariationCategoryMaterialSectionFieldInterface
{
    private ?CategoryMaterialOffersUid $offer = null;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function offer(CategoryMaterialOffers|CategoryMaterialOffersUid|string $offer): self
    {
        if($offer instanceof CategoryMaterialOffers)
        {
            $offer = $offer->getId();
        }

        if(is_string($offer))
        {
            $offer = new CategoryMaterialOffersUid($offer);
        }

        $this->offer = $offer;
        return $this;
    }

    public function findAll(): CategoryMaterialSectionFieldUid|false
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CategoryMaterialVariation::class, 'category_variation');

        if($this->offer)
        {
            $dbal
                ->where('category_variation.offer = :offer')
                ->setParameter('offer', $this->offer, CategoryMaterialOffersUid::TYPE);
        }

        $dbal->leftJoin(
            'category_variation',
            CategoryMaterialVariationTrans::class,
            'category_variation_trans',
            'category_variation_trans.variation = category_variation.id AND category_variation_trans.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_variation.id AS value');
        $dbal->addSelect('category_variation.offer AS const');
        $dbal->addSelect('category_variation_trans.name AS attr');
        $dbal->addSelect('category_variation.reference AS property');

        return $dbal->fetchHydrate(CategoryMaterialSectionFieldUid::class); // ->getOneOrNullResult();
    }
}
