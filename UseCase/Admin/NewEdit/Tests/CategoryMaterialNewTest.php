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

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialHandler;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Landing\CategoryMaterialLandingCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\CategoryMaterialOffersDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Trans\CategoryMaterialOffersTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\CategoryMaterialVariationDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\CategoryMaterialModificationDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans\CategoryMaterialModificationTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Trans\CategoryMaterialVariationTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\CategoryMaterialSectionCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Fields\CategoryMaterialSectionFieldCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\CategoryMaterialSectionFieldTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\Trans\CategoryMaterialSectionTransDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Seo\CategoryMaterialSeoCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Trans\CategoryMaterialTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('materials-category')]
class CategoryMaterialNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(CategoryMaterial::class)
            ->findOneBy(['id' => CategoryMaterialUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }


        $event = $em->getRepository(CategoryMaterialEvent::class)
            ->findBy(['category' => CategoryMaterialUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {
        /** @see CategoryMaterialDTO */
        $CategoryMaterialDTO = new CategoryMaterialDTO();


        $CategoryMaterialDTO->setSort(123);
        self::assertEquals('123', $CategoryMaterialDTO->getSort());




        /** @var CategoryMaterialTransDTO $CategoryMaterialTransDTO */
        foreach($CategoryMaterialDTO->getTranslate() as $CategoryMaterialTransDTO)
        {
            $CategoryMaterialTransDTO->setName('Test Category Name');
            self::assertEquals('Test Category Name', $CategoryMaterialTransDTO->getName());

            $CategoryMaterialTransDTO->setDescription('Test Category Description');
            self::assertEquals('Test Category Description', $CategoryMaterialTransDTO->getDescription());
        }


        /** @var CategoryMaterialOffersDTO $CategoryMaterialOffersDTO */
        $CategoryMaterialOffersDTO = $CategoryMaterialDTO->getOffer();

        /** @var CategoryMaterialOffersTransDTO $MaterialOffersTransDTO */
        foreach($CategoryMaterialOffersDTO->getTranslate() as $MaterialOffersTransDTO)
        {
            $MaterialOffersTransDTO->setName('Test Category Offer Name');
            self::assertEquals('Test Category Offer Name', $MaterialOffersTransDTO->getName());

        }

        $CategoryMaterialOffersDTO->setArticle(false);
        self::assertFalse($CategoryMaterialOffersDTO->getArticle());
        $CategoryMaterialOffersDTO->setArticle(true);
        self::assertTrue($CategoryMaterialOffersDTO->getArticle());

        $CategoryMaterialOffersDTO->setOffer(false);
        self::assertFalse($CategoryMaterialOffersDTO->isOffer());
        $CategoryMaterialOffersDTO->setOffer(true);
        self::assertTrue($CategoryMaterialOffersDTO->isOffer());


        $CategoryMaterialOffersDTO->setPrice(false);
        self::assertFalse($CategoryMaterialOffersDTO->getPrice());
        $CategoryMaterialOffersDTO->setPrice(true);
        self::assertTrue($CategoryMaterialOffersDTO->getPrice());


        $CategoryMaterialOffersDTO->setImage(false);
        self::assertFalse($CategoryMaterialOffersDTO->getImage());
        $CategoryMaterialOffersDTO->setImage(true);
        self::assertTrue($CategoryMaterialOffersDTO->getImage());


        $CategoryMaterialOffersDTO->setQuantitative(false);
        self::assertFalse($CategoryMaterialOffersDTO->getQuantitative());
        $CategoryMaterialOffersDTO->setQuantitative(true);
        self::assertTrue($CategoryMaterialOffersDTO->getQuantitative());


        $CategoryMaterialOffersDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryMaterialOffersDTO->getReference());


        /** @var CategoryMaterialVariationDTO $CategoryMaterialVariationDTO */
        $CategoryMaterialVariationDTO = $CategoryMaterialOffersDTO->getVariation();

        /** @var CategoryMaterialVariationTransDTO $CategoryMaterialVariationTransDTO */
        foreach($CategoryMaterialVariationDTO->getTranslate() as $CategoryMaterialVariationTransDTO)
        {
            $CategoryMaterialVariationTransDTO->setName('Test Category Variation Name');
            self::assertEquals('Test Category Variation Name', $CategoryMaterialVariationTransDTO->getName());

        }


        $CategoryMaterialVariationDTO->setArticle(false);
        self::assertFalse($CategoryMaterialVariationDTO->getArticle());
        $CategoryMaterialVariationDTO->setArticle(true);
        self::assertTrue($CategoryMaterialVariationDTO->getArticle());


        $CategoryMaterialVariationDTO->setVariation(false);
        self::assertFalse($CategoryMaterialVariationDTO->isVariation());
        $CategoryMaterialVariationDTO->setVariation(true);
        self::assertTrue($CategoryMaterialVariationDTO->isVariation());


        $CategoryMaterialVariationDTO->setPrice(false);
        self::assertFalse($CategoryMaterialVariationDTO->getPrice());
        $CategoryMaterialVariationDTO->setPrice(true);
        self::assertTrue($CategoryMaterialVariationDTO->getPrice());


        $CategoryMaterialVariationDTO->setImage(false);
        self::assertFalse($CategoryMaterialVariationDTO->getImage());
        $CategoryMaterialVariationDTO->setImage(true);
        self::assertTrue($CategoryMaterialVariationDTO->getImage());

        $CategoryMaterialVariationDTO->setQuantitative(false);
        self::assertFalse($CategoryMaterialVariationDTO->getQuantitative());
        $CategoryMaterialVariationDTO->setQuantitative(true);
        self::assertTrue($CategoryMaterialVariationDTO->getQuantitative());


        $CategoryMaterialVariationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryMaterialVariationDTO->getReference());


        /** @var CategoryMaterialModificationDTO $CategoryMaterialModificationDTO */
        $CategoryMaterialModificationDTO = $CategoryMaterialVariationDTO->getModification();

        /** @var CategoryMaterialModificationTransDTO $CategoryMaterialModificationTransDTO */
        foreach($CategoryMaterialModificationDTO->getTranslate() as $CategoryMaterialModificationTransDTO)
        {
            $CategoryMaterialModificationTransDTO->setName('Test Category Modification Name');
            self::assertEquals('Test Category Modification Name', $CategoryMaterialModificationTransDTO->getName());
        }


        $CategoryMaterialModificationDTO->setArticle(false);
        self::assertFalse($CategoryMaterialModificationDTO->getArticle());
        $CategoryMaterialModificationDTO->setArticle(true);
        self::assertTrue($CategoryMaterialModificationDTO->getArticle());

        $CategoryMaterialModificationDTO->setModification(false);
        self::assertFalse($CategoryMaterialModificationDTO->isModification());
        $CategoryMaterialModificationDTO->setModification(true);
        self::assertTrue($CategoryMaterialModificationDTO->isModification());

        $CategoryMaterialModificationDTO->setPrice(false);
        self::assertFalse($CategoryMaterialModificationDTO->getPrice());
        $CategoryMaterialModificationDTO->setPrice(true);
        self::assertTrue($CategoryMaterialModificationDTO->getPrice());

        $CategoryMaterialModificationDTO->setImage(false);
        self::assertFalse($CategoryMaterialModificationDTO->getImage());
        $CategoryMaterialModificationDTO->setImage(true);
        self::assertTrue($CategoryMaterialModificationDTO->getImage());

        $CategoryMaterialModificationDTO->setQuantitative(false);
        self::assertFalse($CategoryMaterialModificationDTO->getQuantitative());
        $CategoryMaterialModificationDTO->setQuantitative(true);
        self::assertTrue($CategoryMaterialModificationDTO->getQuantitative());


        $CategoryMaterialModificationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryMaterialModificationDTO->getReference());


        /** @var CategoryMaterialHandler $CategoryMaterialHandler */
        $CategoryMaterialHandler = self::getContainer()->get(CategoryMaterialHandler::class);
        $handle = $CategoryMaterialHandler->handle($CategoryMaterialDTO);

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

        self::assertTrue($dbal->fetchExist());
    }
}
