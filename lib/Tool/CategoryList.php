<?php

namespace xepan\blog;

class Tool_CategoryList extends \xepan\cms\View_Tool{
	public $options = [
						'category_count'=> 5,
						'show_post_count'=>true,
						'redirect_page_url'=>'blog',
						'show_post'=>true,
						'group'=>'',
						'order'=>false,
				];

	function init(){
		parent::init();

		$category = $this->add('xepan\blog\Model_BlogPostCategory');
		$category->setLimit($this->options['category_count']);
		$category->addCondition('status','Active');
		
		if($this->options['order'])
			$category->setOrder('order','asc');
		
		if($this->options['group'])	
			$category->addCondition('group',$this->options['group']);

		$cl = $this->add('CompleteLister',null,null,['view/tool/post/category']);
		
		if($this->options['show_post']){
			$assos_j = $category->leftJoin('blog_post_category_association.blog_post_category_id');
			$blog_j = $assos_j->leftJoin('blog_post','blog_post_id');
			$blog_j->addField('title');
			$blog_j->addField('post_id','id');
			$category->setOrder(['order asc','post_id asc']);
		}

		if(!$category->count()->getOne()){
			$cl->template->set('not_found_message','No Record Found');
		}
		else
			$cl->template->del('not_found');

		$categories=[];
		$posts=[];
		foreach ($cat_rows = $category->getRows() as $cat) {
			if(!isset($categories[$cat['id']])) $categories[$cat['id']]=$cat['name'];
			$posts[$cat['id']][] =  ['title'=>$cat['title'],'post_id'=>$cat['post_id']];
		}

		if($this->options['show_post']){
			$cl->addHook('formatRow',function($cl)use($categories,$posts){
				$pl = $cl->add('CompleteLister',null,'cat_post',['view/tool/post/category','cat_post']);
				$pl->setSource($posts[$cl->model->id]);
				$cl->current_row_html['cat_post'] = $pl->getHTMl();
			});
		}

		$cl->setSource($categories);

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$category]);
	}

	function addToolCondition_row_show_post_count($value, $l){
		
		if(!$value){
			$l->current_row_html['post_count_wrapper'] = "";
			return;
		}

		$count = $l->add('xepan\blog\Model_Association_PostCategory')->addCondition('blog_post_category_id',$l->model->id)->count();
		$l->current_row_html['post_count'] =$count;
	}

	function addToolCondition_row_redirect_page_url($value, $l){					
		$l->current_row['url'] = $this->app->url($this->options['redirect_page_url'],['category_id'=>$l->model->id]);
	}

	function addToolCondition_row_show_post($value, $l){
		
		if(!$value){
			$l->current_row_html['post_wrapper'] = "";
			return;
		}
	}
}