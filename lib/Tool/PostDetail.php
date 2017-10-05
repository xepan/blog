<?php

namespace xepan\blog;

class Tool_PostDetail extends \xepan\cms\View_Tool{
	public $options = [
	'show_tag'=>true,
	'show_image'=>true,
	'show_comment_list'=>true,
	'allow_anonymous_comment'=>false,
	'login_page'=>'login',
	'comment_form_position'=>'above',
	'add_socialshare'=>false,
	'include_socialshare'=>'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp',
	'socialshare_theme'=>"flat" //classic,minima,plain
	];
	public $post;

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$post_category = $this->api->stickyGET('post_category');
		$post_name = $this->api->stickyGET('blog_post_code');
		$post_id = $this->api->stickyGET('post_id');
		
		$this->post = $this->add('xepan\blog\Model_BlogPost');
		$this->post->addCondition('status','Published');
		if($post_name){
			$this->post->addCondition('slug_url',$post_name);
			$this->post->tryLoadAny();
		}elseif ($post_id) {
			$this->post->load($post_id);
		}else{
			$this->post->load(-1);
		}

		if(!$this->post->loaded()){
			$this->template->tryDel('tag_wrapper');
			return;
		}

		$this->app->template->trySet('title',$this->post['meta_title']?:$this->post['title']);
		$this->app->template->trySet('meta_keywords',$this->post['tag'].' '.$this->post['meta_title']);
		$this->app->template->trySet('meta_description',$this->post['meta_description']);

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

		// add social share todo shift into tool condition function
		if($this->options['add_socialshare']){
			$this->js(true)->_load('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials-theme-'.$this->options['socialshare_theme']);
			
			$sharing_url = $this->app->pm->base_url.$this->app->url(null,['xepan_landing_content_id'=>$this->model->id]);
			$social_shares = explode(",", $this->options['include_socialshare']?:'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp');
			$social_shares = array_values($social_shares);
			$this->js(true)->_selector('#postshare'.$this->model->id)
							->jsSocials(
								[
									'shares'=>$social_shares,
									"url"=>$sharing_url
								]);
		}else
			$this->template->trySet('sharewrapper',"");
	}

	function setModel($model){
		$this->template->trySetHtml('comment_count', $model['comment_count']);
		$this->template->trySetHtml('post_description', $model['description']);
		
		parent::setModel($model);
	}

	function recursiveRender(){
		parent::recursiveRender();
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