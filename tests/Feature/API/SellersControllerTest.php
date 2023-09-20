<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Seller;

class SellersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_sellers_endpoint(): void
    {
        $sellers = Seller::factory(3)->create();

        $response = $this->getJson('/api/sellers');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($sellers) {
            $json->whereType('0.id', 'integer');
            $json->whereType('0.name', 'string');
            $json->whereType('0.email', 'string');
            $json->whereType('0.sales_commission', 'double');

            $json->hasAll(['0.id', '0.name', '0.email', '0.sales_commission']);

            $seller = $sellers->first();

            $json->whereAll([
                '0.id' => $seller->id,
                '0.name' => $seller->name,
                '0.email' => $seller->email,
                '0.sales_commission' => $seller->sales_commission,
            ]);
        });
    }

    public function test_get_single_sellers_endpoint(): void
    {
        $seller = Seller::factory(1)->createOne();

        $response = $this->getJson('/api/sellers/'. $seller->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($seller) {

            $json->hasAll([
                'id',
                'name',
                'email',
                'sales_commission',
                'created_at',
                'updated_at'
            ]);

            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'email' => 'string',
                'sales_commission' => 'double'
            ]);

            $json->whereAll([
                'id' => $seller->id,
                'name' => $seller->name,
                'email' => $seller->email,
                'sales_commission' => $seller->sales_commission,
            ]);
        });
    }

    public function test_post_sellers_endpoint(): void
    {
        $seller = Seller::factory(1)->makeOne()->toArray();

        $response = $this->postJson('/api/sellers', $seller);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($seller) {

            $json->hasAll([
                'id',
                'name',
                'email',
                'sales_commission',
                'created_at',
                'updated_at'
            ]);

            $json->whereAll([
                'name' => $seller['name'],
                'email' => $seller['email'],
                'sales_commission' => $seller['sales_commission']
            ])->etc();
        });
    }

    public function test_post_sellers_shoult_validate_a_seller(): void
    {
        $response = $this->postJson('/api/sellers', []);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->where('errors.name.0', 'Este campo é obrigatório!');
        });
    }

    public function test_put_sellers_endpoint(): void
    {
        Seller::factory(1)->createOne();

        $seller = [
            'name' => 'Atualizando',
            'email' => 'atualizando@gmail.com',
            'sales_commission' => 10.10
        ];

        $response = $this->putJson('/api/sellers/1', $seller);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($seller) {

            $json->hasAll([
                'id',
                'name',
                'email',
                'sales_commission',
                'created_at',
                'updated_at'
            ]);

            $json->whereAll([
                'name' => $seller['name'],
                'email' => $seller['email'],
                'sales_commission' => $seller['sales_commission']
            ])->etc();
        });
    }

    public function test_patch_sellers_endpoint(): void
    {
        Seller::factory(1)->createOne();

        $seller = [
            'name' => 'Atualizando patch'
        ];

        $response = $this->patchJson('/api/sellers/1', $seller);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($seller) {

            $json->hasAll([
                'id',
                'name',
                'email',
                'sales_commission',
                'created_at',
                'updated_at'
            ]);

           $json->where('name', $seller['name']);
        });
    }

    public function test_delete_sellers_endpoint(): void
    {
        Seller::factory(1)->createOne();

        $response = $this->deleteJson('/api/sellers/1');

        $response->assertStatus(204);
    }
}
