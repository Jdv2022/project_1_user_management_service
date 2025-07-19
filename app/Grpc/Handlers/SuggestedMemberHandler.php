<?php

namespace App\Grpc\Handlers;

use App\Grpc\Middlewares\ActionByMiddleware;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Services\CommonFunctions;
use grpc\SuggestedMember\SuggestedMemberRequest;
use grpc\SuggestedMember\SuggestedMemberResponse;
use grpc\SuggestedMember\member;
use grpc\SuggestedMember\SuggestedMemberServiceInterface;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use Log;

class SuggestedMemberHandler extends ActionByMiddleware implements SuggestedMemberServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function SuggestedMember(ContextInterface $ctx, SuggestedMemberRequest $in): SuggestedMemberResponse {
		Log::info("SuggestedMember running...");

		$response = new SuggestedMemberResponse();

		$suggestedMember = UserDetail::with('userTeams')->get();

		$teamArray = [];
		foreach($suggestedMember as $teamList) {
			$is_on_team = true;
			if($teamList->userTeams->isEmpty()) {
				$is_on_team = false;
			}
			$teams = new member([
				'id' => $teamList->id,
				'name' => $teamList->team_name,
				'department' => $teamList->description,
				'is_on_team' => $is_on_team,
				'profile_image_url' => $teamList->updated_at,
				'profile_image_name' => $teamList->updated_at,
			]);
			$teamArray[] = $teams;
		}

		$response->setTeamLists($teamArray);

		return $response;
	}	

}