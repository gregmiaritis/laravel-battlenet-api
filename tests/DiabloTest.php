<?php

namespace Xklusive\BattlenetApi\Test;

use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class DiabloTest extends TestCase
{
    protected $diablo;
    protected $notFoundResponseCode = 'NOTFOUND';
    protected $battleTag = 'xklusive-2711';
    protected $heroId = '67482825';
    protected $itemDataString = 'Unique_Sword_1H_101_x1';
    protected $followers = ['enchantress', 'templar', 'scoundrel'];
    protected $artisans = ['blacksmith', 'mystic', 'jeweler'];

    public function setUp()
    {
        parent::setUp();

        $this->diablo = app(\Xklusive\BattlenetApi\Services\DiabloService::class);
    }

    /** @test */
    public function api_can_fetch_career_profile()
    {
        $response = $this->diablo->getCareerProfile($this->battleTag);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('battleTag', $response->toArray());
        $this->assertEquals($this->battleTag, strtolower(str_replace("#", "-", $response->get('battleTag'))));
    }

    /** @test */
    public function api_can_fetch_hero_profile()
    {
        $response = $this->diablo->getHeroProfile($this->battleTag, $this->heroId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertEquals($this->heroId, $response->get('id'));
    }

    /** @test */
    public function api_should_fail_if_hero_id_is_invalid()
    {
        $response = $this->diablo->getHeroProfile($this->battleTag, 'a');

        $this->assertArrayHasKey('code', $response->toArray());
        $this->assertEquals($this->notFoundResponseCode, $response->get('code'));
    }

    /** @test */
    public function api_should_fail_if_battletag_is_invalid()
    {
        $response = $this->diablo->getCareerProfile('aaaa');

        $this->assertArrayHasKey('code', $response->toArray());
        $this->assertEquals($this->notFoundResponseCode, $response->get('code'));
    }

    /** @test */
    public function api_can_fetch_item_data()
    {
        $response = $this->diablo->getItem($this->itemDataString);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertEquals($this->itemDataString, $response->get('id'));
    }

    /** @test */
    public function api_should_fail_if_item_data_is_invalid()
    {
        $this->expectException(ClientException::class);

        $this->diablo->getItem('a');
    }

    /** @test */
    public function api_can_fetch_follower_data()
    {
        $rand = rand(0,2);
        $response = $this->diablo->getFollowerData($this->followers[$rand]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('slug', $response->toArray());
        $this->assertEquals($this->followers[$rand], $response->get('slug'));
    }

    /** @test */
    public function api_should_fail_if_follower_name_is_invalid()
    {
        $this->expectException(ClientException::class);

        $this->diablo->getFollowerData('fail');
    }

    /** @test */
    public function api_can_fetch_artisan_data()
    {
        $rand = rand(0,2);
        $response = $this->diablo->getArtisanData($this->artisans[$rand]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('slug', $response->toArray());
        $this->assertEquals($this->artisans[$rand], $response->get('slug'));
    }

    /** @test */
    public function api_should_fail_if_artisan_name_is_invalid()
    {
        $this->expectException(ClientException::class);

        $this->diablo->getArtisanData('fail');
    }
}