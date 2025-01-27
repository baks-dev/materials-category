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

namespace BaksDev\Materials\Category\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Materials\Category\Entity;
use BaksDev\Materials\Category\UseCase\Admin\Delete\DeleteCategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\Delete\DeleteCategoryMaterialForm;
use BaksDev\Materials\Category\UseCase\Admin\Delete\DeleteCategoryMaterialHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_MATERIALS_CATEGORY_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/material/category/delete/{id}', name: 'admin.delete', methods: ['POST', 'GET'])]
    public function delete(
        Request $request,
        DeleteCategoryMaterialHandler $handler,
        Entity\Event\CategoryMaterialEvent $Event,
    ): Response
    {

        $category = new DeleteCategoryMaterialDTO();
        $Event->getDto($category);
        $form = $this->createForm(DeleteCategoryMaterialForm::class, $category, [
            'action' => $this->generateUrl('materials-category:admin.delete', ['id' => $category->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('delete'))
        {
            $this->refreshTokenForm($form);

            $CategoryMaterial = $handler->handle($category);

            if($CategoryMaterial instanceof Entity\CategoryMaterial)
            {
                $this->addFlash('admin.form.header.delete', 'admin.success.delete', 'admin.materials.category');

                return $this->redirectToRoute('materials-category:admin.index');
            }
            $this->addFlash(
                'admin.form.header.delete',
                'admin.danger.delete',
                'admin.materials.category',
                $CategoryMaterial
            );

            return $this->redirectToRoute('materials-category:admin.index', status: 400);
        }

        return $this->render(
            [
                'form' => $form->createView(),
                'name' => $Event->getNameByLocale($this->getLocale()), // название согласно локали
            ]
        );
    }
}
