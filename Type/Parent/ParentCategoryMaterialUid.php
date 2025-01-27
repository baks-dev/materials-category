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

namespace BaksDev\Materials\Category\Type\Parent;

use App\Kernel;
use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;

class ParentCategoryMaterialUid extends Uid
{
    public const string TEST = '0188a99d-fe5b-7608-b04f-7a43b958a66a';

    public const string TYPE = 'material_parent_category';

    private ?string $option;

    private int $level;

    public function __construct(
        AbstractUid|self|string|null $value = null,
        mixed $option = null,
        mixed $level = 1
    )
    {
        parent::__construct($value);

        $this->option = $option;
        $this->level = $level;
    }

    public function getOption(): ?string
    {
        return $this->option;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

}
