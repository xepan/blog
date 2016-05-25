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
		
		$this->addField('comment_date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('comment')->type('text');
		$this->addField('status')->enum(['Approved','Pending','Rejected']);
		$this->addField('type');
		$this->addCondition('type','BlogComment');

		$this->getElement('status')->defaultValue('Pending');

		$this->addExpression('commented_by')->set(function($m,$q){
			return $q->expr('(IFNULL([0],"Anonymous Person"))',[$m->refSQL('created_by_id')->fieldQuery('name')]);			
		});
	}

	//Approve Post Comment
	function approve(){
		$this['status']='Approved';
		$this->app->employee
            ->addActivity("This '".$this['comment']."' comment approved to show on web", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('reject','Approved',$this);
		$this->save();
	}

	//Reject Comment
	function reject(){
		$this['status']='Rejected';
		$this->app->employee
            ->addActivity("This '".$this['comment']."' comment rejected", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan(' ','Rejected',$this);
		$this->save();
	}
}