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

namespace BaksDev\Materials\Category\UseCase\Admin\Delete\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Repository\CategoryCurrentEvent\CategoryMaterialCurrentEventInterface;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\Delete\DeleteCategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\Delete\DeleteCategoryMaterialHandler;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialHandler;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialEditTest;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialNewTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group category-material
 * @group category-material-usecase
 *
 * @depends BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialNewTest::class
 * @depends BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialEditTest::class
 * @depends BaksDev\Materials\Category\Controller\Admin\Tests\DeleteControllerTest::class
 */
#[When(env: 'test')]
class CategoryMaterialDeleteTest extends KernelTestCase
{
    public static function tearDownAfterClass(): void
    {
        CategoryMaterialNewTest::setUpBeforeClass();
    }

    public function testUseCase(): void
    {
        /** @var CategoryMaterialCurrentEventInterface $CategoryMaterialCurrentEvent */
        $CategoryMaterialCurrentEvent = self::getContainer()->get(CategoryMaterialCurrentEventInterface::class);
        $CategoryMaterialEvent = $CategoryMaterialCurrentEvent->forMain(CategoryMaterialUid::TEST)->find();
        self::assertNotNull($CategoryMaterialEvent);

        /** @see CategoryMaterialDeleteDTO */
        $CategoryMaterialDeleteDTO = new DeleteCategoryMaterialDTO();
        $CategoryMaterialEvent->getDto($CategoryMaterialDeleteDTO);


        /** @var CategoryMaterialHandler $CategoryMaterialHandler */
        $CategoryMaterialDeleteHandler = self::getContainer()->get(DeleteCategoryMaterialHandler::class);
        $handle = $CategoryMaterialDeleteHandler->handle($CategoryMaterialDeleteDTO);

        self::assertTrue(($handle instanceof CategoryMaterial), $handle.': Ошибка CategoryMaterial');

    }

    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(CategoryMaterial::class)
            ->where('id = :id')
            ->setParameter('id', CategoryMaterialUid::TEST);

        self::assertFalse($dbal->fetchExist());

    }
}
