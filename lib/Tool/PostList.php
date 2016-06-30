<?php

namespace xepan\blog;

class Tool_PostList extends \xepan\cms\View_Tool{
	public $options = [
					'show_description'=>true,
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>4,
					'description_page_url'=>'blog-item',
					'show_image'=>true,
					'set_by_order'=>'order_by_created_at',
					'add_socialshare'=>true,
					'include_socialshare'=>'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp',
					'socialshare_theme'=>"flat", //classic,minima,plain
					'show_microdata'=>true
				];

	function init(){
		parent::init();
		if($this->options['show_microdata']){
			$this->company_m = $this->add('xepan\base\Model_ConfigJsonModel',
						[
							'fields'=>[
										'company_name'=>"Line",
									'company_owner'=>"Line",
									'mobile_no'=>"Line",
									'company_email'=>"Line",
									'company_address'=>"Line",
									'company_pin_code'=>"Line",
									'company_description'=>"text",
									'company_description'=>"text",
									'company_logo_absolute_url'=>"Line",
									'company_twitter_url'=>"Line",
									'company_facebook_url'=>"Line",
									'company_google_url'=>"Line",
									'company_linkedin_url'=>"Line",
										],
							'config_key'=>'COMPANY_AND_OWNER_INFORMATION',
							'application'=>'communication'
						]);
			$this->company_m->tryLoadAny();
		}

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
		
		if($this->options['set_by_order']=='order_by_id'){
				$post->setOrder('order','asc');
			}elseif($this->options['set_by_order']=='order_by_recent_post'){
				$post->setOrder('created_at','Desc');
			}elseif($this->options['set_by_order']=='order_by_created_at'){
				$post->setOrder('created_at','asc');
		}
		
		
	}

	function recursiveRender(){
		parent::recursiveRender();
		
		if($this->options['add_socialshare']){
			$this->js(true)->_load('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials');
			$this->js(true)->_css('socialshare/jssocials-theme-'.$this->options['socialshare_theme']);
		}
	}

	function addToolCondition_row_show_description($value, $l){
		
		if(!$value){
			$l->current_row_html['description_wrapper'] = "";
			return;
		}

		$l->current_row_html['description'] =$l->model['description'];
	}
	function addToolCondition_row_show_microdata($value, $l){
		$v=$this->add('CompleteLister',null,null,['view/schema-micro-data','blog_post_block']);
		$v->setModel(clone $l->model);
		
		$v->addHook('formatRow',function($m){
			$m->current_row_html['blog_image']=$this->app->pm->base_url.$m->model['image'];
			$m->current_row_html['url']=$this->app->pm->base_url.$this->app->url(null,['post_id'=>$m->model->id]);
			$m->current_row_html['blog_description']=strip_tags($m->model['description']);
			$m->current_row_html['logo_url']=$this->company_m['company_logo_absolute_url'];
		});
		$l->current_row_html['micro_data']=$v->getHtml();

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

	function addToolCondition_row_add_socialshare($value,$l){
		// $l->current_row_html['socialshare'] = $l->add('View',null,'socialshare')->set("URL")->getHtml();
		$social_shares = explode(",", $this->options['include_socialshare']?:'email,twitter,facebook,googleplus,linkedin,pinterest,stumbleupon,whatsapp');
		$social_shares = array_values($social_shares);
		$this->js(true)->_selector('#postshare'.$l->model->id)
						->jsSocials(
							[
								'shares'=>$social_shares
							]);
						// 'url'=>
						// 'text'=>
						// 'showLabel'=>,
						// 'showCount'=>
						// 'shareIn'=>
	}

}