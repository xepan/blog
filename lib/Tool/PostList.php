<?php

namespace xepan\blog;

class Tool_PostList extends \xepan\cms\View_Tool{
	public $options = [
					'show_description'=>true,
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>4,
					'description_page_url'=>'blog-item',
					'show_image'=>false,
				];

	function init(){
		parent::init();

		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->setOrder('created_at','desc');

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

		if(!$l->model['description'])
			$l->current_row['description'] = $l->model['desription'];
	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value){
			$l->current_row_html['image_wrapper'] = "";
			return;
		}
	}

	function addToolCondition_row_description_page_url($value, $l){			
		$l->current_row['url'] = $this->app->url($this->options['description_page_url'],['post_id'=>$l->model->id]);
	}

}