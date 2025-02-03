<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Materials\Category\Repository\MenuPublicCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Catalog\Entity\Category\MaterialCategory;
use BaksDev\Materials\Catalog\Entity\Info\MaterialInfo;
use BaksDev\Materials\Catalog\Entity\Material;
use BaksDev\Materials\Catalog\Entity\Offers\Image\MaterialOfferImage;
use BaksDev\Materials\Catalog\Entity\Offers\MaterialOffer;
use BaksDev\Materials\Catalog\Entity\Offers\Quantity\MaterialOfferQuantity;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\Image\MaterialVariationImage;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\MaterialVariation;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\Modification\Image\MaterialModificationImage;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\Modification\MaterialModification;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\Modification\Quantity\MaterialModificationQuantity;
use BaksDev\Materials\Catalog\Entity\Offers\Variation\Quantity\MaterialsVariationQuantity;
use BaksDev\Materials\Catalog\Entity\Photo\MaterialPhoto;
use BaksDev\Materials\Catalog\Entity\Price\MaterialPrice;
use BaksDev\Materials\Catalog\Entity\Trans\MaterialTrans;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Cover\CategoryMaterialCover;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;


final readonly class MenuPublicCategoryMaterialRepository implements MenuPublicCategoryMaterialInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает все категории меню
     */
    public function findAll(): array|bool
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /* Категория */
        $dbal
            ->select('category.id')
            ->addSelect('category.event AS event')
            ->from(CategoryMaterial::class, 'category');

        /* События категории */
        $dbal
            ->addSelect('category_event.sort AS category_sort')
            ->addSelect('category_event.parent AS category_parent')
            ->join(
                'category',
                CategoryMaterialEvent::class,
                'category_event',
                'category_event.id = category.event AND category_event.parent IS NULL'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryMaterialInfo::class,
                'category_info',
                'category_info.event = category.event AND category_info.active = true'
            );

        /* Обложка */
        $dbal
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect(
                "
			CASE
			 WHEN category_cover.name IS NOT NULL THEN
					CONCAT ( '/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', category_cover.name)
			   		ELSE NULL
			END AS category_cover_dir
		"
            );

        $dbal
            ->leftJoin(
                'category_event',
                CategoryMaterialCover::class,
                'category_cover',
                'category_cover.event = category_event.id'
            );

        /* Перевод категории */
        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->addSelect('category_trans.description AS category_description')
            ->leftJoin(
                'category_event',
                CategoryMaterialTrans::class,
                'category_trans',
                'category_trans.event = category_event.id AND category_trans.local = :local'
            );


        /* сырьё корневой категории */

        $dbal->leftJoin(
            'category',
            MaterialCategory::class,
            'material_category',
            'material_category.category = category.id AND material_category.root = true'
        );


        $dbal->leftJoin(
            'material_category',
            Material::class,
            'material',
            'material.event = material_category.event'
        );

        $dbal->leftJoin(
            'material_category',
            MaterialPrice::class,
            'material_price',
            'material_price.event = material.event'
        );

        $dbal
            ->leftJoin(
                'material',
                MaterialInfo::class,
                'material_info',
                'material_info.material = material.id'
            );

        $dbal
            ->leftJoin(
                'material',
                MaterialTrans::class,
                'material_trans',
                'material_trans.event = material.event AND material_trans.local = :local'
            );


        $dbal->leftJoin(
            'material',
            MaterialOffer::class,
            'material_offer',
            'material_offer.event = material.event'
        );

        $dbal->leftJoin(
            'material_offer',
            MaterialOfferQuantity::class,
            'material_offer_quantity',
            'material_offer_quantity.offer = material_offer.id'
        );


        $dbal->leftJoin(
            'material_offer',
            MaterialVariation::class,
            'material_variation',
            'material_variation.offer = material_offer.id'
        );

        $dbal->leftJoin(
            'material_variation',
            MaterialsVariationQuantity::class,
            'material_variation_quantity',
            'material_variation_quantity.variation = material_variation.id'
        );


        $dbal->leftJoin(
            'material_variation',
            MaterialModification::class,
            'material_modification',
            'material_modification.variation = material_variation.id'
        );


        $dbal->leftJoin(
            'material_modification',
            MaterialModificationQuantity::class,
            'material_modification_quantity',
            'material_modification_quantity.modification = material_modification.id'
        );


        // Фото сырья

        $dbal->leftJoin(
            'material_modification',
            MaterialModificationImage::class,
            'material_modification_image',
            '
                material_modification_image.modification = material_modification.id AND
                material_modification_image.root = true
			'
        );

        $dbal->leftJoin(
            'material_offer',
            MaterialVariationImage::class,
            'material_variation_image',
            '
                material_variation_image.variation = material_variation.id AND
                material_variation_image.root = true
			'
        );

        $dbal->leftJoin(
            'material_offer',
            MaterialOfferImage::class,
            'material_offer_images',
            '
			material_variation_image.name IS NULL AND
			material_offer_images.offer = material_offer.id AND
			material_offer_images.root = true
			'
        );

        $dbal->leftJoin(
            'material_offer',
            MaterialPhoto::class,
            'material_photo',
            '
                material_offer_images.name IS NULL AND
                material_photo.event = material.event AND
                material_photo.root = true
			'
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', COALESCE(
                               material_modification_quantity.reserve, 
                               material_variation_quantity.reserve, 
                               material_offer_quantity.reserve, 
                               material_price.reserve
                            ),
                            
                        'category', material_category.category,
                        'url', material_info.url,
      
                        'category_url', category_info.url,
                        'name', material_trans.name,
                        
                        'is_offer', CASE WHEN material_offer.id IS NOT NULL THEN TRUE ELSE FALSE END,
                
                         
                         'price', CASE 
                             WHEN material_offer.id IS NOT NULL 
                             THEN (SELECT 
                              
                              MIN(COALESCE(
                                    material_modification_price.price, 
                                    material_variation_price.price, 
                                    material_offer_price.price, 
                                    material_price.price
                              )) as price
                              
                              FROM material_offer  
                              LEFT JOIN material_offer_price ON material_offer_price.offer = material_offer.id
                              LEFT JOIN material_offer_quantity ON material_offer_quantity.offer = material_offer.id
   
                              LEFT JOIN material_variation ON material_variation.offer = material_offer.id
                              LEFT JOIN material_variation_price ON material_variation_price.variation = material_variation.id
                              LEFT JOIN material_variation_quantity ON material_variation_quantity.variation = material_variation.id
                              
                              LEFT JOIN material_modification ON material_modification.variation = material_variation.id
                              LEFT JOIN material_modification_price ON material_modification_price.modification = material_modification.id
                              LEFT JOIN material_modification_quantity ON material_modification_quantity.modification = material_modification.id
                             
                              LEFT JOIN material_price ON material_price.event = material_offer.event

                              WHERE material_offer.event = material.event

                              AND COALESCE(
                                (material_modification_quantity.quantity - material_modification_quantity.reserve), 
                                (material_variation_quantity.quantity - material_variation_quantity.reserve), 
                                (material_offer_quantity.quantity - material_offer_quantity.reserve), 
                                (material_price.quantity - material_price.reserve)
                            ) > 0
                              
                              
                              AND COALESCE(
                                    material_modification_price.price, 
                                    material_variation_price.price, 
                                    material_offer_price.price, 
                                    material_price.price
                              ) > 0
                            
                         ) ELSE 0 END,
                         
           
                        'image', CASE
                                 WHEN material_modification_image.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialModificationImage::class)."' , '/', material_modification_image.name)
                                   WHEN material_variation_image.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialVariationImage::class)."' , '/', material_variation_image.name)
                                   WHEN material_offer_images.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialOfferImage::class)."' , '/', material_offer_images.name)
                                   WHEN material_photo.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialPhoto::class)."' , '/', material_photo.name)
                                   ELSE NULL
                                END,
                                
                        'ext', CASE
                           WHEN material_modification_image.name IS NOT NULL THEN  material_modification_image.ext
                           WHEN material_variation_image.name IS NOT NULL THEN material_variation_image.ext
                           WHEN material_offer_images.name IS NOT NULL THEN material_offer_images.ext
                           WHEN material_photo.name IS NOT NULL THEN material_photo.ext
                           ELSE NULL
                        END,
                        
                        'cdn', CASE
                           WHEN material_variation_image.name IS NOT NULL THEN
                                material_variation_image.cdn
                           WHEN material_offer_images.name IS NOT NULL THEN
                                material_offer_images.cdn
                           WHEN material_photo.name IS NOT NULL THEN
                                material_photo.cdn
                           ELSE NULL
                        END        
                        
                        
                    ) 
                    
                    
                    
            ) FILTER (WHERE material_info.url IS NOT NULL AND 
                COALESCE(
                    (material_modification_quantity.quantity - material_modification_quantity.reserve), 
                    (material_variation_quantity.quantity - material_variation_quantity.reserve), 
                    (material_offer_quantity.quantity - material_offer_quantity.reserve), 
                    (material_price.quantity - material_price.reserve)
                ) > 0
            ) 
			AS materials"
        );


        /*







        */

        /* РАЗДЕЛЫ: 2-я вложенность  */

        // $dbal->addSelect('parent_category_event.id AS parent_event');
        $dbal->leftJoin(
            'category',
            CategoryMaterialEvent::class,
            'parent_category_event',
            'parent_category_event.parent = category.id'
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

        // $dbal->addSelect('parent_category_trans.name AS parent_category_name');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryMaterialTrans::class,
            'parent_category_trans',
            'parent_category_trans.event = parent_category_event.id  AND parent_category_trans.local = :local'
        );

        // сырьё вложенной категории

        $dbal->leftJoin(
            'parent_category_event',
            MaterialCategory::class,
            'material_category_two',
            'material_category_two.category = parent_category_event.category AND material_category_two.root = true'
        );


        $dbal->leftJoin(
            'material_category_two',
            Material::class,
            'material_two',
            'material_two.event = material_category_two.event'
        );

        $dbal->leftJoin(
            'material_two',
            MaterialPrice::class,
            'material_two_price',
            'material_two_price.event = material_two.event'
        );


        $dbal
            ->leftJoin(
                'material_two',
                MaterialInfo::class,
                'material_info_two',
                'material_info_two.material = material_two.id'
            );

        $dbal
            ->leftJoin(
                'material_two',
                MaterialTrans::class,
                'material_trans_two',
                'material_trans_two.event = material_two.event AND material_trans_two.local = :local'
            );


        $dbal->leftJoin(
            'material_two',
            MaterialOffer::class,
            'material_offer_two',
            'material_offer_two.event = material_two.event'
        );

        $dbal->leftJoin(
            'material_offer_two',
            MaterialOfferQuantity::class,
            'material_offer_two_quantity',
            'material_offer_two_quantity.offer = material_offer_two.id'
        );


        $dbal->leftJoin(
            'material_offer_two',
            MaterialVariation::class,
            'material_variation_two',
            'material_variation_two.offer = material_offer_two.id'
        );

        $dbal->leftJoin(
            'material_variation_two',
            MaterialsVariationQuantity::class,
            'material_variation_two_quantity',
            'material_variation_two_quantity.variation = material_variation_two.id'
        );


        $dbal->leftJoin(
            'material_variation_two',
            MaterialModification::class,
            'material_modification_two',
            'material_modification_two.variation = material_variation_two.id'
        );

        $dbal->leftJoin(
            'material_modification_two',
            MaterialModificationQuantity::class,
            'material_modification_two_quantity',
            'material_modification_two_quantity.modification = material_modification_two.id'
        );


        // Фото сырья

        $dbal->leftJoin(
            'material_modification_two',
            MaterialModificationImage::class,
            'material_modification_image_two',
            '
                material_modification_image_two.modification = material_modification_two.id AND
                material_modification_image_two.root = true
			'
        );

        $dbal->leftJoin(
            'material_offer_two',
            MaterialVariationImage::class,
            'material_variation_image_two',
            '
                material_variation_image_two.variation = material_variation_two.id AND
                material_variation_image_two.root = true
			'
        );


        $dbal->leftJoin(
            'material_offer_two',
            MaterialOfferImage::class,
            'material_offer_images_two',
            '
                material_variation_image_two.name IS NULL AND
                material_offer_images_two.offer = material_offer_two.id AND
                material_offer_images_two.root = true
			'
        );

        $dbal->leftJoin(
            'material_offer_two',
            MaterialPhoto::class,
            'material_photo_two',
            '
                material_offer_images_two.name IS NULL AND
                material_photo_two.event = material_two.event AND
                material_photo_two.root = true
			'
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', parent_category_event.sort,
                        
                        'category_url', parent_category_info.url,
                        
                        'child_category_cover_name', 
                        
                        CASE 
                            WHEN parent_category_cover.name IS NOT NULL 
                            THEN CONCAT ( '/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', parent_category_cover.name)
                            ELSE NULL
                        END,
                        
                        'child_category_cover_ext', parent_category_cover.ext,
                        'child_category_cover_cdn', parent_category_cover.cdn,
            
                        'child_category_event', parent_category_event.id,
                        'child_category_name', parent_category_trans.name,
                        'child_category_description', parent_category_trans.description
                    )
            ) FILTER (WHERE parent_category_info.url IS NOT NULL AND parent_category_info.active = true) 
			AS child_category"
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', material_info_two.sort,
                        'category', parent_category_event.id,
                        'name', material_trans_two.name,
                        
                        
                        'category_url', parent_category_info.url,
                        'url', material_info_two.url,
                        
                        'is_offer', CASE WHEN material_offer_two.id IS NOT NULL THEN TRUE ELSE FALSE END,
 
                        'price', CASE 
                             WHEN material_offer_two.id IS NOT NULL 
                             THEN (SELECT 
                              
                              MIN(COALESCE(
                                    material_modification_price.price, 
                                    material_variation_price.price, 
                                    material_offer_price.price, 
                                    material_price.price
                              )) as price
                              
                              FROM material_offer 

                              LEFT JOIN material_price ON material_price.event = material_offer.event
                              LEFT JOIN material_offer_price ON material_offer_price.offer = material_offer.id
                              LEFT JOIN material_variation_price ON material_variation_price.variation = material_variation.id
                              LEFT JOIN material_modification_price ON material_modification_price.modification = material_modification.id
                              
                              WHERE material_offer.event = material_two.event AND COALESCE(
                                    material_modification_price.price, 
                                    material_variation_price.price, 
                                    material_offer_price.price, 
                                    material_price.price
                              ) > 0
                            
                         ) ELSE 0 END,
                            
                            
                       
                        'image', CASE
                                 WHEN material_modification_image_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialModificationImage::class)."' , '/', material_modification_image_two.name)
                                   WHEN material_variation_image_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialVariationImage::class)."' , '/', material_variation_image_two.name)
                                   WHEN material_offer_images_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialOfferImage::class)."' , '/', material_offer_images_two.name)
                                   WHEN material_photo_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(MaterialPhoto::class)."' , '/', material_photo_two.name)
                                   ELSE NULL
                                END,
                                
                        'ext', CASE
                           WHEN material_modification_image_two.name IS NOT NULL THEN  material_modification_image_two.ext
                           WHEN material_variation_image_two.name IS NOT NULL THEN material_variation_image_two.ext
                           WHEN material_offer_images_two.name IS NOT NULL THEN material_offer_images_two.ext
                           WHEN material_photo_two.name IS NOT NULL THEN material_photo_two.ext
                           ELSE NULL
                        END,
                        
                        'cdn', CASE
                           WHEN material_variation_image_two.name IS NOT NULL THEN
                                material_variation_image_two.cdn
                           WHEN material_offer_images_two.name IS NOT NULL THEN
                                material_offer_images_two.cdn
                           WHEN material_photo_two.name IS NOT NULL THEN
                                material_photo_two.cdn
                           ELSE NULL
                        END 

                    ) 
            ) FILTER (WHERE material_info_two.url IS NOT NULL AND 
                COALESCE(
                    (material_modification_two_quantity.quantity - material_modification_two_quantity.reserve), 
                    (material_variation_two_quantity.quantity - material_variation_two_quantity.reserve), 
                    (material_offer_two_quantity.quantity - material_offer_two_quantity.reserve), 
                    (material_two_price.quantity - material_two_price.reserve)
                ) > 0
            ) 
			AS child_materials"
        );


        /*


        */

        /* РАЗДЕЛЫ: 3-я вложенность  */

        // $dbal->addSelect('parent_category_event.id AS parent_event');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryMaterialEvent::class,
            'parent_category_event_three',
            'parent_category_event_three.parent = parent_category_event.category'
        );

        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryMaterialInfo::class,
            'parent_category_info_three',
            'parent_category_info_three.event = parent_category_event_three.id'
        );

        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryMaterialCover::class,
            'parent_category_cover_three',
            'parent_category_cover_three.event = parent_category_event_three.id'
        );


        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryMaterialTrans::class,
            'parent_category_trans_three',
            'parent_category_trans_three.event = parent_category_event_three.id  AND parent_category_trans_three.local = :local'
        );


        $dbal->leftJoin(
            'parent_category_event_three',
            MaterialCategory::class,
            'material_category_three',
            'material_category_three.category = parent_category_event_three.category AND material_category_three.root = true'
        );


        $dbal->leftJoin(
            'material_category_three',
            Material::class,
            'material_three',
            'material_three.event = material_category_three.event'
        );

        $dbal->leftJoin(
            'material_three',
            MaterialPrice::class,
            'material_three_price',
            'material_three_price.event = material_three.event'
        );


        $dbal->leftJoin(
            'material_three',
            MaterialOffer::class,
            'material_offer_three',
            'material_offer_three.event = material_three.event'
        );

        $dbal->leftJoin(
            'material_offer_three',
            MaterialOfferQuantity::class,
            'material_offer_three_quantity',
            'material_offer_three_quantity.offer = material_offer_three.id'
        );


        $dbal->leftJoin(
            'material_offer_three',
            MaterialVariation::class,
            'material_variation_three',
            'material_variation_three.offer = material_offer_three.id'
        );

        $dbal->leftJoin(
            'material_variation_three',
            MaterialsVariationQuantity::class,
            'material_variation_three_quantity',
            'material_variation_three_quantity.variation = material_variation_three.id'
        );


        $dbal->leftJoin(
            'material_variation_three',
            MaterialModification::class,
            'material_modification_three',
            'material_modification_three.variation = material_variation_three.id'
        );

        $dbal->leftJoin(
            'material_modification_three',
            MaterialModificationQuantity::class,
            'material_modification_three_quantity',
            'material_modification_three_quantity.modification = material_modification_three.id'
        );


        $dbal->addSelect(
            "JSON_AGG
		( DISTINCT
			
				JSONB_BUILD_OBJECT
				(
					'0', parent_category_event_three.sort,
					
					'child_category_url', parent_category_info_three.url,
					
					'url', parent_category_info_three.url,
					'name', parent_category_trans_three.name,

					'child_category_cover_name', 
					
					CASE 
					    WHEN parent_category_cover_three.name IS NOT NULL 
					    THEN CONCAT ( '/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', parent_category_cover_three.name)
					    ELSE NULL
					END,
					
					'child_category_cover_ext', parent_category_cover_three.ext,
					'child_category_cover_cdn', parent_category_cover_three.cdn,
		
					'child_category_event', parent_category_event_three.id,
					'category', parent_category_event.id,
					'child_category_name', parent_category_trans_three.name,
					
					'child_category_description', parent_category_trans_three.description

				)
		) FILTER (WHERE parent_category_info_three.url IS NOT NULL AND parent_category_info_three.active = true AND material_three.id IS NOT NULL 
		
		
		 AND 
                COALESCE(
                    (material_modification_three_quantity.quantity - material_modification_three_quantity.reserve), 
                    (material_variation_three_quantity.quantity - material_variation_three_quantity.reserve), 
                    (material_offer_three_quantity.quantity - material_offer_three_quantity.reserve), 
                    (material_three_price.quantity - material_three_price.reserve)
                ) > 0
		
		) 
	
			AS child_category_three"
        );

        $dbal->orderBy('category_event.sort', 'ASC');

        $dbal->allGroupByExclude();

        /** Присваиваем кеш c namespace materials-catalog, т.к. меню завязано на сырьях */
        return $dbal
            ->enableCache('materials-category', refresh: false)
            ->fetchAllAssociativeIndexed();

    }

}