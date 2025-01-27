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

namespace BaksDev\Materials\Category\Entity;

use BaksDev\Materials\Category\Type\Settings\CategoryMaterialSettingsIdentifier;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/* Настройки сущности Category */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_settings')]
class CategoryMaterialSettings
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryMaterialSettingsIdentifier::TYPE)]
    private CategoryMaterialSettingsIdentifier $id;

    /** Очищать корзину старше n дней */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false)]
    private int $truncate = 365;


    /** Очищать события старше n дней */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false)]
    private int $history = 365;

    public function __construct()
    {
        $this->id = new CategoryMaterialSettingsIdentifier();
    }

    //
    //    public function setSettings(int $settingsTruncate, int $settingsHistory) : void
    //    {
    //        $this->truncate = $settingsTruncate;
    //        $this->history = $settingsHistory;
    //    }
    //
}
