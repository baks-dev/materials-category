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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Materials\Category\BaksDevMaterialsCategoryBundle;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventType;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialType;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;
use BaksDev\Materials\Category\Type\Landing\Id\CategoryMaterialLandingType;
use BaksDev\Materials\Category\Type\Landing\Id\CategoryMaterialLandingUid;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersType;
use BaksDev\Materials\Category\Type\Offers\Id\CategoryMaterialOffersUid;
use BaksDev\Materials\Category\Type\Offers\Modification\CategoryMaterialModificationType;
use BaksDev\Materials\Category\Type\Offers\Modification\CategoryMaterialModificationUid;
use BaksDev\Materials\Category\Type\Offers\Type\CategoryMaterialModificationTypeType;
use BaksDev\Materials\Category\Type\Offers\Type\CategoryMaterialModificationTypeUid;
use BaksDev\Materials\Category\Type\Offers\Variation\CategoryMaterialVariationType;
use BaksDev\Materials\Category\Type\Offers\Variation\CategoryMaterialVariationUid;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialType;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use BaksDev\Materials\Category\Type\Section\Field\Const\CategoryMaterialSectionFieldConst;
use BaksDev\Materials\Category\Type\Section\Field\Const\CategoryMaterialSectionFieldConstType;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldType;
use BaksDev\Materials\Category\Type\Section\Field\Id\CategoryMaterialSectionFieldUid;
use BaksDev\Materials\Category\Type\Section\Id\CategoryMaterialSectionType;
use BaksDev\Materials\Category\Type\Section\Id\CategoryMaterialSectionUid;
use BaksDev\Materials\Category\Type\Settings\CategoryMaterialSettingsIdentifier;
use BaksDev\Materials\Category\Type\Settings\CategoryMaterialSettingsType;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {

    $doctrine->dbal()->type(CategoryMaterialSettingsIdentifier::TYPE)->class(CategoryMaterialSettingsType::class);
    $doctrine->dbal()->type(CategoryMaterialSectionFieldUid::TYPE)->class(CategoryMaterialSectionFieldType::class);
    $doctrine->dbal()->type(CategoryMaterialSectionFieldConst::TYPE)->class(CategoryMaterialSectionFieldConstType::class);

    $doctrine->dbal()->type(CategoryMaterialOffersUid::TYPE)->class(CategoryMaterialOffersType::class);
    $container->services()->set(CategoryMaterialOffersUid::class)
        ->tag('controller.argument_value_resolver');


    $doctrine->dbal()->type(CategoryMaterialLandingUid::TYPE)->class(CategoryMaterialLandingType::class);
    $doctrine->dbal()->type(CategoryMaterialUid::TYPE)->class(CategoryMaterialType::class);
    $doctrine->dbal()->type(CategoryMaterialSectionUid::TYPE)->class(CategoryMaterialSectionType::class);
    $doctrine->dbal()->type(ParentCategoryMaterialUid::TYPE)->class(ParentCategoryMaterialType::class);
    $doctrine->dbal()->type(CategoryMaterialEventUid::TYPE)->class(CategoryMaterialEventType::class);
    $doctrine->dbal()->type(CategoryMaterialVariationUid::TYPE)->class(CategoryMaterialVariationType::class);
    $doctrine->dbal()->type(CategoryMaterialModificationUid::TYPE)->class(CategoryMaterialModificationType::class);
    $doctrine->dbal()->type(CategoryMaterialModificationTypeUid::TYPE)->class(CategoryMaterialModificationTypeType::class);


    /** Резолверы */
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CategoryMaterialUid::class)->class(CategoryMaterialUid::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);


    $emDefault->mapping('materials-category')
        ->type('attribute')
        ->dir(BaksDevMaterialsCategoryBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevMaterialsCategoryBundle::NAMESPACE.'\\Entity')
        ->alias('materials-category');
};
