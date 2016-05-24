<?php
namespace xepan\blog;

class page_test extends \xepan\base\Page{
	public $title = 'Tool Test';
	function init(){
		parent::init();

		$this->add('xepan\blog\Tool_PostList');
	}
}