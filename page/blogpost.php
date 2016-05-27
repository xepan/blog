<?php
 
namespace xepan\blog;

class page_blogpost extends \xepan\base\Page {
	public $title='Blog Posts';

	function init(){
		parent::init();

		$blog_model = $this->add('xepan\blog\Model_BlogPost');
		$blog_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/post']);

		$crud->setModel($blog_model)->setOrder('created_at','desc');
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(50);

		$crud->grid->addColumn('category');
		$crud->grid->addMethod('format_postcategory',function($grid,$field){				
				$data = $grid->add('xepan\blog\Model_Association_PostCategory')->addCondition('blog_post_id',$grid->model->id);
				$l = $grid->add('Lister',null,'category',['view/post/post','category_lister']);
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHtml();
		});

		$crud->grid->addFormatter('category','postcategory');
	}
}

