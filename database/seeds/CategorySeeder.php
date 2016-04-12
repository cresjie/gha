<?php

use Illuminate\Database\Seeder;
use App\Models\Classification\TermTaxonomy;
use App\Models\Classification\Terms;

class CategorySeeder extends Seeder
{

	public function run()
	{
		$taxonomy = TermTaxonomy::create([
			'title' => 'Event Category',
			'taxonomy_code' => 'event_category'
		]);

		Terms::insert([
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Concerts',
				'term_code' => 'concerts'
			],
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Arts|Exhibitions',
				'term_code' => 'arts_exhibitions'
			],
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Festivals|Parties',
				'term_code' => 'festivals_parties'
			],
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Workshops',
				'term_code' => 'workshops'
			],
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Diverse Events',
				'term_code' => 'diverse_events'
			],
			[
				'taxonomy_id' => $taxonomy->id,
				'name' => 'Online Events',
				'term_code' => 'online_events'
			],

		]);
	}
}