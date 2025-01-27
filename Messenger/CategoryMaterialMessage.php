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

namespace BaksDev\Materials\Category\Messenger;

use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Id\CategoryMaterialUid;

final class CategoryMaterialMessage
{
    /** Идентификатор */
    private CategoryMaterialUid $id;

    /** Идентификатор события */
    private CategoryMaterialEventUid $event;

    /** Идентификатор предыдущего события */
    private ?CategoryMaterialEventUid $last;

    public function __construct(
        CategoryMaterialUid $id,
        CategoryMaterialEventUid $event,
        ?CategoryMaterialEventUid $last = null
    )
    {
        $this->last = $last;
        $this->id = $id;
        $this->event = $event;
    }


    /** Идентификатор */

    public function getId(): CategoryMaterialUid
    {
        return $this->id;
    }


    /** Идентификатор события */

    public function getEvent(): CategoryMaterialEventUid
    {
        return $this->event;
    }


    /** Идентификатор предыдущего события */

    public function getLast(): ?CategoryMaterialEventUid
    {
        return $this->last;
    }

}
