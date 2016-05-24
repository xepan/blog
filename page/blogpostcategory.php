<?php
 
namespace xepan\blog;

class page_blogpostcategory extends \xepan\base\Page {
	public $title='Blog Post Categories';

	function init(){
		parent::init();

		$blog_category_model = $this->add('xepan\blog\Model_BlogPostCategory');
		$blog_category_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/category']
						);

		$crud->setModel($blog_category_model);
		$crud->grid->addPaginator(50);
		


	}
}

