<?php

namespace App\Grpc\Handlers;

use grpc\getAttendance\GetAttendanceRequest;
use grpc\getAttendance\GetAttendanceResponse;
use grpc\getAttendance\GetUserDetailsServiceInterface;
use grpc\getAttendance\Attendance;
use Spiral\RoadRunner\GRPC\ContextInterface;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Redis;
use App\Grpc\Services\CommonFunctions;
use Log;
use App\Grpc\Middlewares\ActionByMiddleware;
use grpc\getAttendance\GetAttendanceInterface;
use Carbon\Carbon;

class GetAttendanceHandler extends ActionByMiddleware implements GetAttendanceInterface {

	public function __construct(CommonFunctions $commonFunctions) {
		$this->commonFunctions = $commonFunctions;
	}

	public function GetAttendance(ContextInterface $ctx, GetAttendanceRequest $in): GetAttendanceResponse {
		Log::debug("GetAttendanceHandler running...");
		$modelResponse = new GetAttendanceResponse();

		$fk = $in->getFk();
		$date = $in->getMonth();
		$tz = $in->getTimeZone();

		Log::debug("Foreign Key: $fk, Date: $date, Timezone: $tz");
		$model = UserDetail::with('userAttendance')->find($fk);
		if(!isset($model->userAttendance)) {
			$modelResponse->getAttendance()[] = new Attendance([]);
			return $modelResponse;
		}
		Log::debug("Data to return: " . json_encode($model->userAttendance, JSON_PRETTY_PRINT));

		$carbon = Carbon::parse($date);
		$nextMonth7th = $carbon->copy()->addMonthNoOverflow()->startOfMonth()->addDays(6);
		$previousMonth21st = $carbon->copy()->subMonthNoOverflow()->startOfMonth()->addDays(20);
		
		$startDate = $nextMonth7th->toDateString();      
		$endDate = $previousMonth21st->toDateString(); 
		Log::debug("Date Range: " . $startDate . " - " . $endDate);

		$now = Carbon::now($tz)->format('Y-m');
		$attendace = $model->userAttendance;
		$returnDays = $attendace->whereBetween('created_at', [$endDate, $startDate]);

		foreach ($returnDays as $item) {
			$attendance = new Attendance([
				'time_in' => $item->time_in ? Carbon::parse($item->time_in)->format('g:i a') : '',
				'time_out' => $item->time_out ? Carbon::parse($item->time_out)->format('g:i a') : '',
				'time_in_status' => $item->time_in_status ?? '',
				'time_out_status' => $item->time_out_status ?? '',
				'created_at' => $item->created_at ? Carbon::parse($item->created_at)->format('l F j') : ''
			]);
			$modelResponse->getAttendance()[] = $attendance;
		}
		Log::debug("Response: " . $modelResponse->serializeToJsonString() . PHP_EOL);
		return $modelResponse;
	}

}