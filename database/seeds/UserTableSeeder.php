<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder{

	public function run(){
		User::create([
			'email' => 'cresjie@gmail.com',
			'password' => Hash::make('12345123'),
			'first_name' => 'cres',
			'last_name' => 'labasano',
			'user_slug' => 'cres',
			'verified' => true,
			'gender' => 'male'
		]);


		User::create([
			'email' => 'cj@gmail.com',
			'password' => Hash::make('12345123'),
			'first_name' => 'cres',
			'last_name' => 'labasano',
			'user_slug' => 'cj',
			'verified' => true,
			'gender' => 'male'
		]);
	}
}