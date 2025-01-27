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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Materials\Category\Entity\CategoryMaterial;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Messenger\CategoryMaterialMessage;
use DomainException;

final class CategoryMaterialHandler extends AbstractHandler
{
    public function handle(CategoryMaterialDTO $command): string|CategoryMaterial
    {

        //        if($command->getOffer()?->isOffer())
        //        {
        //            $offer = $command->getOffer();
        //
        //            if($offer?->getVariation()->isVariation())
        //            {
        //                $variation = $offer?->getVariation();
        //
        //                if($variation->getModification()->isModification())
        //                {
        //
        //
        //                }
        //
        //            }
        //
        //        }

        /** Делаем сброс иерархии настроек торговых предложений  */
        $command->resetOffer();

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new CategoryMaterial();
        $this->event = new CategoryMaterialEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, false) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }


        /** Загружаем файл обложки раздела */

        if(method_exists($command, 'getCover'))
        {
            $Cover = $command->getCover();

            if($Cover && $Cover->file !== null)
            {
                $CategoryMaterialCover = $this->event->getUploadCover();
                $this->imageUpload->upload($Cover->file, $CategoryMaterialCover);
            }
        }


        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }


        $this->entityManager->flush();

        /* Отправляем событие в шину  */
        $this->messageDispatch->dispatch(
            message: new CategoryMaterialMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'materials-category'
        );

        return $this->main;
    }


}
