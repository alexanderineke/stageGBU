<?php

function viewsCache($cache_key){

	$expiration = Yii::app()->params['cache_expiration'];
	$cache_value = Yii::app()->cache->get($cache_key);

	if($cache_value){
		$view_value = $cache_value['expiration'];

		if( ( time() - $cache_value['time'] ) > $cache_value['expiration'] ){		
			$cache_value = array('time'=>time(), 'expiration'=>$expiration, 'views'=>0);	
		}else{
			$cache_value['views']++; 
		}
		Yii::app()->cache->delete($cache_key);
		Yii::app()->cache->set($cache_key, $cache_value);
	}else{
		$view_value = 0;
		Yii::app()->cache->set($cache_key, array('time'=>time(), 'expiration'=>$expiration, 'views'=>0));
	}


	return $view_value;
}