<?php

namespace xepan\blog;

class Model_Comment extends \xepan\base\Model_Table{
	public $table='blog_comment';
	public $status = ['Approved','Pending','Rejected'];
	public $actions = [
					'Pending'=>['view','edit','delete','approve','reject'],
					'Approved'=>['view','edit','delete','reject'],
					'Rejected'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Customer','created_by_id');
		$this->hasOne('xepan\blog\BlogPost','blog_post_id');
		
		$this->addField('comment')->type('text');
		$this->addField('status')->enum(['Approved','Pending','Rejected']);
		$this->addField('type');
		$this->addCondition('type','BlogComment');
	}
}