<?php

namespace App\Grpc\Handlers;

use grpc\userClockIn\UserClockInServiceInterface;
use grpc\userClockIn\UserClockInRequest;
use grpc\userClockIn\UserClockInResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserAttendance;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;
use Carbon\Carbon;

class UserClockInHandler extends ActionByMiddleware implements UserClockInServiceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function UserClockInService(ContextInterface $ctx, UserClockInRequest $in): UserClockInResponse {
		$fk = $in->getFk();
		$timezone = $in->getTimezone();
		$now = Carbon::now($timezone);
		$userInstance = UserDetail::find($fk);
		if(!$userInstance) {
			throw new \Exception("User Not Found!");
		}
		$response = new UserClockInResponse();
		$this->initializeActionByUser((int)$fk, $timezone);
		Log::info("User[$fk] Clock In Timezone: " . $now->format('Y-m-d H:i:s'));

		$attendance = UserAttendance::where('user_detail_id', $fk)
			->whereNotNull('time_in')
			->whereNull('time_out')
			->orderByDesc('created_at')
			->first();

		if(!$attendance) {
			Log::info("User FIRST CLOCKIN!");
			Log::info("User Time IN SAVED!");
			$attendance = new UserAttendance();
			$attendance->create(
				[
					"user_detail_id" => $fk,
					"time_in" => $now->toTimeString(),
				]
			);
			return $response->setResult(true);
		}

		Log::debug("Check Latest Attendance: " . json_encode($attendance, JSON_PRETTY_PRINT));
		$attendanceDate = Carbon::now($attendance->time_zone);
		$date = $attendanceDate->format('Y-m-d');
		$created_at = Carbon::parse($attendance->created_at)->format('Y-m-d');
		Log::debug("Compare the Date: " . $date . " =? " . $created_at);
		$isAlreadyClockedIn = ($date == $created_at) 
			? true 
			: false;

		if($isAlreadyClockedIn) {
			Log::info("User Already Timed IN!");
			return $response->setResult(false);
		}
		else {
			Log::info("User Time IN SAVED!");
			$attendance = new UserAttendance();
			$attendance->create(
				[
					"user_detail_id" => $fk,
					"time_in" => $now->toTimeString(),
				]
			);
			
			return $response->setResult(true);
		}
	}

}