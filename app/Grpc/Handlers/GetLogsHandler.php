<?php

namespace App\Grpc\Handlers;

use grpc\GetLogs\GetLogsServiceInterface;
use grpc\GetLogs\GetLogsResponse;
use grpc\GetLogs\GetLogsRequest;
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

	public function AddArchive(ContextInterface $ctx, GetLogsRequest $in): GetLogsResponse {
		Log::info('addArchive');
		
		$res = new GetLogsResponse();
		
		$fk = $in->getFk();
		$timezone = $in->getTimezone();
		$description = $in->getDescription();
		$current_page = $in->getCurrentPage();
		$per_page = $in->getPerPage();
		$search = $in->getSearch();
		$sort = $in->getSort();
		$sortColumn = $in->getSortColumn();

		if($search == '') {
			$result = UserLog::orderBy($sortColumn, $sort)
				->paginate($per_page);
		}
		else {
			$result = UserLog::where('description', 'like', '%'.$search.'%')
				->orWhere('created_at', 'like', '%'.$search.'%')
				->orWhere('created_by', 'like', '%'.$search.'%')
				->orderBy($sortColumn, $sort)
				->paginate($per_page);
		}
		
		$final = PaginationService::paginate($result, $current_page, $per_page, count($result));

		return $final;
	}

}
