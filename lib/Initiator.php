<?php

namespace xepan\blog;

class Initiator extends \Controller_Addon {
	
	public $addon_name = 'xepan_blog';

	function setup_admin(){

		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/blog/');

		if(isset($this->app->cms_menu)){
			$this->app->cms_menu->addItem(['Blog Category','icon'=>' fa fa-sitemap'],'xepan_blog_blogpostcategory');//->setAttr(['title'=>'Blogs']);
			$this->app->cms_menu->addItem(['Blog Post','icon'=>' fa fa-file-text-o'],'xepan_blog_blogpost');//->setAttr(['title'=>'Blogs']);
		}
		return $this;

	}

	function setup_frontend(){
		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
		->setBaseURL('./vendor/xepan/blog/');

		 $this->app->exportFrontEndTool('xepan\blog\Tool_PostList','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_PostDetail','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_CategoryList','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_Search','Blog');
		 $this->app->exportFrontEndTool('xepan\blog\Tool_Archieve','Blog');
		
		return $this;
	}

	
	function resetDB(){
		// Clear DB
		// if(!isset($this->app->old_epan)) $this->app->old_epan = $this->app->epan;
  //       if(!isset($this->app->new_epan)) $this->app->new_epan = $this->app->epan;
        
		// $this->app->epan=$this->app->old_epan;
  //       $truncate_models = ['BlogPostCategory','BlogPost'];
  //       foreach ($truncate_models as $t) {
  //           $m=$this->add('xepan\blog\Model_'.$t);
  //           foreach ($m as $mt) {
  //               $mt->delete();
  //           }
  //       }
		// $this->app->epan=$this->app->new_epan;
	}

}
