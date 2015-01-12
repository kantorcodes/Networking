<?php namespace Drapor\Networking\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
	/**
	 * Created by PhpStorm.
	 * User: michaelkantor
	 * Date: 1/9/15
	 * Time: 9:12 PM
	 */

	class Request extends Eloquent {
		protected $table ='service_requests';

		protected $guarded = [];
        protected $hidden  = [];
		public $timestamps = true;


        public function getBodyAttribute( $value)
        {
            return $this->toString($value);
        }


        public function getHeadersAttribute($value){
            return $this->toString($value);
        }

        /**
         * @param $value
         * @return string
         */
        private function toString($value)
        {
            $body = json_decode($value, true);
            $string = '';
            foreach ($body as $key => $value) {
                $string .= "$key : $value</br>";
            }
            return $string;
        }

    }