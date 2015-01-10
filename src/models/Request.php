<?php namespace Drapor\Services\Models;
	/**
	 * Created by PhpStorm.
	 * User: michaelkantor
	 * Date: 1/9/15
	 * Time: 9:12 PM
	 */

	class Request extends \Model {
		protected $table ='service_requests';

		protected $guarded = [];
		public $timestamps = true;

	}