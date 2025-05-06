<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends __SystemBaseModel {
	
    use HasFactory;
	
	public function userRoles() {
		return $this->belongsToMany(UserRole::class, 'user_detail_user_roles', 'user_detail_id', 'user_role_id')
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
