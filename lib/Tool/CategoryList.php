<?php

namespace xepan\blog;

class Tool_CategoryList extends \xepan\cms\View_Tool{
	public $options = [
						'category_count'=> 5,
						'show_post_count'=>true,
						'redirect_page_url'=>'blog'
				];

	function init(){
		parent::init();

		$category = $this->add('xepan\blog\Model_BlogPostCategory');
		$category->setLimit($this->options['category_count']);
		$category->addCondition('status','Active');

		$cl = $this->add('CompleteLister',null,null,['view/tool/post/category']);
		if(!$category->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');

		$cl->setModel($category);

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
}