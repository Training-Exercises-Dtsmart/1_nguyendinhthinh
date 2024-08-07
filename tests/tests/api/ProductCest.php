<?php


namespace tests\api;

use ApiTester;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Helper\Api;

class ProductCest
{
    private $authToken;
    private $faker;

    /**
     * @throws \Exception
     */
    public function _before(ApiTester $I)
    {
        $this->faker = Factory::create();
        $I->sendPOST('auth/login', [
            'username' => 'dinhthinh6',
            'password' => '123456A#'
        ]);
        $I->seeResponseCodeIs(200);
        $this->authToken = $I->grabDataFromResponseByJsonPath('$.data.token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->authToken);
    }

    public function getAllProducts(ApiTester $I)
    {
        $I->sendGET("product");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->comment($I->grabResponse()); // This will print out the response
        $I->seeResponseIsJson();
    }


    public function createProduct(ApiTester $I)
    {
        $this->faker = Factory::create();
        $I->sendPOST('product/create', [
            'name' => $this->faker->name(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'description' => $this->faker->word(),
            'stock' => 0,
            'category_product_id' => 1
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}
