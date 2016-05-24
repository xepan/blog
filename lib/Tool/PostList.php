<?php

namespace xepan\blog;

class Tool_PostList extends \xepan\cms\View_Tool{
	public $options = [
					'show_title'=>true,
					'show_description'=>true,
					'show_created_at'=>true,
					'show_tag'=>true,
					'show_tag'=>true,
					'show_paginator'=>true,
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>4,
					'description_page_url'=>'',
				];

	function init(){
		parent::init();

		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->setOrder('created_at','desc');

		// $cl = $this->add('CompleteLister',null,null,['view/tool/item/'.$this->options['layout']]);
		// if(!$item->count()->getOne())
		// 	$cl->template->set('not_found_message','No Record Found');
		// else
		// 	$cl->template->del('not_found');


		// $cl->setModel($item);
		
		// if($this->options['show_paginator']=="true"){
		// 	$paginator = $cl->add('Paginator',['ipp'=>$this->options['paginator_set_rows_per_page']]);
		// 	$paginator->setRowsPerPage($this->options['paginator_set_rows_per_page']);
		// }
	}

	// function addToolCondition_row_show_image($value, $l){
	// 	if(!$value){
	// 		$l->current_row_html['image_wrapper'] = "";
	// 		return;
	// 	}

	// 	if(!$l->model['first_image'])
	// 		$l->current_row['first_image'] = "vendor/xepan/commerce/templates/view/tool/item/images/xepan_item_list_no_image.jpg";
		
	// }
}