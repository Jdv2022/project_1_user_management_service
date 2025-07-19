<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\RemoveUserTeam\RemoveUserTeamResponse;
use grpc\RemoveUserTeam\RemoveUserTeamRequest;
use App\Models\UserDetailUserTeam;
use Illuminate\Support\Facades\DB;
use Log;
use grpc\RemoveUserTeam\RemoveUserTeamServiceInterface;

class RemoveUserTeamHandler extends ActionByMiddleware implements RemoveUserTeamServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function RemoveUserTeam(ContextInterface $ctx, RemoveUserTeamRequest $in): RemoveUserTeamResponse {
		Log::info("RemoveUserTeam running...");
		$howManyPerchunks = 10;

		$actionByUserId = $in->getActionByUserId();
		$userFks = $in->getFk();
		$teamId = $in->getTeamId();
		$tz = $in->getTimezone();
		
		$this->initializeActionByUser((int)$actionByUserId, $tz);

		$res = UserDetailUserTeam::where('user_team_id', $teamId)
			->where('user_detail_id', $userFks)
			->delete();

		$response = new RemoveUserTeamResponse();
		if($res) {
			return $response->setResult(true);
		}
		else {
			return $response->setResult(false);
		}
	}

}