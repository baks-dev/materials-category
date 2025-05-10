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

use BaksDev\Materials\Category\Security\VoterDelete;
use BaksDev\Materials\Category\Type\Event\CategoryMaterialEventUid;
use BaksDev\Users\User\Tests\TestUserAccount;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group materials-category
 * @depends BaksDev\Materials\Category\UseCase\Admin\NewEdit\Tests\CategoryMaterialNewTest::class
 */
#[When(env: 'test')]
final class DeleteAdminControllerTest extends WebTestCase
{
    private const string URL = '/admin/material/category/delete/%s';

    /**
     * Доступ по роли ROLE_MATERIALS_CATEGORY_DELETE
     */
    public function testRoleSuccessful(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $usr = TestUserAccount::getModer(VoterDelete::getVoter()); // ROLE_MATERIALS_CATEGORY_DELETE

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, CategoryMaterialEventUid::TEST));

            self::assertResponseIsSuccessful();
        }
    }

    /**
     * Доступ по роли ROLE_ADMIN
     */
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
    }

    /**
     * Доступ по роли ROLE_USER
     */
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

    }

    /**
     * Доступ по без роли
     */
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

    }
}
