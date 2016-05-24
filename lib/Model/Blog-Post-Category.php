<?php

namespace xepan\blog;

class Model_Blog-Post-Category extends xepan\base\Model_Table{
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xepan\blog\Associaton_Post-Category');
	}
}