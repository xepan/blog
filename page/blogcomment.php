<?php
 
namespace xepan\blog;

class page_blogcomment extends \xepan\base\Page {
	public $title='Blog Comments';

	function init(){
		parent::init();

		$blog_comment_model = $this->add('xepan\blog\Model_Comment');
		$blog_comment_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/comments']
						);

		$crud->setModel($blog_comment_model);
		$crud->grid->addPaginator(50);


	}
}

