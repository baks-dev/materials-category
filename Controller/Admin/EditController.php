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

namespace BaksDev\Materials\Category\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Materials\Category\Entity;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialForm;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_MATERIALS_CATEGORY_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/material/category/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] Entity\Event\CategoryMaterialEvent $Event,
        CategoryMaterialHandler $handler,
    ): Response
    {

        /** @var CategoryMaterialDTO $CategoryMaterialDTO */
        $CategoryMaterialDTO = $Event->getDto(CategoryMaterialDTO::class);


        // Форма добавления
        $form = $this->createForm(CategoryMaterialForm::class, $CategoryMaterialDTO);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() && $form->has('Save'))
        {
            /** TODO */
            //$this->refreshTokenForm($form);

            $CategoryMaterial = $handler->handle($CategoryMaterialDTO);

            if($CategoryMaterial instanceof Entity\CategoryMaterial)
            {
                $this->addFlash('success', 'admin.success.update', 'admin.materials.category');

                return $this->redirectToRoute('materials-category:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.update', 'admin.materials.category', $CategoryMaterial);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
