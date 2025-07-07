<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Services\PaginationService;
use Log;

class PaginationTest extends TestCase {
    /**
     * A basic unit test example.
     */
    public function test_pagination(): void {
		$data = [
			[
				"id" => 1,
				"name" => "John Doe 1"
			],
			[
				"id" => 2,
				"name" => "Jane Doe 2"
			],
			[
				"id" => 3,
				"name" => "John Doe 3"
			],
			[
				"id" => 4,
				"name" => "Jane Doe 4"
			],
			[
				"id" => 5,
				"name" => "John Doe 5"
			]
		];
		$total = count($data);
		$per_page = 2;
		$current_page = 2;

		$result = PaginationService::paginate($data, $current_page, $per_page, $total);
		$this->assertEquals(2, $result['current_page']);
		$this->assertEquals(2, $result['per_page']);
		$this->assertEquals(3, $result['total_pages']);
		$this->assertEquals($total, $result['total_records']);
		$this->assertEquals(3, $result['result'][0]['id']);
    }
}
