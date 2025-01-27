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

namespace BaksDev\Materials\Category\Repository\CategoryByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Cover\CategoryMaterialCover;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Entity\Landing\CategoryMaterialLanding;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;

final readonly class CategoryMaterialByUrlRepository implements CategoryMaterialByUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     *  Категория по части URI
     */
    public function findByUrl(string $url): array|false
    {

        $dbal = $this
            ->DBALQueryBuilder->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->select('info.event AS category_event')
            ->addSelect('info.url AS category_url')
            ->addSelect('info.counter AS category_counter')
            ->from(CategoryMaterialInfo::class, 'info')
            ->where('info.url = :url')
            ->andWhere('info.active = true')
            ->setParameter('url', $url);

        $dbal
            ->addSelect('material_category.id AS category_id')
            ->join(
                'info',
                CategoryMaterial::class,
                'material_category',
                'material_category.event = info.event'
            );


        $dbal
            ->addSelect('material_category_event.parent AS category_parent')
            ->leftJoin(
                'material_category',
                CategoryMaterialEvent::class,
                'material_category_event',
                'material_category_event.id = material_category.event'
            );

        $dbal
            ->addSelect('material_category_trans.name AS category_name')
            ->leftJoin(
                'material_category',
                CategoryMaterialTrans::class,
                'material_category_trans',
                'material_category_trans.event = material_category_event.id  AND material_category_trans.local = :local'
            );


        $dbal
            ->addSelect('material_category_landing.header AS category_header')
            ->addSelect('material_category_landing.bottom AS category_bottom')
            ->leftJoin(
                'material_category',
                CategoryMaterialLanding::class,
                'material_category_landing',
                'material_category_landing.event = material_category_event.id  AND material_category_landing.local = :local'
            );


        /** КОРНЕВОЙ РАЗДЕЛ */
        $dbal
            ->leftJoin(
                'material_category_event',
                CategoryMaterial::class,
                'parent_material_category',
                'parent_material_category.id = material_category_event.parent'
            );

        $dbal
            ->addSelect('parent_material_category_trans.name AS parent_category_name')
            ->leftJoin(
                'parent_material_category',
                CategoryMaterialTrans::class,
                'parent_material_category_trans',
                'parent_material_category_trans.event = parent_material_category.event AND parent_material_category_trans.local = :local'
            );

        $dbal
            ->addSelect('parent_material_category_info.url AS parent_category_url')
            ->addSelect('parent_material_category_info.counter AS parent_category_counter')
            ->leftJoin(
                'parent_material_category',
                CategoryMaterialInfo::class,
                'parent_material_category_info',
                'parent_material_category_info.event = parent_material_category.event '
            );


        /** ВЛОЖЕННЫЕ РАЗДЕЛЫ */
        $dbal->leftJoin(
            'material_category',
            CategoryMaterialEvent::class,
            'parent_category_event',
            'parent_category_event.parent = material_category.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryMaterialInfo::class,
            'parent_category_info',
            'parent_category_info.event = parent_category_event.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryMaterialCover::class,
            'parent_category_cover',
            'parent_category_cover.event = parent_category_event.id'
        );


        //$dbal->addSelect('parent_category_trans.name AS parent_category_name');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryMaterialTrans::class,
            'parent_category_trans',
            'parent_category_trans.event = parent_category_event.id  AND parent_category_trans.local = :local'
        );


        $dbal->addSelect(
            "JSON_AGG
		( DISTINCT
			
				JSONB_BUILD_OBJECT
				(
					'0', parent_category_event.sort,
					
					'parent_category_url', parent_category_info.url,
					'parent_category_counter', parent_category_info.counter,
					
					'parent_category_cover_name', 
					CASE
					    WHEN parent_category_cover.name IS NOT NULL 
					    THEN CONCAT ( '/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', parent_category_cover.name)
					    ELSE NULL
                    END,
					
					'parent_category_cover_ext', parent_category_cover.ext,
					'parent_category_cover_cdn', parent_category_cover.cdn,
		
					'parent_category_event', parent_category_event.id,
					'parent_category_name', parent_category_trans.name
				)
		)
			AS parent_category"
        );

        /** Обложка категории */
        $dbal
            ->addSelect("
			CASE
			   WHEN category_cover.name IS NOT NULL 
			   THEN CONCAT ( '/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', category_cover.name)
			   ELSE NULL
			END AS category_cover_path
		")
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->leftJoin(
                'material_category',
                CategoryMaterialCover::class,
                'category_cover',
                'category_cover.event = material_category.event',
            );


        $dbal->allGroupByExclude();

        return $dbal
            ->enableCache('materials-category', 86400)
            ->fetchAssociative() ?: false;

    }
}
