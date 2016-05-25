<?php
 
namespace xepan\blog;

class page_blogpost extends \xepan\base\Page {
	public $title='Blog Posts';

	function init(){
		parent::init();

		$association = $this->add('xepan\blog\Model_Association_PostCategory');
		$crud = $this->add('xepan\hr\CRUD')->setModel($association);

		$blog_model = $this->add('xepan\blog\Model_BlogPost');
		$blog_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/grid']);

		$crud->setModel($blog_model)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);


	}
}

