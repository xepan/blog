<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
						'show_description'=>true,
						'show_title'=>true,
						'show_image'=>true,
						'show_comment'=>true,
						'show_anonymous_comment'=>true

						];
	public $post;
	function init(){
		parent::init();

		$post_id = $this->api->stickyGET('post_id');

		$this->post = $this->add('xepan\blog\Model_BlogPost')->tryLoad($post_id?:-1);
		
		if(!$this->post->loaded()){
			$this->add('View')->set('Err while loading');
			return;
		}

		$this->setModel($this->post);

	}

	function setModel($model){
		$this->template->trySetHtml('comment_count', $model['comment_count']);

		parent::setModel($model);
	}

	function defaultTemplate(){
		return ['view/tool/post/detail'];
	}

	function addToolCondition_row_show_title($value, $l){
		
		if(!$value){
			$l->current_row_html['title_wrapper'] = "";
			return;
		}

		$l->current_row_html['title'] =$l->model['title'];
	}

	function addToolCondition_row_show_description($value, $l){
		
		if(!$value){
			$l->current_row_html['description_wrapper'] = "";
			return;
		}

		$l->current_row_html['description'] =$l->model['description'];
	}

	function addToolCondition_row_show_tag($value, $l){
		
		if(!$value){
			$l->current_row_html['tag_wrapper'] = "";
			return;
		}

		$l->current_row_html['tag'] =$l->model['tag'];
	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value){
			$l->current_row_html['image_wrapper'] = "";
			return;	
		}
	}
}