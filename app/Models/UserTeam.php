<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTeam extends __SystemBaseModel {
	protected $guarded = [];

	public function userDetails() {
		return $this->belongsToMany(UserDetail::class, 'user_detail_user_teams', 'user_detail_id', 'user_team_id')
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
