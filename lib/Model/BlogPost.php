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
					'Published'=>['view','edit','delete','unpublish','comments','category' ],
					'UnPublished'=>['view','edit','delete','publish','comments','category']
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
		$this->hasMany('xepan\blog\Comment','blog_post_id',null,'Comments');

		$this->addCondition('type','BlogPost');
		$this->getElement('status')->defaultValue('Published');

		$this->addExpression('comment_count')->set(function($m,$q){
			return $m->refSQL('Comments')
					 ->addCondition('status','Approved')
					 ->count();
		});

		$this->addExpression('year')->set(function($m,$q){
			return $q->expr("( EXTRACT(year from [0] ) )",[$m->getElement('created_at')]);
		});

		$this->addExpression('year_post_count')->set(function($m,$q){
			return $q->expr("count([0])",[$m->getElement('id')]);
		});


		$this->addExpression('month')->set(function($m,$q){
			return $q->expr("EXTRACT(YEAR_MONTH from [0])",[$m->getElement('created_at')]);
		});

		$this->addExpression('month_post_count')->set(function($m,$q){
			return $q->expr("( COUNT([0]) )",[$m->getElement('id')]);
		});
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
	function unpublish(){
		$this['status']='UnPublished';
		$this->app->employee
            ->addActivity("Blog Post '". $this['title'] ."' not available for show on web", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('publish','UnPublished',$this);
		return $this->save();
	}

	function page_comments($page){		
		$comment = $this->add('xepan\blog\Model_Comment');
		$comment->addCondition('blog_post_id',$this->id);
		$crud = $page->add('xepan\hr\CRUD',null,null,['view\post\comment']);
		$crud->setModel($comment)->setOrder('comment_date','desc');
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(50);
	}

	function page_category($page){		
	}	
}
