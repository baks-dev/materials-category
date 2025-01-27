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


use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;

interface AllCategoryMaterialsInterface
{

    public function search(SearchDTO $search): self;

    /** Возвращает список категорий с ключами:
     *
     * id - идентификатор <br>
     * event - идентификатор события <br>
     * category_sort - сортирвка <br>
     * category_parent - идентификатор родителя категории <br>
     * category_cover_name - название файла обложки  <br>
     * category_cover_ext - расширение  файла обложки <br>
     * category_cover_cdn - флаг загрузки файла CDN <br>
     * category_cover_dir - директория  файла обложки <br>
     * category_name - название катеогрии <br>
     * category_description - краткое описание обложки <br>
     * category_parent_count - количество вложенных категорий <br>
     *
     */

    public function fetchMaterialParentAllAssociative(?ParentCategoryMaterialUid $parent = null): PaginatorInterface;

    public function getRecursive(): array|false;

    public function getOnlyChildren(): array;
}