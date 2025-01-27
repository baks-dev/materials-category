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

namespace BaksDev\Materials\Category\Repository\CategoryVariationForm;

use BaksDev\Materials\Category\Type\Offers\Id\OffersUid;
use BaksDev\Materials\Category\Type\Offers\Variation\CategoryMaterialVariationUid;

final class CategoryMaterialVariationFormDTO
{
    public CategoryMaterialVariationUid $id;

    public ?string $name;
    public ?string $reference;
    public bool $image;
    public bool $price;
    public bool $quantitative;
    public bool $article;
    public bool $postfix;
    public ?string $postfixName;

    public function __construct(
        CategoryMaterialVariationUid $id,
        ?string $reference,
        bool $image,
        bool $price,
        bool $quantitative,
        bool $article,
        ?string $name,
        bool $postfix,
        ?string $postfixName,
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->reference = $reference;
        $this->image = $image;
        $this->price = $price;
        $this->article = $article;
        $this->quantitative = $quantitative;
        $this->postfix = $postfix;
        $this->postfixName = $postfixName;
    }

}
