<?php

namespace xepan\blog;

class Model_BlogPostCategory extends xepan\base\Model_Table{
	public $table='blog_post_category';
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xepan\blog\Associaton_PostCategory');
	}
}