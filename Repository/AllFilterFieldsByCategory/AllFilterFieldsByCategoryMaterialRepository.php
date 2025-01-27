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

namespace BaksDev\Materials\Category\Repository\AllFilterFieldsByCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Section\CategoryMaterialSection;
use BaksDev\Materials\Category\Entity\Section\Field\CategoryMaterialSectionField;
use BaksDev\Materials\Category\Entity\Section\Field\Trans\CategoryMaterialSectionFieldTrans;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;

final class AllFilterFieldsByCategoryMaterialRepository implements AllFilterFieldsByCategoryMaterialInterface
{
    private ?CategoryMaterialUid $category = null;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает все свойства, участвующие в фильтре
     */
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

    public function findAll(): array
    {

        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->from(CategoryMaterial::class, 'category');


        if($this->category)
        {
            $qb
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryMaterialUid::TYPE);
        }

        $qb
            ->leftJoin(
                'category',
                CategoryMaterialSection::class,
                'category_section',
                'category_section.event = category.event'
            );

        $qb
            ->select('category_section_field.id')
            ->addSelect('category_section_field.const')
            ->addSelect('category_section_field.type')
            ->leftJoin(
                'category_section',
                CategoryMaterialSectionField::class,
                'category_section_field',
                'category_section_field.section = category_section.id AND category_section_field.filter = TRUE'
            );


        $qb
            ->addSelect('category_section_field_trans.name')
            ->leftJoin(
                'category_section_field',
                CategoryMaterialSectionFieldTrans::class,
                'category_section_field_trans',
                'category_section_field_trans.field = category_section_field.id AND category_section_field_trans.local = :local'
            );


        $qb->orderBy('category_section.sort, category_section_field.sort');

        return $qb->enableCache('materials-category')->fetchAllAssociative();
    }

}
