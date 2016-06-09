<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
						'show_tag'=>true,
						'show_image'=>true,
						'show_comment_list'=>true,
						'allow_anonymous_comment'=>true

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
		$this->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$this->post]);

		$sub_form = $this->add('Form',null,'leave_comment');
		$sub_form->addField('text','Comment')->validate('required');
		$sub_form->addSubmit('Submit')->addClass('btn btn-primary btn-lg');

		if($sub_form->isSubmitted()){
			
      		if($this->options['allow_anonymous_comment'] == false){
                $contact = $this->add('xepan\base\Model_Contact');
      			if($contact->loadLoggedIn()){

      				$comment_model = $this->add('xepan\blog\Model_Comment');
					$comment_model['comment'] = $sub_form['Comment'];
					$comment_model['blog_post_id'] = $post_id;	
					$comment_model['created_by_id'] = $contact->id;
					$comment_model->save();

		      		$sub_form->js(null,$sub_form->js()->reload())->univ()->successMessage('You have successfully commented on this post')->execute();
      			}else{
      				$this->api->memorize('next_url',array('page'=>$_GET['page'],'post_id'=>$_GET['post_id']));
                	$this->app->redirect('login');
      			}
          	}
			
			$comment_model = $this->add('xepan\blog\Model_Comment');
			$comment_model['comment'] = $sub_form['Comment'];
			$comment_model['blog_post_id'] = $post_id;	
			
			$contact = $this->add('xepan\base\Model_Contact');
      		if($contact->loadLoggedIn()){
				$comment_model['created_by_id'] = $contact->id;
			}
			
			$comment_model->save();
      		$sub_form->js(null,$sub_form->js()->reload())->univ()->successMessage('You have successfully commented on this post')->execute();
      	}
	}

	function setModel($model){
		$this->template->trySetHtml('comment_count', $model['comment_count']);
		$this->template->trySetHtml('post_description', $model['description']);
    
		//comments
		$comnt_mdl = $this->add('xepan\blog\Model_Comment');
		$cl = $this->add('CompleteLister',null,'comment_list',['view/tool/post/extra-detail/comment-list']);
		$cl->setModel($comnt_mdl)->addCondition('blog_post_id',$this->post->id)->addCondition('status','Approved');
		
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

	function addToolCondition_row_show_image($value, $l){
		if(!$value){
			$l->current_row_html['image_wrapper'] = "";
			return;	
		}
	}
}