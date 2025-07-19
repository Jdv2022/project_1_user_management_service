<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\CreateTeam\CreateTeamResponse;
use grpc\CreateTeam\CreateTeamRequest;
use grpc\CreateTeam\CreateTeamServiceInterface;
use App\Models\UserTeam;
use Log;

class CreateTeamHandler extends ActionByMiddleware implements CreateTeamServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function CreateTeam(ContextInterface $ctx, CreateTeamRequest $in): CreateTeamResponse {
		Log::info("CreateTeamHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$teamName = $in->getTeamName();
		$description = $in->getDescription();
		$tz = $in->getTimezone();

		$this->initializeActionByUser((int)$actionByUserId, $tz);

		if(UserTeam::where('team_name', $teamName)->exists()) {
			Log::debug("Team Already Exists!");
			return new CreateTeamResponse(['result' => "Team Already Exists!"]);
		}

		$result = UserTeam::create([
			"team_name" => $teamName,
			"description" => $description
		]);
		Log::debug("Team Created: " . json_encode($result, JSON_PRETTY_PRINT));
		$response = new CreateTeamResponse();
		if($result) {
			Log::info("Team Created! ID: " . $result->id);
			return $response->setResult($result->id);
		}
		else {
			Log::info("Team Not Created!");
			return $response->setResult("Team Not Created!");
		}
	}

}