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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit\Trans;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Entity\Trans\CategoryMaterialTransInterface;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryMaterialTransDTO implements CategoryMaterialTransInterface
{
    #[Assert\Uuid]
    private ?CategoryMaterialEventUid $event = null;

    /** Локаль */
    #[Assert\NotBlank]
    private readonly Locale $local;

    /** Название раздела (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w \.\_\-\(\)\%]+$/iu')]
    private ?string $name = null;

    /** Краткое описание */
    #[Assert\Regex(pattern: '/^[\w \,\'\.\_\-\(\)\%]+$/iu')]
    private ?string $description = null;


    public function withEvent(CategoryMaterialEvent|CategoryMaterialEventUid $event): void
    {
        $this->event = $event instanceof CategoryMaterialEvent ? $event->getId() : $event;
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


    /** Название раздела */

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** Краткое описание */

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

}
