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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests;

use BaksDev\Materials\Category\Repository\CategoryCurrentEvent\CategoryMaterialCurrentEventInterface;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\CategoryMaterialOffersDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Trans\CategoryMaterialOffersTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\CategoryMaterialVariationDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\CategoryMaterialModificationDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans\CategoryMaterialModificationTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Trans\CategoryMaterialVariationTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Trans\CategoryMaterialTransDTO;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


#[When(env: 'test')]
#[Group('materials-category')]
class CategoryMaterialEditTest extends KernelTestCase
{
    #[DependsOnClass(CategoryMaterialNewTest::class)]
    public function testUseCase(): void
    {
        /** @var CategoryMaterialCurrentEventInterface $CategoryMaterialCurrentEvent */
        $CategoryMaterialCurrentEvent = self::getContainer()->get(CategoryMaterialCurrentEventInterface::class);
        $CategoryMaterialEvent = $CategoryMaterialCurrentEvent
            ->forMain(CategoryMaterialUid::TEST)
            ->find();


        self::assertNotNull($CategoryMaterialEvent);
        self::assertNotFalse($CategoryMaterialEvent);

        /** @see CategoryMaterialDTO */
        $CategoryMaterialDTO = new CategoryMaterialDTO();
        $CategoryMaterialEvent->getDto($CategoryMaterialDTO);

        self::assertEquals(123, $CategoryMaterialDTO->getSort());
        $CategoryMaterialDTO->setSort(321);




        /** @var CategoryMaterialTransDTO $CategoryMaterialTransDTO */
        foreach($CategoryMaterialDTO->getTranslate() as $CategoryMaterialTransDTO)
        {
            self::assertEquals('Test Category Name', $CategoryMaterialTransDTO->getName());
            $CategoryMaterialTransDTO->setName('Edit Test Category Name');

            self::assertEquals('Test Category Description', $CategoryMaterialTransDTO->getDescription());
            $CategoryMaterialTransDTO->setDescription('Edit Test Category Description');

        }


        /** @var CategoryMaterialOffersDTO $CategoryMaterialOffersDTO */
        $CategoryMaterialOffersDTO = $CategoryMaterialDTO->getOffer();

        /** @var CategoryMaterialOffersTransDTO $MaterialOffersTransDTO */
        foreach($CategoryMaterialOffersDTO->getTranslate() as $MaterialOffersTransDTO)
        {
            self::assertEquals('Test Category Offer Name', $MaterialOffersTransDTO->getName());
            $MaterialOffersTransDTO->setName('Edit Test Category Offer Name');


        }

        self::assertTrue($CategoryMaterialOffersDTO->isOffer());

        self::assertTrue($CategoryMaterialOffersDTO->getPrice());
        $CategoryMaterialOffersDTO->setPrice(false);

        self::assertTrue($CategoryMaterialOffersDTO->getImage());
        $CategoryMaterialOffersDTO->setImage(true);


        self::assertTrue($CategoryMaterialOffersDTO->getQuantitative());
        $CategoryMaterialOffersDTO->setQuantitative(true);

        self::assertTrue($CategoryMaterialOffersDTO->getReference()->getType() === 'input');


        /* * */


        /** @var CategoryMaterialVariationDTO $CategoryMaterialVariationDTO */
        $CategoryMaterialVariationDTO = $CategoryMaterialOffersDTO->getVariation();

        /** @var CategoryMaterialVariationTransDTO $CategoryMaterialVariationTransDTO */
        foreach($CategoryMaterialVariationDTO->getTranslate() as $CategoryMaterialVariationTransDTO)
        {
            self::assertEquals('Test Category Variation Name', $CategoryMaterialVariationTransDTO->getName());
            $CategoryMaterialVariationTransDTO->setName('Edit Test Category Variation Name');


        }

        self::assertTrue($CategoryMaterialVariationDTO->isVariation());

        self::assertTrue($CategoryMaterialVariationDTO->getPrice());
        $CategoryMaterialVariationDTO->setPrice(true);

        self::assertTrue($CategoryMaterialVariationDTO->getImage());
        $CategoryMaterialVariationDTO->setImage(true);

        self::assertTrue($CategoryMaterialVariationDTO->getQuantitative());
        $CategoryMaterialVariationDTO->setQuantitative(true);

        self::assertTrue($CategoryMaterialVariationDTO->getReference()->getType() === 'input');


        /** @var CategoryMaterialModificationDTO $CategoryMaterialModificationDTO */
        $CategoryMaterialModificationDTO = $CategoryMaterialVariationDTO->getModification();

        /** @var CategoryMaterialModificationTransDTO $CategoryMaterialModificationTransDTO */
        foreach($CategoryMaterialModificationDTO->getTranslate() as $CategoryMaterialModificationTransDTO)
        {
            self::assertEquals('Test Category Modification Name', $CategoryMaterialModificationTransDTO->getName());
            $CategoryMaterialModificationTransDTO->setName('Edit Test Category Modification Name');

        }

        self::assertTrue($CategoryMaterialModificationDTO->isModification());

        self::assertTrue($CategoryMaterialModificationDTO->getPrice());
        $CategoryMaterialModificationDTO->setPrice(true);

        self::assertTrue($CategoryMaterialModificationDTO->getImage());
        $CategoryMaterialModificationDTO->setImage(false);

        self::assertTrue($CategoryMaterialModificationDTO->getQuantitative());
        $CategoryMaterialModificationDTO->setQuantitative(false);

        self::assertTrue($CategoryMaterialModificationDTO->getReference()->getType() === 'input');


        //        /** @var CategoryMaterialHandler $CategoryMaterialHandler */
        //        $CategoryMaterialHandler = self::getContainer()->get(CategoryMaterialHandler::class);
        //        $handle = $CategoryMaterialHandler->handle($CategoryMaterialDTO);
        //
        //        self::assertTrue(($handle instanceof CategoryMaterial), $handle.': Ошибка CategoryMaterial');

    }

}
