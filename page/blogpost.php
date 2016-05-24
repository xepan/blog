<?php
 
namespace xepan\blog;

class page_blogpost extends \xepan\base\Page {
	public $title='Blog Posts';

	function init(){
		parent::init();

		// $blog_model = $this->add('xepan\blog\Model_BlogPost');
		// $blog_model = $this->add('xepan\blog\Model_BlogPostCategory');
		// $blog_model = $this->add('xepan\blog\Model_Comment');
		$blog_model = $this->add('xepan\blog\Model_Association_PostCategory');

		$crud = $this->add('CRUD'
						)->setModel($blog_model);

		// $crud->setModel($blog_model)->setOrder('created_at','desc');
		// $crud->grid->addPaginator(50);

	}
}