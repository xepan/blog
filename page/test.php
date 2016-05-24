<?php
namespace xepan\blog;

class page_test extends \xepan\base\Page{
	function init(){
		parent::init();

		$this->add('CRUD')->setModel('xepan\blog\Association_PostCategory');
	}
}