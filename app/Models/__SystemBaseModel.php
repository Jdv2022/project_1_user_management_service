<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;
use App\Grpc\Services\ActionByUserService;

class __SystemBaseModel extends Model
{
    public $timestamps = false;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

	public static function customInsert(array $attributes = []) {
		$now = Carbon::now();
		foreach($attributes as &$attribute) {
			$attribute['created_at'] = $now;
			$attribute['created_at_timezone'] = '+08:00';
			$attribute['created_by_user_id'] = 1;
			$attribute['created_by_username'] = 'JD';
			$attribute['created_by_user_type'] = 'Admin';
			$attribute['updated_at'] = $now;
			$attribute['updated_at_timezone'] = '+08:00';
			$attribute['updated_by_user_id'] = 1;
			$attribute['updated_by_username'] = 'JD';
			$attribute['updated_by_user_type'] = 'Admin';	
		}
		Log::info("Custom Inserting Attributes...");
		Log::debug($attributes);
		parent::insert($attributes);
	}

    /* Set Default Attributes */
    protected static function boot() {
        parent::boot();

		// Disable this CLASS when running this commands
		if(app()->runningInConsole()) {
			static::creating(function ($model) {
				$now = Carbon::now();
				if($model->hasColumn('created_at')) $model->created_at = $now;
				if($model->hasColumn('created_at_timezone')) $model->created_at_timezone = '+08:00';
				if($model->hasColumn('created_by_user_id')) $model->created_by_user_id = 1;
				if($model->hasColumn('created_by_username')) $model->created_by_username = 'JD';
				if($model->hasColumn('created_by_user_type')) $model->created_by_user_type = 'Admin';
				if($model->hasColumn('updated_at')) $model->updated_at = $now;
				if($model->hasColumn('updated_at_timezone')) $model->updated_at_timezone = '+08:00';
				if($model->hasColumn('updated_by_user_id')) $model->updated_by_user_id = 1;
				if($model->hasColumn('updated_by_username')) $model->updated_by_username = 'JD';
				if($model->hasColumn('updated_by_user_type')) $model->updated_by_user_type = 'Admin';	
			});

			static::updating(function ($model) {
				$now = Carbon::now();
				if($model->hasColumn('updated_at')) $model->updated_at = $now;
				if($model->hasColumn('updated_at_timezone')) $model->updated_at_timezone = '+08:00';
				if($model->hasColumn('updated_by_user_id')) $model->updated_by_user_id = 1;
				if($model->hasColumn('updated_by_username')) $model->updated_by_username = 'JD';
				if($model->hasColumn('updated_by_user_type')) $model->updated_by_user_type = 'Admin';	
			});

			return;
		}

        static::creating(function ($model) {
            $model->setCreatedAttributes();
            $model->setUpdatedAttributes();
        });

        static::updating(function ($model) {
            $model->setUpdatedAttributes();
        });
    }

    private function getAuthUser():array {
        return app(ActionByUserService::class)->authUser();
    }

	private function getUserTimezone():string {
        return app(ActionByUserService::class)->getUserTimeZone();
    }

    private function setCreatedAttributes():void {
        $user = $this->getAuthUser();
        $timezone = $this->getUserTimezone();
        $now = Carbon::now();

        if($this->hasColumn('created_at')) $this->created_at = $now;
        if($this->hasColumn('created_at_timezone')) $this->created_at_timezone = $timezone;
        if($this->hasColumn('created_by_user_id')) $this->created_by_user_id = $user['id'];
        if($this->hasColumn('created_by_username')) $this->created_by_username = $user['created_by_username'];
        if($this->hasColumn('created_by_user_type')) $this->created_by_user_type = $user['created_by_user_type'];
    }

    private function setUpdatedAttributes():void {
        $user = $this->getAuthUser();
        $timezone = $this->getUserTimezone();
        $now = Carbon::now();

        if($this->hasColumn('updated_at')) $this->updated_at = $now;
        if($this->hasColumn('updated_at_timezone')) $this->updated_at_timezone = $timezone;
        if($this->hasColumn('updated_by_user_id')) $this->updated_by_user_id = $user['id'];
        if($this->hasColumn('updated_by_username')) $this->updated_by_username = $user['updated_by_username'];
        if($this->hasColumn('updated_by_user_type')) $this->updated_by_user_type = $user['updated_by_user_type'];
    }

    private function hasColumn(string $column):bool {
        return in_array(
            $column, 
            $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable())
        );
    }
}
