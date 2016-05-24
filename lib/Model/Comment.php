<?php

namespace xepan\blog;

class Model_Comment extends \xepan\base\Model_Table{
	public $table='comment';
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Customer');
		$this->addField('comment')->type('text');
	}
}