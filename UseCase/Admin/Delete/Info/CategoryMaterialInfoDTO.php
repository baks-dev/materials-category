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

namespace BaksDev\Materials\Category\UseCase\Admin\Delete\Info;

use BaksDev\Materials\Category\Entity\Info\CategoryMaterialInfoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryMaterialInfoDTO implements CategoryMaterialInfoInterface
{
    /** Семантическая ссылка на раздел (строка с тире и нижним подчеркиванием) */
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-z0-9\_\-]+$/i'
    )]
    private readonly string $url;

    /** Статус активности раздела */
    private readonly bool $active;


    public function __construct()
    {
        /* меняем семантическую ссылку на раздел и деактивируем */
        $this->url = uniqid('', false);
        $this->active = false;
    }

    /** Семантическая ссылка на раздел */

    public function getUrl(): ?string
    {
        return $this->url;
    }


    /** Статус активности раздела */

    public function getActive(): bool
    {
        return $this->active;
    }

}
