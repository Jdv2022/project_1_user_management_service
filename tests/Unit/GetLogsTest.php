<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Handlers\GetLogsHandler;
use Spiral\RoadRunner\GRPC\ContextInterface;
use grpc\getLogs\GetLogsRequest;
use grpc\getLogs\GetLogsResponse;
use grpc\getLogs\Logs;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Illuminate\Support\Facades\Redis;

class GetLogsTest extends TestCase {

    public function test_get_logs(): void {
		Log::info("Test get logs");

		$getLogsHandler = new GetLogsHandler(new CommonFunctions());

		$in = new GetLogsRequest();
		$in->setDescription('Logging test!');
		$in->setTimezone('America/New_York');
		$in->setCurrentPage(2);
		$in->setPerPage(2);
		$in->setSearch('');
		$in->setSort('');
		$in->setSortColumn('description');

		$ctx = $this->createMock(ContextInterface::class);
		$result = $getLogsHandler->GetLogs($ctx, $in);
		
		$repeatedField = $result->getLogs();
		$array = iterator_to_array($repeatedField);

		$this->assertInstanceOf(GetLogsResponse::class, $result);
		$this->assertIsArray($array); 
		$this->assertInstanceOf(Logs::class, $array[0]); 
    }

}
