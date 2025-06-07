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
    /* Set Default Attributes */
    protected static function boot() {
        parent::boot();

		// Disable this CLASS when running this commands
		if (app()->runningInConsole()) {
			$artisanCommandsToSkip = ['migrate', 'db:seed', 'db:wipe', 'cache:clear', 'config:cache'];
		
			$calledCommand = collect($_SERVER['argv'] ?? [])->implode(' ');
		
			foreach ($artisanCommandsToSkip as $cmd) {
				if (str_contains($calledCommand, $cmd)) {
					return;
				}
			}
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
