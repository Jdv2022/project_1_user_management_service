<?php

namespace App\Grpc\Handlers;

use grpc\Overview\OverviewServiceInterface;
use grpc\Overview\OverviewResponse;
use grpc\Overview\OverviewRequest;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Grpc\Middlewares\ActionByMiddleware;
use App\Grpc\Services\CommonFunctions;
use App\Grpc\Services\PaginationService;
use Illuminate\Support\Facades\Redis;
use App\Models\UserAccessCounter;
use App\Models\UserRole;
use App\Models\UserDetail;
use App\Models\UserDetailUserRole;
use Carbon\Carbon;
use grpc\Overview\UserDetailUserRole as UserDetailUserRoleGRPC;
use grpc\Overview\UserAccessCounter as UserAccessCounterGRPC;
use grpc\Overview\UserDetail as UserDetailGRPC;
use Log;

class OverviewHandler extends ActionByMiddleware implements OverviewServiceInterface{

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function overview(ContextInterface $ctx, OverviewRequest $in): OverviewResponse {
		Log::info('addArchive');
		
		$res = new OverviewResponse();
		
		$fk = $in->getActionByUserId();
		$timezone = $in->getTimezone();

		$userAccessCounter = UserAccessCounter::select(['count', 'created_at'])->get();
		$grpcItems = $userAccessCounter->map(function ($item) {
			$item->created_at = Carbon::parse($item->created_at)->format('Y-m');
			return new UserAccessCounterGRPC($item->toArray());
		});		
		$res->setUserAccessCounter($grpcItems->all());

		$userDetail = UserDetail::select('id', 'created')
			->get();
		$monthlyCounts = $userDetail
			->groupBy(function ($item) {
				return Carbon::parse($item->created)->format('Y-m');
			})
			->map(function ($group) {
				return count($group); 
			});
		$userDataList = $monthlyCounts->map(function ($value, $key) {
			return new UserDetailGRPC([
				'date' => $key,
				'totalUser' => $value,
			]);
		});
		$res->setUserDetail($userDataList->all());

		$authCount = UserDetailUserRole::get();
		$authCounts = $authCount
			->groupBy('user_role_id')
			->values();
		$roles = UserRole::get();
		$userDetailUserRoleList = $authCounts->map(function ($item) use($roles) {
			$userRoles = $roles->where('id', $item[0]->user_role_id)->first();
			$data = [
				'user_role_id' => $item[0]->user_role_id,
				'user_role' => $userRoles->type_1,
				'count' => count($item),
			];
			return new UserDetailUserRoleGRPC($data);
		});
		$res->setUserDetailUserRole($userDetailUserRoleList->all());

		return $res;
	}

}
