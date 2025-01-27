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

declare(strict_types=1);

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Offers\Variation\Modification\CategoryMaterialModification;
use BaksDev\Materials\Category\Entity\Offers\Variation\Modification\Trans\CategoryMaterialModificationTransInterface;
use BaksDev\Materials\Category\Type\Offers\Modification\CategoryMaterialModificationUid;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CategoryMaterialModificationTrans */
final class CategoryMaterialModificationTransDTO implements CategoryMaterialModificationTransInterface
{
    #[Assert\Uuid]
    private ?CategoryMaterialModificationUid $modification = null;

    /** Локаль */
    #[Assert\NotBlank]
    private readonly Locale $local;

    /** Название варианта (строка с точкой, нижнее подчеркивание тире процент скобки) */
    //#[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w \.\_\-\(\)\%]+$/iu')]
    private ?string $name = null;

    /** Название постфикса (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\_\-\(\)\%]+$/iu')]
    private ?string $postfix = null;


    public function withModification(CategoryMaterialModificationUid|CategoryMaterialModification $modification): self
    {
        $this->modification = $modification instanceof CategoryMaterialModification ? $modification->getId() : $modification;
        return $this;
    }


    /** Локаль */

    public function getLocal(): Locale
    {
        return $this->local;
    }

    /** Локаль */

    public function setLocal(Locale $local): void
    {
        if(!(new ReflectionProperty(self::class, 'local'))->isInitialized($this))
        {
            $this->local = $local;
        }
    }


    /** Название варианта */

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** Название постфикса  */
    public function getPostfix(): ?string
    {
        return $this->postfix;
    }


    public function setPostfix(?string $postfix): void
    {
        $this->postfix = $postfix;
    }


}
