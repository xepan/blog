<?php

namespace xepan\blog;

class Tool_PostList extends \xepan\cms\View_Tool{
	public $options = [
					'show_description'=>true,
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>4,
					'description_page_url'=>'blog-item',
					'show_image'=>true,
				];

	function init(){
		parent::init();


		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->setOrder('created_at','desc');

		if($category_id = $this->app->stickyGET('category_id')){
			$assoc_j = $post->join('blog_post_category_association.blog_post_id');
			$assoc_j->addField('blog_post_category_id');
			$post->addCondition('blog_post_category_id',$category_id);
			$post->_dsql()->group('blog_post_id');
		}

		if($search_string = $this->app->stickyGET('search')){
			$post->addExpression('Relevance')->set('MATCH(title, description, tag, meta_title, meta_description) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
			$post->addCondition('Relevance','>',0);
	 		$post->setOrder('Relevance','Desc');
		}

		if($month = $this->app->stickyGET('month')){
			$post->addCondition('month',$month);
		}

		$cl = $this->add('CompleteLister',null,null,['view/tool/post/list']);
		if(!$post->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');

		$cl->setModel($post);
		
		if($this->options['show_paginator']=="true"){
			$paginator = $cl->add('Paginator',['ipp'=>$this->options['paginator_set_rows_per_page']]);
			$paginator->setRowsPerPage($this->options['paginator_set_rows_per_page']);
		}
		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$post]);
	}

	function addToolCondition_row_show_description($value, $l){
		
		if(!$value){
			$l->current_row_html['description_wrapper'] = "";
			return;
		}

		$l->current_row_html['description'] =$l->model['description'];
	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value || !$l->model['image_id']){
			$l->current_row_html['image_wrapper'] = "";
			return;	
		}
	}

	function addToolCondition_row_description_page_url($value, $l){			
		$l->current_row['url'] = $this->app->url($this->options['description_page_url'],['post_id'=>$l->model->id]);
	}

}