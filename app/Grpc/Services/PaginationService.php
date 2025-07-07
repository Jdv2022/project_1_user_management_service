<?php

namespace App\Grpc\Services;

use Log;

class PaginationService {

    public static function paginate($data = [], $current_page = 1, $per_page = 10, $total_records = 100) {
		Log::debug("Pagination started");
		$first_record = ($current_page - 1) * $per_page;
		$result = collect($data)->slice($first_record, $per_page);
		$total_pages = ceil($total_records / $per_page);
		log::debug("Pagination ended");
		return [
			'current_page' => $current_page,
			'per_page' => $per_page,
			'total_pages' => $total_pages,
			'total_records' => $total_records,
			'result' => $result->values()
		];
    }

}