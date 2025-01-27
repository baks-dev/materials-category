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

namespace BaksDev\Materials\Category\Repository\CategoryVariationForm;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Entity\Offers\Variation\Trans\CategoryMaterialVariationTrans;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;

final class CategoryMaterialVariationFormRepository implements CategoryMaterialVariationFormInterface
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

    public function findAllVariation(): ?CategoryMaterialVariationFormDTO
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(
            variation.id,
            variation.reference,
            variation.image,
            variation.price,
            variation.quantitative,
            variation.article,
            variation_trans.name,
            
            variation.postfix,
            variation_trans.postfix
        )',
            CategoryMaterialVariationFormDTO::class
        );

        $orm->select($select);

        $orm
            ->from(CategoryMaterialVariation::class, 'variation');

        if($this->offer)
        {
            $orm
                ->where('variation.offer = :offer')
                ->setParameter(
                    'offer',
                    $this->offer,
                    CategoryMaterialOffersUid::TYPE
                );
        }

        $orm->join(
            CategoryMaterialOffers::class,
            'offer',
            'WITH',
            'offer.id = variation.offer'
        );

        $orm->join(
            CategoryMaterial::class,
            'category',
            'WITH',
            'category.event = offer.event'
        );


        $orm->leftJoin(
            CategoryMaterialVariationTrans::class,
            'variation_trans',
            'WITH',
            'variation_trans.variation = variation.id AND variation_trans.local = :local'
        );


        return $orm->getQuery()->getOneOrNullResult();

    }
}
