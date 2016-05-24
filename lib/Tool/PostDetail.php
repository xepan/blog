<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [ ];
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
		$this->template->trySetHtml('title', $model['title']);
		$this->template->trySetHtml('description', $model['description']);
		$this->template->trySetHtml('comment_count', $model['comment_count']);

		$comnt = $this->add('xepan\blog\Model_Comment');

		$v = $this->add('View',null,'leave_reply');
		$v->add('View')->setHTML($model['leave_reply']);
		$sub_form = $v->add('Form');
		$sub_form->addField('Email');
		$sub_form->addField('Message');
		$sub_form->addField('Email');
		$sub_form->addField('Url');
		
		$sub_form->addSubmit('Submit')->addClass('btn btn-primary btn-lg');
		
		if($sub_form->isSubmitted()){

		}

		parent::setModel($model);
	}

	function defaultTemplate(){
		return ['view/tool/post/detail'];
	}
}