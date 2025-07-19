<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\EditTeam\EditTeamResponse;
use grpc\EditTeam\EditTeamRequest;
use grpc\EditTeam\EditTeamServiceInterface;
use App\Models\UserTeam;
use Log;

class EditTeamHandler extends ActionByMiddleware implements EditTeamServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function EditTeam(ContextInterface $ctx, EditTeamRequest $in): EditTeamResponse {
		Log::info("EditTeamHandler running...");

		$actionByUserId = $in->getActionByUserId();
		$teamName = $in->getTeamName();
		$description = $in->getDescription();
		$tz = $in->getTimezone();
		$team_id = $in->getTeamId();
		Log::debug($actionByUserId);
		$this->initializeActionByUser((int)$actionByUserId, $tz);

		$model = UserTeam::find($team_id);
		$model->team_name = $teamName;
		$model->description = $description;
		$model->save();

		$response = new EditTeamResponse();
		if($model->save()) {
			Log::info("Team Edited! ID: " . $model->id);
			return $response->setResult($model->id);
		}
		else {
			Log::info("Team Not Edited!");
			return $response->setResult("Team Not Edited!");
		}
	}

}