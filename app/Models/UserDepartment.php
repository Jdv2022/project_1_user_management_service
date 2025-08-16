<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDepartment extends __SystemBaseModel {
    
	use HasFactory;

	protected $guarded = [];

	public function userDetails() {
		return $this->belongsToMany(UserDetail::class, 'user_detail_user_departments', 'user_department_id', 'user_detail_id')
			->withPivot([
				'id',
				'created_at',
				'created_at_timezone',
				'created_by_user_id',
				'created_by_username',
				'created_by_user_type',
				'updated_at',
				'updated_at_timezone',
				'updated_by_user_id',
				'updated_by_username',
				'updated_by_user_type',
				'enabled'
			]);	
	}

}
