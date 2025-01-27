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

namespace BaksDev\Materials\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEventInterface;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\Type\Parent\ParentCategoryMaterialUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Cover\CategoryMaterialCoverDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Info\CategoryMaterialInfoDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Landing\CategoryMaterialLandingCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Offers\CategoryMaterialOffersDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Section\CategoryMaterialSectionCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Seo\CategoryMaterialSeoCollectionDTO;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Trans\CategoryMaterialTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryMaterialDTO implements CategoryMaterialEventInterface
{
    /** Идентификатор события */
    #[Assert\Uuid]
    private ?CategoryMaterialEventUid $id = null;

    /** Идентификатор родительской категории */
    #[Assert\Uuid]
    private ?ParentCategoryMaterialUid $parent;

    /**  Сортировка категории */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 500;

    /** Настройки локали категории */
    #[Assert\Valid]
    private ArrayCollection $translate;

    /** Настройки локали категории */
    #[Assert\Valid]
    private ArrayCollection $domain;


    /** Секции свойств продукта категории */
    #[Assert\Valid]
    private ArrayCollection $section;

    /** Посадочные блоки */
    #[Assert\Valid]
    private ArrayCollection $landing;

    /** Торговые предложения */
    #[Assert\Valid]
    private ?CategoryMaterialOffersDTO $offer;

    /** Настройки SEO категории */
    #[Assert\Valid]
    private ArrayCollection $seo;

    /** Обложка категории */
    #[Assert\Valid]
    private ?CategoryMaterialCoverDTO $cover;

    /** Неизменяемые свойства категории */
    #[Assert\Valid]
    private CategoryMaterialInfoDTO $info;

    /**  Модификатор события  */
    #[Assert\Valid]
    private readonly Modify\CategoryMaterialModifyDTO $modify;


    public function __construct(
        ?ParentCategoryMaterialUid $parent = null,
        //CategoryEvent $event = null,

        // bool $active = true,
        // string $url = null,
    )
    {
        $this->parent = $parent;

        $this->cover = new CategoryMaterialCoverDTO();
        $this->info = new CategoryMaterialInfoDTO();
        $this->modify = new Modify\CategoryMaterialModifyDTO();
        $this->offer = new Offers\CategoryMaterialOffersDTO();

        $this->translate = new ArrayCollection();
        $this->landing = new ArrayCollection();
        $this->section = new ArrayCollection();
        $this->seo = new ArrayCollection();
        $this->domain = new ArrayCollection();

    }

    /** Идентификатор события */

    public function getEvent(): ?CategoryMaterialEventUid
    {
        return $this->id;
    }

    /** Идентификатор родительской категории */

    public function getParent(): ?ParentCategoryMaterialUid
    {
        return $this->parent;
    }


    public function setParent(?ParentCategoryMaterialUid $parent): void
    {
        $this->parent = $parent?->getValue() ? $parent : null;
    }

    /**  Сортировка категории */

    public function getSort(): int
    {
        return $this->sort;
    }


    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }


    /** Неизменяемые свойства категории INFO */

    public function getInfo(): CategoryMaterialInfoDTO
    {
        return $this->info;
    }


    public function setInfo(CategoryMaterialInfoDTO $info): void
    {
        $this->info = $info;
    }


    /** Настройки локали категории */


    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $CategoryTransDTO = new CategoryMaterialTransDTO();
            $CategoryTransDTO->setLocal($locale);
            $this->addTranslate($CategoryTransDTO);
        }

        return $this->translate;
    }

    public function addTranslate(CategoryMaterialTransDTO $trans): void
    {
        if(empty($trans->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->translate->contains($trans))
        {
            $this->translate->add($trans);
        }
    }

    public function removeTranslate(CategoryMaterialTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }


    /** Посадочные блоки */

    public function getLanding(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->landing) as $locale)
        {
            $CategoryLandingDTO = new CategoryMaterialLandingCollectionDTO();
            $CategoryLandingDTO->setLocal($locale);
            $this->addLanding($CategoryLandingDTO);
        }

        return $this->landing;
    }

    public function addLanding(CategoryMaterialLandingCollectionDTO $landing): void
    {
        if(empty($landing->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->landing->contains($landing))
        {
            $this->landing->add($landing);
        }
    }

    public function removeLanding(CategoryMaterialLandingCollectionDTO $landing): void
    {
        $this->landing->removeElement($landing);
    }


    /** Настройки SEO категории */


    public function getSeo(): ArrayCollection
    {

        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->seo) as $locale)
        {
            $CategorySeoDTO = new CategoryMaterialSeoCollectionDTO();
            $CategorySeoDTO->setLocal($locale);
            $this->addSeo($CategorySeoDTO);
        }

        return $this->seo;
    }

    public function addSeo(CategoryMaterialSeoCollectionDTO $seo): void
    {
        if(empty($seo->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->seo->contains($seo))
        {
            $this->seo[] = $seo;
        }
    }

    public function removeSeo(CategoryMaterialSeoCollectionDTO $seo): void
    {
        $this->seo->removeElement($seo);
    }


    /** Секции свойств продукта категории */

    public function getSection(): ArrayCollection
    {
        return $this->section;
    }

    public function addSection(CategoryMaterialSectionCollectionDTO $section): void
    {
        if(!$this->section->contains($section))
        {
            $this->section->add($section);
        }
    }

    public function removeSection(CategoryMaterialSectionCollectionDTO $section): void
    {
        $this->section->removeElement($section);
    }


    /** Торговые предложения */

    public function getOffer(): ?CategoryMaterialOffersDTO
    {
        return $this->offer;
    }

    public function setOffer(?CategoryMaterialOffersDTO $offer): void
    {
        $this->offer = $offer;
    }

    public function resetOffer(): void
    {
        $this->offer->resetVariation();

        if($this->offer->isOffer() === false)
        {
            $this->offer = null;
        }
    }


    /**  Модификатор события  */

    public function getModify(): Modify\CategoryMaterialModifyDTO
    {
        return $this->modify;
    }

    /** Обложка категории */

    public function getCover(): ?CategoryMaterialCoverDTO
    {
        return $this->cover;
    }

    public function setCover(?CategoryMaterialCoverDTO $cover): void
    {
        $this->cover = $cover;
    }


}
