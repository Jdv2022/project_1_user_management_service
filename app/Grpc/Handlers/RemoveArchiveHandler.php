<?php

namespace App\Grpc\Handlers;

use grpc\RemoveArchive\RemoveArchiveServiceInterface;
use grpc\RemoveArchive\RemoveArchiveResponse;
use grpc\RemoveArchive\RemoveArchiveRequest;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;
use Log;

class RemoveArchiveHandler extends ActionByMiddleware implements RemoveArchiveServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function RemoveArchive(ContextInterface $ctx, RemoveArchiveRequest $in): RemoveArchiveResponse {
		Log::info('removeArchive');
		
		$res = new RemoveArchiveResponse();

		$actionByUserId = $in->getActionByUserId();
		$timezone = $in->getTimezone();
		$userId = $in->getUserId();

		$this->initializeActionByUser((int)$actionByUserId, $timezone);

		$userDetail = UserDetail::find($userId);
		if($userDetail) {			
			$userDetail->update([
				'is_archive' => false,
			]);
			return $res->setResult(true);
		}
		else {
			return $res->setResult(false);
		}
	}

}
