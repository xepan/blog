<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
				
				
					 
				];
	public $post;
	function init(){
		parent::init();

		$post_id = $this->api->stickyGET('blog_post_id');

		$this->post = $this->add('xepan\blog\Model_BlogPost')->tryLoad($post_id?:-1);
		if(!$this->post->loaded()){
			$this->add('View')->set('Post must be load by Id');
			return;
		}	
		$this->setModel($this->post);

	}

	function setModel($model){
		//tryset html for description 
		$this->template->trySetHtml('post_description', $model['description']);

		parent::setModel($model);
	}

	function defaultTemplate(){
		return ['view/tool/post/detail'];
	}
}