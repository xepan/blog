<?php

namespace xepan\blog;

class Model_BlogPostCategory extends \xepan\base\Model_Table{
	public $table = 'blog_post_category';
	public $acl = false; 
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('type');
		$this->addField('status');
		$this->addCondition('type','PostCategory');
		$this->addCondition('status','-');

		$this->hasMany('xepan\blog\Associaton_PostCategory');
		
	}
}