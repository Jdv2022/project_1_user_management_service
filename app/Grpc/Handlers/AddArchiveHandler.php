<?php

namespace App\Grpc\Handlers;

use grpc\AddArchive\AddArchiveServiceInterface;
use grpc\AddArchive\AddArchiveResponse;
use grpc\AddArchive\AddArchiveRequest;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use Illuminate\Support\Facades\Redis;
use App\Models\UserDetail;
use Log;

class AddArchiveHandler extends ActionByMiddleware implements AddArchiveServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function AddArchive(ContextInterface $ctx, AddArchiveRequest $in): AddArchiveResponse {
		Log::info('addArchive');
		
		$res = new AddArchiveResponse();

		$actionByUserId = $in->getActionByUserId();
		$timezone = $in->getTimezone();
		$userId = $in->getUserId();
		$archiveReason = $in->getArchiveReason();

		$this->initializeActionByUser((int)$actionByUserId, $timezone);

		$userDetail = UserDetail::find($userId);
		if($userDetail) {			
			$userDetail->update([
				'is_archive' => true,
				'archive_reason' => $archiveReason
			]);
			return $res->setResult(true);
		}
		else {
			return $res->setResult(false);
		}
	}

}
