<?php

namespace App\Grpc\Handlers;

use grpc\GetArchives\GetArchivesRequest;
use grpc\GetArchives\GetArchivesResponse;
use grpc\GetArchives\GetArchivesServiceInterface;
use grpc\GetArchives\User;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;

class GetArchivesHandler extends ActionByMiddleware implements GetArchivesServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetArchives(ContextInterface $ctx, GetArchivesRequest $in): GetArchivesResponse {
		Log::info("Get Archives running...");
		   
		$archived = UserDetail::where('is_archived', true)->get();
		$res = new GetArchivesResponse();

		$arr = [];
		foreach($archived as $item) {
			$arc = new Archives([
				'id' => $item->id,
				'first_name' => $item->first_name,
				'middle_name' => $item->middle_name,
				'last_name' => $item->last_name,
				'position' => $item->position,
				'archived_reason' => $item->archived_reason,
				'archived_at' => $item->archived_at
			]);
			$arr[] = $arc;
		}
		$res->setArchives($arr);

		return $res;
	}

}
