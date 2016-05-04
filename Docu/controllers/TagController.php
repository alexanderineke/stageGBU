<?php

class TagController extends Controller
{
	public function actionSearch(){

		if(isset($_GET['term'])){
			$term = (string)$_GET['term'];
			setlocale(LC_ALL, 'nl_NL'); //Nodig voor iconv
			$term = iconv('UTF-8', 'ASCII//TRANSLIT', $term);
    		$term = preg_replace("/[^ \w]+/", '-', $term);
    		$term = mb_strtolower($term);
    		$term = trim($term, '-');

    		if(strlen($term) > 1){
				$json = [];
				foreach(Tag::model()->findTags($term) as $tag){
					$json[] = [
						'value' => $tag->id,
						'label' => $tag->name,
					];
				}
				echo json_encode($json);
    		}
		}
	}
}