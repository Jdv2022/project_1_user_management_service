<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserDetailUserRole;

class SetupEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-environment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {

    }

	private function populateInitUser() {
		$this->info("Populating initial user...");
		if(UserDetail::exists()) {
			UserDetail::factory(1)->create();
			$this->info("Initial UserDetail populated!");
		}
		else {
			$this->info("Initial UserDetail already exist!");
		}
		if(UserRole::exists()) {
			UserRole::factory(1)->create();
			$this->info("Initial UserRole populated!");
		}
		else {
			$this->info("Initial UserRole already exist!");
		}
		if(UserDetailUserRole::exists()) {
			UserDetailUserRole::factory(1)->create();
			$this->info("Initial UserDetailUserRole populated!");
		}
		else {
			$this->info("Initial UserDetailUserRole already exist!");
		}
	}
}
