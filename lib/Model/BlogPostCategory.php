<?php

namespace xepan\blog;

class Model_BlogPostCategory extends \xepan\base\Model_Table{
	public $table = 'blog_post_category';
	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'InActive'=>['view','edit','delete','activate']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id);
		$this->addField('name');
		$this->addField('order');
		$this->addField('group');
		$this->addField('type');
		$this->addField('status')->enum(['Active','InActive']);
		$this->addCondition('type','PostCategory');

		$this->getElement('status')->defaultValue('Active');

		$this->hasMany('xepan\blog\Associaton_PostCategory','blog_post_category_id');
		
	}

	//activate BlogPostCategory
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Blog Post Category : '".$this['name']."' now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate BlogPostCategory
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Blog Post Category '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}
}