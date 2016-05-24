<?php

namespace xepan\blog;

class Model_Association_PostCategory extends \xepan\base\Model_Table{
	public $table='blog_post_category_association';
	function init(){
		parent::init();

		$this->hasOne('xepan\blog\BlogPostCategory');
		$this->hasOne('xepan\blog\BlogPost');
	}
}