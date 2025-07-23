<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\TeamLists\TeamListsRequest;
use grpc\TeamLists\TeamListsResponse;
use grpc\TeamLists\teamLists;
use grpc\TeamLists\TeamListsServiceInterface;
use App\Models\UserTeam;
use Illuminate\Support\Facades\DB;
use Log;

class TeamListsHandler extends ActionByMiddleware implements TeamListsServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function TeamLists(ContextInterface $ctx, TeamListsRequest $in): TeamListsResponse {
		Log::info("TeamLists running...");

		$response = new TeamListsResponse();

		$teamLists = UserTeam::get();

		$teamArray = [];
		foreach($teamLists as $teamList) {
			$teams = new teamLists([
				'id' => $teamList->id,
				'team_name' => $teamList->team_name,
				'description' => $teamList->description,
				'created_at' => $teamList->created_at,
				'updated_at' => $teamList->updated_at
			]);
			$teamArray[] = $teams;
		}
		$response->setTeamLists($teamArray);

		return $response;
	}	

}