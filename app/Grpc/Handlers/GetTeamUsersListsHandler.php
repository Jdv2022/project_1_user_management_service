<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\TeamUsersLists\TeamUsersListsRequest;
use grpc\TeamUsersLists\TeamUsersListsResponse;
use grpc\TeamUsersLists\teamUsersLists;
use grpc\TeamUsersLists\TeamUsersListsServiceInterface;
use App\Models\UserDetailUserShift;
use Illuminate\Support\Facades\DB;
use App\Models\UserTeam;
use Log;

class GetTeamUsersListsHandler extends ActionByMiddleware implements TeamUsersListsServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function TeamUsersLists(ContextInterface $ctx, TeamUsersListsRequest $in): TeamUsersListsResponse {
		Log::info("Getting Team Users List");

		$response = new TeamUsersListsResponse();

		$teamId = $in->getTeamId();
		$id = $in->getActionByUserId();

		$allUsersOnThisTeam = UserTeam::with('userDetails', 'userDetails.userRoles')
			->find($teamId);

		$returnArray = [];
		foreach($allUsersOnThisTeam->userDetails as $user_detail) {
			$type1Values = $user_detail->userRoles->pluck('type_1')->toArray();
			$returnArray[] = new teamUsersLists([
				'id' => $user_detail->id,
				'first_name' => $user_detail->first_name,
				'middle_name' => $user_detail->middle_name,
				'last_name' => $user_detail->last_name,
				'profile_image_url' => $user_detail->profile_image_url,
				'profile_image_name' => $user_detail->profile_image_name,
				'position' => implode(',', $type1Values),
			
				'created_at' => (string)$user_detail->pivot->created_at,
				'updated_at' => (string)$user_detail->pivot->updated_at,
			]);
		}
		$response->setTeamLists($returnArray ?? []);
		$response->setId($allUsersOnThisTeam->id);
		$response->setTeamName($allUsersOnThisTeam->team_name);
		$response->setDescription($allUsersOnThisTeam->description);
		$response->setCreatedAt($allUsersOnThisTeam->created_at);
		$response->setUpdatedAt($allUsersOnThisTeam->updated_at);

		return $response;
	}

}