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

namespace BaksDev\Materials\Category\Repository\OfferFieldsCategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Offers\CategoryMaterialOffers;
use BaksDev\Materials\Category\Entity\Offers\Trans\CategoryMaterialOffersTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;

final class OfferFieldsCategoryMaterialChoiceRepository implements OfferFieldsCategoryMaterialChoiceInterface
{
    private ?CategoryMaterialUid $category = null;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

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

    /**
     * Метод возвращает список свойств указанной категории (тип и название)
     */
    public function findAllCategoryMaterialOffers(): ?CategoryMaterialOffersUid
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf('new %s(offer.id, trans.name, offer.reference)', CategoryMaterialOffersUid::class);
        $orm->select($select);


        $orm->from(CategoryMaterial::class, 'category');

        if($this->category)
        {
            $orm
                ->where('category.id = :category')
                ->setParameter(
                    key: 'category',
                    value: $this->category,
                    type: CategoryMaterialUid::TYPE
                );
        }

        $orm->join(
            CategoryMaterialOffers::class,
            'offer',
            'WITH',
            'offer.event = category.event'
        );

        $orm->leftJoin(
            CategoryMaterialOffersTrans::class,
            'trans',
            'WITH',
            'trans.offer = offer.id AND trans.local = :local'
        );

        /* Кешируем результат ORM */
        return $orm->getOneOrNullResult();

    }
}
