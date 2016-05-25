<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
						'show_tag'=>true,
						'show_image'=>true,
						'show_comment_list'=>true,
						'show_anonymous_comment_list'=>true

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

		$sub_form = $this->add('Form',null,'leave_comment');
		$sub_form->addField('text','Post Comment');
		$sub_form->addSubmit('Submit')->addClass('btn btn-primary btn-lg');

		if($sub_form->isSubmitted()){

		}

	}

	function setModel($model){
		$this->template->trySetHtml('comment_count', $model['comment_count']);
		$this->template->trySetHtml('post_description', $model['description']);

		//comments
		$comnt_mdl = $this->add('xepan\blog\Model_Comment');
		$cl = $this->add('CompleteLister',null,'comment_list',['view/tool/post/extra-detail/comment-list']);
		$cl->setModel($comnt_mdl)->addCondition('blog_post_id',$this->post->id)->addCondition('status','Approved');

		//anonymous comments
		$an_cl = $this->add('CompleteLister',null,'anonymous_comment_list',['view/tool/post/extra-detail/anonymous-comment-list']);
		$an_cl->setModel('xepan\blog\Comment')->addCondition('blog_post_id',$this->post->id);


		parent::setModel($model);
	}
	function defaultTemplate(){
		return ['view/tool/post/detail'];
	}

	
	function addToolCondition_row_show_tag($value, $l){
		
		if(!$value){
			$l->current_row_html['tag_wrapper'] = "";
			return;
		}

		$l->current_row_html['tag'] =$l->model['tag'];
	}

	function addToolCondition_row_show_comment_list($value, $l){
		
		if(!$value){
			$l->current_row_html['comment_list_wrapper'] = "";
			return;
		}
	}

	function addToolCondition_row_show_anonymous_comment_list($value, $l){
		
		if(!$value){
			$l->current_row_html['anonymous_comment_list_wrapper'] = "";
			return;
		}
	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value){
			$l->current_row_html['image_wrapper'] = "";
			return;	
		}
	}
}