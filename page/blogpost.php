<?php
 
namespace xepan\blog;

class page_blogpost extends \xepan\base\Page {
	public $title='Blog Posts';

	function init(){
		parent::init();

		$blog_model = $this->add('xepan\blog\Model_Blog-Post');

		// $crud = $this->add('xepan\hr\CRUD',null,
		// 					null,
		// 					['view/blogpost/grid']
		// 				);

		// $crud->setModel($blog_model)->setOrder('created_at','desc');
		// $crud->grid->addPaginator(50);

	}
}