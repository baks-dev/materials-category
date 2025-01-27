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

namespace BaksDev\Materials\Category\Entity\Modify;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Ip\IpAddress;
use BaksDev\Core\Type\Modify\Modify\ModifyActionNew;
use BaksDev\Core\Type\Modify\Modify\ModifyActionUpdate;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Materials\Category\Entity\Event\CategoryMaterialEvent;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Модификаторы событий Category */

#[ORM\Entity]
#[ORM\Table(name: 'material_category_modify')]
#[ORM\Index(columns: ['action'])]
class CategoryMaterialModify extends EntityState
{
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: CategoryMaterialEvent::class, inversedBy: 'modify')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ?CategoryMaterialEvent $event;

    /** Модификатор */
    #[ORM\Column(type: ModifyAction::TYPE, nullable: false)]
    private ModifyAction $action;

    /** Дата */
    #[ORM\Column(name: 'mod_date', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $modDate;

    /** ID пользователя  */
    #[ORM\Column(type: UserUid::TYPE, nullable: true)]
    private ?UserUid $usr = null;

    /** Ip адрес */
    #[ORM\Column(type: IpAddress::TYPE)]
    private IpAddress $ip;

    /** User-agent */
    #[ORM\Column(type: Types::TEXT)]
    private string $agent;


    public function __construct(CategoryMaterialEvent $event)
    {
        $this->event = $event;
        $this->modDate = new DateTimeImmutable();
        $this->ip = new IpAddress('127.0.0.1');
        $this->agent = 'console';
        $this->action = new ModifyAction(ModifyActionNew::class);
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryMaterialModifyInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryMaterialModifyInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function persistModifyAgent(IpAddress $ip, string $agent): void
    {
        $this->ip = $ip;
        $this->agent = $agent ?: 'console';
        $this->modDate = new DateTimeImmutable();
    }

    public function updateModifyAgent(IpAddress $ip, string $agent): void
    {
        $this->ip = $ip;
        $this->agent = $agent ?: 'console';
        $this->modDate = new DateTimeImmutable();
        $this->action = new ModifyAction(ModifyActionUpdate::class);
    }

    public function setUsr(UserUid|User|null $usr): void
    {
        $this->usr = $usr instanceof User ? $usr->getId() : $usr;
    }


    public function equals(mixed $action): bool
    {
        return $this->action->equals($action);
    }

}
