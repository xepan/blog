<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
	'show_tag'=>true,
	'show_image'=>true,
	'show_comment_list'=>true,
	'allow_anonymous_comment'=>false,
	'login_page'=>'login',
	'comment_form_position'=>'above'
	];
	public $post;

	function init(){
		parent::init();

		$post_id = $this->api->stickyGET('post_id');

		$this->post = $this->add('xepan\blog\Model_BlogPost')->tryLoad($post_id?:-1);
		
		if(!$this->post->loaded()){
			$this->template->tryDel('tag_wrapper');
			return;
		}

		$this->setModel($this->post);
		$this->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$this->post]);
		
		if($this->options['show_comment_list']){
			$comment_m= $this->add('xepan\blog\Model_Comment')
						 ->addCondition('blog_post_id',$this->post->id)
						 ->addCondition('status','Approved');
			$comment_view = $this->add('xepan\blog\View_Comment',['options'=>$this->options],'comments');
			$comment_view->setModel($comment_m);
		}

		if($this->app->isEditing){
			$this->template->tryDel('tag_wrapper');

			$this->post->getElement('title')->display(['form'=>'hidden']);
			$this->post->getElement('description')->display(['form'=>'hidden']);
			$this->post->getElement('show_comments')->display(['form'=>'xepan\base\DropDownNormal']);
			$this->post->getElement('anonymous_comment_config')->display(['form'=>'xepan\base\DropDownNormal']);
			$this->post->getElement('registered_comment_config')->display(['form'=>'xepan\base\DropDownNormal']);
			
			$form = $this->add('Form',null,'editing_mode');
			$form->setModel($this->post,['title','description','tag','meta_title','meta_description','show_comments','anonymous_comment_config','registered_comment_config']);
			$title_field = $form->getElement('title');
			$description_field = $form->getElement('description');
			
			$form->addSubmit('SAVE POST')->addClass('btn btn-danger btn-block');

			$form->js('beforesubmit',[
				$title_field->js()->val($form->js()->_selector('#xepan-blog-title')->html()),
				$description_field->js()->val($form->js()->_selector('#xepan-blog-description')->html())
				]);

			if($form->isSubmitted()){
				$form->save();
				$form->js()->univ()->successMessage('Blog Post Saved')->execute();
			}

		}else{
			$this->template->tryDel('editing_mode');
		}

		if(!$this->model['image_id']){
			$this->template->tryDel('image_wrapper');
		}
	}

	function setModel($model){
		$this->template->trySetHtml('comment_count', $model['comment_count']);
		$this->template->trySetHtml('post_description', $model['description']);
		
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

		$l->current_row_html['tag'] = $l->model['tag'];
	}

	function addToolCondition_row_show_comment_list($value, $l){
		
		if(!$value){
			$l->current_row_html['comment_list_wrapper'] = "";
			return;
		}
	}
}