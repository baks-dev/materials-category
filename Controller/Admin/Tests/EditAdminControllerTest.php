<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Materials\Category\Controller\Admin\Tests;

use BaksDev\Materials\Category\Security\VoterEdit;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialNewTest;
use BaksDev\Users\User\Tests\TestUserAccount;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('materials-category')]
final class EditAdminControllerTest extends WebTestCase
{
    private const string URL = '/admin/material/category/edit/%s';

    /**
     * Доступ по роли ROLE_MATERIALS_CATEGORY_EDIT
     */
    #[DependsOnClass(CategoryMaterialNewTest::class)]
    public function testRoleSuccessful(): void
    {

        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {

            $usr = TestUserAccount::getModer(VoterEdit::getVoter()); // ROLE_MATERIALS_CATEGORY_EDIT

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, CategoryMaterialEventUid::TEST));

            self::assertResponseIsSuccessful();
        }


        self::assertTrue(true);
    }

    /**
     * Доступ по роли ROLE_ADMIN
     */
    #[DependsOnClass(CategoryMaterialNewTest::class)]
    public function testRoleAdminSuccessful(): void
    {

        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $usr = TestUserAccount::getAdmin();

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, CategoryMaterialEventUid::TEST));

            self::assertResponseIsSuccessful();
        }


        self::assertTrue(true);
    }

    /**
     * Доступ по роли ROLE_USER
     */
    #[DependsOnClass(CategoryMaterialNewTest::class)]
    public function testRoleUserDeny(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $usr = TestUserAccount::getUsr();

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, CategoryMaterialEventUid::TEST));

            self::assertResponseStatusCodeSame(403);
        }


        self::assertTrue(true);
    }

    /**
     * Доступ по без роли
     */
    #[DependsOnClass(CategoryMaterialNewTest::class)]
    public function testGuestFiled(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);

            $client->request('GET', sprintf(self::URL, CategoryMaterialEventUid::TEST));

            // Full authentication is required to access this resource
            self::assertResponseStatusCodeSame(401);
        }

        self::assertTrue(true);
    }
}
