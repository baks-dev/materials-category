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

namespace BaksDev\Materials\Category\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Materials\Category\Entity;
use BaksDev\Materials\Category\Entity as CategoryEntity;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialForm;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\CategoryMaterialHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_MATERIALS_CATEGORY_NEW')]
final class NewController extends AbstractController
{
    #[Route(
        '/admin/material/category/new/{cat}/{id}',
        name: 'admin.newedit.new',
        defaults: ['cat' => null, 'id' => null],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryMaterialHandler $handler,
        #[MapEntity] ?CategoryEntity\CategoryMaterial $cat = null,
        ?CategoryMaterialEventUid $id = null,
    ): Response
    {


        $parent = $cat ? new ParentCategoryMaterialUid($cat->getId()) : null;
        $Event = $id ? $entityManager->getRepository(CategoryEntity\Event\CategoryMaterialEvent::class)->find(
            $id
        ) : null;

        $category = new CategoryMaterialDTO($parent);

        // Копируем данные из события
        if($Event)
        {
            $Event->getDto($category);
            // $category->copy();
        }

        // Форма добавления
        $form = $this->createForm(CategoryMaterialForm::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('Save'))
        {
            //dd($request->request);

            //$this->refreshTokenForm($form);

            $CategoryMaterial = $handler->handle($category);

            if($CategoryMaterial instanceof Entity\CategoryMaterial)
            {
                $this->addFlash('success', 'admin.success.new', 'admin.materials.category');

                return $this->redirectToRoute('materials-category:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.new', 'admin.materials.category', $CategoryMaterial);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
