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

namespace BaksDev\Materials\Category\Repository\AllCategory\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Materials\Category\Repository\AllCategory\AllCategoryMaterialsInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group materials-category
 */
#[When(env: 'test')]
class AllCategoryRepositoryTest extends KernelTestCase
{
    private static AllCategoryMaterialsInterface $repository;

    public static function setUpBeforeClass(): void
    {
        self::$repository = self::getContainer()->get(AllCategoryMaterialsInterface::class);
    }

    public function testGetRecursive(): void
    {
        /** @var AllCategoryMaterialsInterface $AllCategoryInterface */
        $AllCategoryInterface = self::$repository;
        $result = $AllCategoryInterface->getRecursive();

        $array_keys = [
            "id",
            "event",
            "sort",
            "parent",
            "category_url",
            "category_name",
            "category_cover_image",
            "category_cover_cdn",
            "category_cover_ext",
            "groups",
            "level",
        ];

        $current = current($result);

        foreach($current as $key => $value)
        {
            self::assertTrue(in_array($key, $array_keys), sprintf('Появился новый ключ %s', $key));
        }

        foreach($array_keys as $key)
        {
            self::assertTrue(array_key_exists($key, $current));
        }

    }

    public function testGetOnlyChildren(): void
    {
        /** @var AllCategoryMaterialsInterface $AllCategoryInterface */
        $AllCategoryInterface = self::$repository;
        $allCategories = $AllCategoryInterface->getRecursive();
        $children = $AllCategoryInterface->getOnlyChildren();

        $previous = null;

        foreach($allCategories as $currentCategory)
        {
            if(null !== $previous)
            {
                //  если текущая категория ссылается на предыдущую - то предыдущая - PARENT, текущая - CHILD
                if($currentCategory['parent'] === $previous['id'])
                {
                    $parent = $previous;

                    // проверка, что в категориях с children нет parents
                    self::assertFalse(in_array($parent, $children, true));
                }
            }

            $previous = $currentCategory;
        }

        self::assertTrue(true);
    }


    public function testFetchMaterialParentAllAssociativeTest()
    {
        /** @var AllCategoryMaterialsInterface $AllCategoryInterface */
        $AllCategoryInterface = self::$repository;
        $result = $AllCategoryInterface->fetchMaterialParentAllAssociative();

        self::assertInstanceOf(PaginatorInterface::class, $result);

        self::assertIsArray($result->getData());
        self::assertNotEmpty($result->getData());

        $array_keys = [
            "id",
            "event",
            "category_sort",
            "category_parent",
            "category_cover_name",
            "category_cover_ext",
            "category_cover_cdn",
            "category_name",
            "category_description",
            "category_child_count",

        ];

        $current = current($result->getData());

        foreach($current as $key => $value)
        {
            self::assertTrue(in_array($key, $array_keys), sprintf('Появился новый ключ %s', $key));
        }

        foreach($array_keys as $key)
        {
            self::assertTrue(array_key_exists($key, $current));
        }
    }
}
