<?php namespace Drapor\Services;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class CreateServiceRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_requests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('body');
			$table->text('headers');
			$table->text('cookies');
			$table->string('url');
			$table->integer('status_code');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('service_requests');
	}

}
