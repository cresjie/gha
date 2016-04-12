<?php

use Illuminate\Database\Seeder;
use App\Models\Usertype\Account;


class UsertypeAccountSeeder extends Seeder
{

	public function run()
	{
		Account::insert([
			[
				'name' => 'Standard',
				'code' => 'standard',
				'description' => 'Standard user account',
				'currency' => 'USD',
				'price' => 0
			],
			[
				'name' => 'Silver',
				'code' => 'silver',
				'description' => 'Silver user account',
				'currency' => 'USD',
				'price' => 3
			],
			[
				'name' => 'Gold',
				'code' => 'gold',
				'description' => 'Gold user account',
				'currency' => 'USD',
				'price' => 5
			],
			[
				'name' => 'Platinum',
				'code' => 'platinum',
				'description' => 'Platinum user account',
				'currency' => 'USD',
				'price' => 7
			],
			
		]);
	}
}