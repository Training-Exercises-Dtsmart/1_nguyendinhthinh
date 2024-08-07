<?php

namespace tests\api;

use ApiTester;
use Codeception\Template\Api;
use Codeception\Util\HttpCode;
use Faker\Factory;

class UserCest
{
    private $faker;

    public function _before(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
    }

    public function register(ApiTester $I)
    {
        $faker = Factory::create();
        $username = $faker->userName();
        $email = $faker->email();
        $password = $faker->password();

        $I->wantTo('register a user');
        $I->sendPost('auth/register', [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'status' => 1
        ]);


        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function loginWithWrong(ApiTester $I)
    {
        $faker = Factory::create();
        $username = $faker->userName();
        $password = $faker->password();
        $I->wantTo('login using wrong password');
        $I->sendPost('auth/login', [
            'username' => $username,
            'password' => $password
        ]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function login(ApiTester $I)
    {
        $faker = Factory::create();
        $username = 'dinhthinh6';
        $password = '123456A#';
        $I->wantTo('login user');
        $I->sendPost('auth/login', [
            'username' => $username,
            'password' => $password
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}