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

namespace BaksDev\Materials\Category\Repository\VariationByCategory;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;

final class CategoryMaterialVariationByOfferRepository implements CategoryMaterialVariationByOfferInterface
{
    private ?CategoryMaterialOffersUid $offer = null;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}


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

    /**
     * Метод получает идентификатор настройки торгового предложения сырья в категории
     */
    public function findCategoryMaterialVariation(): ?CategoryMaterialVariation
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->from(CategoryMaterialOffers::class, 'offer');

        if($this->offer)
        {
            $qb
                ->where('offer.id = :offer')
                ->setParameter(
                    key: 'offer',
                    value: $this->offer,
                    type: CategoryMaterialOffersUid::TYPE
                );
        }
        $qb
            ->select('variation')
            ->leftJoin(
                CategoryMaterialVariation::class,
                'variation',
                'WITH',
                'variation.offer = offer.id'
            );


        return $qb->getOneOrNullResult();
    }
}
