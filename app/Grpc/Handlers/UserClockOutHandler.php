<?php

namespace App\Grpc\Handlers;

use grpc\userClockOut\UserClockOutServiceInterface;
use grpc\userClockOut\UserClockOutRequest;
use grpc\userClockOut\UserClockOutResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserAttendance;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use App\Models\UserDetail;
use App\Grpc\Middlewares\ActionByMiddleware;
use Carbon\Carbon;
use Log;

class UserClockOutHandler extends ActionByMiddleware implements UserClockOutServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function SetUserClockOut(ContextInterface $ctx, UserClockOutRequest $in): UserClockOutResponse {
		$fk = $in->getFk();
		$timezone = $in->getTimezone();
		Log::debug($timezone);
		$now = Carbon::now($timezone);
		$userInstance = UserDetail::find($fk);
		if(!$userInstance) {
			throw new \Exception("User Not Found!");
		}
		$response = new UserClockOutResponse();
		$this->initializeActionByUser((int)$fk, $timezone);
		Log::info("User[$fk] Clock Out Timezone: " . $now->format('Y-m-d H:i:s'));

		$attendance = UserAttendance::where('user_detail_id', $fk)
			->whereNotNull('time_in')
			->whereNull('time_out')
			->orderByDesc('created_at')
			->first();

		if(!$attendance) {
			Log::info("User Already Timed OUT!");
			return $response->setResult(false);
		}

		Log::debug("Check Latest Attendance: " . json_encode($attendance, JSON_PRETTY_PRINT));
		$attendanceDate = Carbon::now($attendance->updated_at_timezone);
		$date = $attendanceDate->format('Y-m-d');

		$attendance->update(
			[
				'time_out' => Carbon::now($attendance->created_at_timezone)
			]
		);
		Log::info("User Time out SAVED!");
		
		return $response->setResult(true);
	}

}