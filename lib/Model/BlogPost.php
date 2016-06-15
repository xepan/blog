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
					'Published'=>['view','edit','delete','unpublish','category' ],
					'UnPublished'=>['view','edit','delete','publish','category']
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
		$this->addField('anonymous_comment_config')->enum(['none','moderate','permit'])->defaultValue('permit');
		$this->addField('registered_comment_config')->enum(['moderate','permit'])->defaultValue('permit');
		$this->addField('show_comments')->enum(['show','hide'])->defaultValue('show');
		$this->add('xepan\filestore\Field_Image','image_id');

		$this->hasMany('xepan\blog\Association_PostCategory','blog_post_id',null,'PostCategoryAssociation');
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

		$this->addExpression('month')->set(function($m,$q){
			return $q->expr("EXTRACT(YEAR_MONTH from [0])",[$m->getElement('created_at')]);
		});

		$this->addExpression('created_at_date')->set('DATE(created_at)');
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

	function page_category($page){

		$form = $page->add('Form');
		$cat_ass_field = $form->addField('hidden','ass_cat')->set(json_encode($this->getAssociatedCategories()));
		$form->addButton('Update');

		$category_assoc_grid = $page->add('xepan/base/Grid',null,null,['view\post\association']);
		$model_assoc_category = $page->add('xepan/blog/Model_BlogPostCategory');

		$category_assoc_grid->setModel($model_assoc_category);
		$category_assoc_grid->addSelectable($cat_ass_field);


		if($form->isSubmitted()){
			$this->removeAssociateCategory();

			$selected_categories = array();
			$selected_categories = json_decode($form['ass_cat'],true);
			
			foreach ($selected_categories as $cat) {
				$this->associateCategory($cat);
			}

		 	return $page->js()->univ()->successMessage('Category Association Updated');
		}
	}

	function getAssociatedCategories(){
		$associated_categories = $this->ref('PostCategoryAssociation')
								->_dsql()->del('fields')->field('blog_post_category_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_categories)),false);
	}

	function removeAssociateCategory(){

		$this->add('xepan\blog\Model_Association_PostCategory')
			 ->addCondition('blog_post_id',$this->id)
			 ->deleteAll();
	}	

	function associateCategory($category){
		return $this->add('xepan\blog\Model_Association_PostCategory')
						->addCondition('blog_post_id',$this->id)
		     			->addCondition('blog_post_category_id',$category)
			 			->tryLoadAny()	
			 			->save();
	}
}
