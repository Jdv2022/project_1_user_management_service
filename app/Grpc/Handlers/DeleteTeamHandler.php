<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\DeleteTeam\DeleteTeamResponse;
use grpc\DeleteTeam\DeleteTeamRequest;
use grpc\DeleteTeam\DeleteTeamServiceInterface;
use App\Models\UserTeam;
use App\Models\UserDetail;
use App\Models\UserDetailUserTeam;
use Log;

class DeleteTeamHandler extends ActionByMiddleware implements DeleteTeamServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function DeleteTeam(ContextInterface $ctx, DeleteTeamRequest $in): DeleteTeamResponse {
		Log::info("DeleteTeamHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$teamId = $in->getTeamId();
		$userId = $in->getUserId();
		$tz = $in->getTimezone();
		$actionByUserId = $in->getActionByUserId();

		$this->initializeActionByUser((int)$actionByUserId, $tz);

		$deletePivotRecord = UserDetailUserTeam::where('user_team_id', $teamId)
			->delete();

		$deleteTeamRecord = UserTeam::where('id', $teamId)->delete();
		$response = new DeleteTeamResponse();
		return $response->setResult(true);
	}

}