<?php

namespace App\Grpc\Handlers;

use grpc\GetLogs\GetLogsServiceInterface;
use grpc\GetLogs\GetLogsResponse;
use grpc\GetLogs\GetLogsRequest;
use grpc\GetLogs\Logs;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Services\PaginationService;
use Illuminate\Support\Facades\Redis;
use App\Models\UserLog;
use Log;

class GetLogsHandler extends ActionByMiddleware implements GetLogsServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetLogs(ContextInterface $ctx, GetLogsRequest $in): GetLogsResponse {
		Log::info('addArchive');
		
		$res = new GetLogsResponse();
		
		$fk = $in->getFk();
		$timezone = $in->getTimezone();
		
		$data = UserLog::get();

		$logs = $data->map(function ($item) use ($timezone) {
			return new Logs([
				'id' => $item->id,
				'description' => $item->description,
				'created_at' => $item->created_at,
				'created_by' => $item->created_by,
			]);
		});

		$res->setLogs($logs->all());
		return $res;
	}

}
