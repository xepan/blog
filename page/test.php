<?php
namespace xepan\blog;

class page_test extends \xepan\base\Page{
	function init(){
		parent::init();

		
		$blog_model = $this->add('xepan\blog\Model_BlogPost');
		// $blog_model = $this->add('xepan\blog\Model_BlogPostCategory');
		// $blog_model = $this->add('xepan\blog\Model_Comment');
		// $blog_model = $this->add('xepan\blog\Model_Association_PostCategory');

		$crud = $this->add('CRUD'
						)->setModel($blog_model);
	}
}