<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\blog;

class Model_BlogPost extends \xepan\base\Model_Table{
	public $table='blog_post';

	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee');
		$this->addField('title');
		$this->addField('description');
		$this->addField('tag')->type('text');
		$this->addField('meta_title');
		$this->addField('meta_description');
		$this->addField('created_at');
		$this->hasMany('xepan\blog\Associaton_PostCategory');
	}
	
}
