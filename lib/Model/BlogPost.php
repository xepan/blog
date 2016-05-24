<?php

/**
* description: ATK Model
* 
* @author : Deepak Kanojia
* @email : dkanojia93@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\blog;

class Model_BlogPost extends \xepan\base\Model_Table{
	public $table='blog_post';
	public $status = ['Published','UnPublished'];
	public $actions = [
					'Published'=>['view','edit','delete','unPublish'],
					'UnPublished'=>['view','edit','delete','publish']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id);

		$this->addField('title');
		$this->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$this->addField('tag')->type('text');
		$this->addField('meta_title');
		$this->addField('status')->enum(['Published','UnPublished']);
		$this->addField('meta_description');
		$this->addField('created_at')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('type');
		$this->add('xepan\filestore\Field_Image','image_id');

		$this->hasMany('xepan\blog\Associaton_PostCategory','blog_post_id');

		$this->addCondition('type','BlogPost');
	}

	//publish Blog Post
	function publish(){
		$this['status']='Published';
		$this->app->employee
            ->addActivity("Blog Post '".$this['title']."' can be view on web", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('unPublish','Published',$this);
		$this->save();
	}

	//unPublish Blog Post
	function unPublish(){
		$this['status']='UnPublished';
		$this->app->employee
            ->addActivity("Blog Post '". $this['title'] ."' not available for show on web", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('publish','UnPublished',$this);
		return $this->save();
	}

	
}
