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

namespace BaksDev\Materials\Category\Repository\AllCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Cover\CategoryMaterialCover;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfo;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTrans;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;

final class AllCategoryMaterialsRepository implements AllCategoryMaterialsInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    /** Возвращает список категорий с ключами:.
     *
     * id - идентификатор <br>
     * event - идентификатор события <br>
     * category_sort - сортировка <br>
     * category_parent - идентификатор родителя категории <br>
     * category_cover_name - название файла обложки  <br>
     * category_cover_ext - расширение  файла обложки <br>
     * category_cover_cdn - флаг загрузки файла CDN <br>
     * category_cover_dir - директория  файла обложки <br>
     * category_name - название категории <br>
     * category_description - краткое описание обложки <br>
     * category_child_count - количество вложенных категорий <br>
     */
    public function fetchMaterialParentAllAssociative(
        ?ParentCategoryMaterialUid $parent = null
    ): PaginatorInterface
    {

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->addSelect('category.event')
            ->from(CategoryMaterial::class, 'category');

        // События категории
        $dbal
            ->addSelect('category_event.sort AS category_sort')
            ->addSelect('category_event.parent AS category_parent')
            ->join(
                'category',
                CategoryMaterialEvent::class,
                'category_event',
                'category_event.id = category.event AND '.
                ($parent ? 'category_event.parent = :parent_category' : 'category_event.parent IS NULL')
            );

        // Обложка
        $dbal
            ->addSelect('category_cover.name AS category_cover_name')
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->leftJoin(
                'category_event',
                CategoryMaterialCover::class,
                'category_cover',
                'category_cover.event = category_event.id'
            );

        if($parent)
        {
            $dbal->setParameter('parent_category', $parent, ParentCategoryMaterialUid::TYPE);
        }

        // Перевод категории
        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->addSelect('category_trans.description AS category_description')
            ->leftJoin(
                'category_event',
                CategoryMaterialTrans::class,
                'category_trans',
                '
                    category_trans.event = category_event.id AND 
                    category_trans.local = :local
                '
            );

        /** Количество вложенных категорий */

        // EXISTS Event IN Category
        $dbalCounterExist = $this->DBALQueryBuilder->createQueryBuilder(self::class);
        $dbalCounterExist
            ->select('1')
            ->from(CategoryMaterial::class, 'count_cat')
            ->where('count_cat.id = category_event_count.category')
            ->andWhere('count_cat.event = category_event_count.id');

        // COUNT Event
        $dbalCounter = $this->DBALQueryBuilder->createQueryBuilder(self::class);
        $dbalCounter
            ->select('COUNT(category_event_count.id)')
            ->from(CategoryMaterialEvent::class, 'category_event_count')
            ->where('category_event_count.parent = category.id');

        $dbalCounter
            ->join(
                'category_event_count',
                CategoryMaterial::class,
                'count_cat',
                'count_cat.id = category_event_count.category AND count_cat.event = category_event_count.id'
            );

        $dbal->addSelect('('.$dbalCounter->getSQL().') AS category_child_count');
        $dbalCounter->andWhere('EXISTS ('.$dbalCounterExist->getSQL().')');


        /* Поиск */
        if($this->search?->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('category_trans.name');

        }

        $dbal->orderBy('category_event.sort', 'ASC');

        return $this->paginator->fetchAllAssociative($dbal);

    }


    public function getRecursive(): array|false
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->addSelect('category.event')
            ->from(CategoryMaterial::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryMaterialEvent::class,
                'category_event',
                'category_event.id = category.event'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryMaterialInfo::class,
                'category_info',
                '
                    category_info.event = category.event AND 
                    category_info.active IS TRUE'
            );

        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->leftJoin(
                'category',
                CategoryMaterialTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local'
            );


        $dbal
            ->addSelect("CONCAT ('/upload/".$dbal->table(CategoryMaterialCover::class)."' , '/', category_cover.name) AS category_cover_image")
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->leftJoin(
                'category',
                CategoryMaterialCover::class,
                'category_cover',
                'category_cover.event = category.event'
            );


        return $dbal
            ->enableCache('materials-category', 86400)
            ->findAllRecursive(['parent' => 'id']);
    }

    /** Фильтрация категорий, у которых могут быть продукты */
    public function getOnlyChildren(): array
    {
        $result = $this->getRecursive();

        if(false === $result)
        {
            return [];
        }

        $previous = null;

        foreach($result as $key => $currentCategory)
        {
            if(null !== $previous)
            {
                // если у текущей категории родитель это предыдущий элемент - удаляем его
                if($currentCategory['parent'] === $previous['id'])
                {
                    unset($result[$key - 1]);
                }
            }

            $previous = $currentCategory;
        }

        return $result;
    }
}
