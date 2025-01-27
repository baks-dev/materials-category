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

namespace BaksDev\Materials\Category\Entity\Cover;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Files\Resources\Upload\UploadEntityInterface;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Обложка раздела */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_cover')]
class CategoryMaterialCover extends EntityState implements UploadEntityInterface
{
    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: CategoryMaterialEvent::class, inversedBy: 'cover')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CategoryMaterialEvent $event;

    /** Название файла */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /** Расширение файла */
    #[Assert\NotBlank]
    #[Assert\Choice(['png', 'gif', 'jpg', 'jpeg', 'webp'])]
    #[ORM\Column(type: Types::STRING)]
    private string $ext;

    /** Размер файла */
    #[Assert\NotBlank]
    #[Assert\Range(max: 10485760)] // 1024 * 1024 * 10
    #[ORM\Column(type: Types::INTEGER)]
    private int $size = 0;

    /** Файл загружен на CDN */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $cdn = false;


    public function __construct(CategoryMaterialEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getId(): CategoryMaterialEventUid
    {
        return $this->event->getId();
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialCoverInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {

        /* Если размер файла нулевой - не заполняем сущность */
        if(
            (empty($dto->file) && empty($dto->getName())) ||
            (!empty($dto->file) && empty($dto->getName()))
        )
        {
            return false;
        }

        if($dto instanceof CategoryMaterialCoverInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function updFile(string $name, string $ext, int $size): void
    {
        $this->cdn = false;
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
        //$this->dir = $this->event->getId();
    }

    public function updCdn(?string $ext = null): void
    {
        if($ext)
        {
            $this->ext = $ext;
        }

        $this->cdn = true;
    }

    /**
     * Ext
     */
    public function getExt(): string
    {
        return $this->ext;
    }


}