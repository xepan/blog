<?php

namespace xepan\blog;

class Initiator extends \Controller_Addon {
	
	public $addon_name = 'xepan_blog';

	function setup_admin(){

		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/blog/');

		}
		
		return $this;

	}

	function setup_frontend(){
		$this->routePages('xepan_blog');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('./vendor/xepan/blog/');
		
		return $this;
	}

	
	function resetDB(){
		// Clear DB
		if(!isset($this->app->old_epan)) $this->app->old_epan = $this->app->epan;
        if(!isset($this->app->new_epan)) $this->app->new_epan = $this->app->epan;
        
		$this->app->epan=$this->app->old_epan;
        $truncate_models = ['Blog-Post-Category','Blog-Post'];
        foreach ($truncate_models as $t) {
            $m=$this->add('xepan\blog\Model_'.$t);
            foreach ($m as $mt) {
                $mt->delete();
            }
        }
		$this->app->epan=$this->app->new_epan;
	}

}
