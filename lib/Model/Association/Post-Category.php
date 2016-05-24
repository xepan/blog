<?php

namespace xepan\blog;

class Model_Association_Post-Category extends xepan\base\Model_Table{
	function init(){
		parent::init();

		$this->hasOne('xepan\blog\Blog-Post-Category');
		$this->hasOne('xepan\blog\Blog-Post');
	}
}