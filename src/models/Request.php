<?php namespace Drapor\Networking\Models;
use Illuminate\Database\Eloquent\Model;
	/**
	 * Created by PhpStorm.
	 * User: michaelkantor
	 * Date: 1/9/15
	 * Time: 9:12 PM
	 */

	class Request extends Model {
		protected $table ='service_requests';

		protected $guarded = [];
        protected $hidden  = [];
		public $timestamps = true;


    }