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

namespace BaksDev\Materials\Category\Repository\ModificationFieldsCategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Materials\Category\Entity\Offers\Variation\CategoryMaterialVariation;
use BaksDev\Materials\Category\Entity\Offers\Variation\Modification\CategoryMaterialModification;
use BaksDev\Materials\Category\Entity\Offers\Variation\Modification\Trans\CategoryMaterialModificationTrans;
use BaksDev\Materials\Category\Type\Offers\Modification\CategoryMaterialModificationUid;
use BaksDev\Materials\Category\Type\Offers\Variation\CategoryMaterialVariationUid;

final class ModificationFieldsCategoryMaterialChoiceRepository implements ModificationFieldsCategoryMaterialChoiceInterface
{
    private string|CategoryMaterialVariationUid|CategoryMaterialVariation $variation;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function variation(CategoryMaterialVariation|CategoryMaterialVariationUid|string $variation): self
    {
        if($variation instanceof CategoryMaterialVariation)
        {
            $variation = $variation->getId();
        }

        if(is_string($variation))
        {
            $variation = new CategoryMaterialVariationUid($variation);
        }

        $this->variation = $variation;
        return $this;

    }

    public function findAllModification(): ?CategoryMaterialModificationUid
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(modification.id, trans.name, modification.reference)',
            CategoryMaterialModificationUid::class
        );
        $orm->select($select);

        $orm->from(CategoryMaterialVariation::class, 'variation');

        if($this->variation)
        {
            $orm
                ->where('variation.id = :variation')
                ->setParameter(
                    'variation',
                    $this->variation,
                    CategoryMaterialVariationUid::TYPE
                );
        }


        $orm->join(
            CategoryMaterialModification::class,
            'modification',
            'WITH',
            'modification.variation = variation.id'
        );

        $orm->leftJoin(
            CategoryMaterialModificationTrans::class,
            'trans',
            'WITH',
            'trans.modification = modification.id AND trans.local = :local'
        );


        /* Кешируем результат ORM */
        return $orm->enableCache('materials-category', 86400)->getOneOrNullResult();

    }

}
