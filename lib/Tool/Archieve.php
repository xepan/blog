<?php

namespace xepan\blog;

class Tool_Archieve extends \xepan\cms\View_Tool{
	public $options = [

					'show_month'=>true
				];

	function init(){
		parent::init();


		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->_dsql()->group($post->_dsql()->expr("[0]",[$post->getElement('year')]));		
		
		$cl = $this->add('CompleteLister',null,null,['view/tool/post/archieve']);
		if(!$post->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');

		$cl->setModel($post);

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$post]);
	}

	function addToolCondition_row_show_month($value, $l){
		if(!$value){
			$l->current_row_html["month_lister"] = "";
			return;
		}

		$post = $l->add('xepan\blog\Model_BlogPost');
		$post->addCondition('year',$l->model['year']);
		$post->_dsql()->group($post->_dsql()->expr("[0]",[$post->getElement('month')]));


		$month_l = $l->add('CompleteLister',null,"month_lister",['view/tool/post/archieve_month']);		
		$month_l->setModel($post);

		$month_l->addHook('formatRow',function($ml){
			$ml->current_row_html["month"] = date('M \'y',strtotime($ml->model['created_at']));
		
		});

		$l->current_row_html['month_lister'] = $month_l->getHtml();
		

	}
}
