<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\CreateTeam\CreateTeamResponse;
use grpc\CreateTeam\CreateTeamRequest;
use App\Models\UserTeam;
use Log;

class CreateTeamHandler extends ActionByMiddleware {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function CreateTeam(ContextInterface $ctx, CreateTeamRequest $in): CreateTeamResponse {
		Log::info("CreateTeamHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$teamName = $in->getTeamName();
		$description = $in->getDescription();

		$this->initializeActionByUser((int)$actionByUserId);

		$team = new UserTeam();
		$result = $team->create([
			"team_name" => $teamName,
			"description" => $description
		]);

		$response = new CreateTeamResponse();
		if($result) {
			Log::info("Team Created!");
			return $response->setResult(true);
		}
		else {
			Log::info("Team Not Created!");
			return $response->setResult(false);
		}
	}

}