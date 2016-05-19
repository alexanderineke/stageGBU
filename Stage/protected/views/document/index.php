<?php
$this->breadcrumbs=array(
	'Documenten',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak documenten aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Beheer documenten','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);

function objectToTagString($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    $string = array();
    foreach ($tags as $tag) {
        $string[] = $tag["name"];
    }
    return implode(", ", $string);
}

function fileLocation($id, $title) {
    return CHtml::link($title,array('document/view', 'id'=>$id));;
}


?>

<h1>Documenten</h1>

<?php 
 	$this->widget('bootstrap.widgets.TbGridView', array(
            'id'=>'document-grid',
            'type'=>'striped bordered',
            'dataProvider'=>$model->search(),
            'columns'=>array(
                 array('name'=>'title', 'header'=>'Naam document', 'value'=>'fileLocation($data->id, $data->title)', 'type'=>'raw'),
                 array('name'=>'tag_search', 'header'=>'Tags', 'value'=>'objectToTagString($data->tags)'), //Omdat een result meerdere tags kan hebben moeten we deze verwerken.
                 array('name'=>'year', 'header'=>'Jaar'),
             ),
            'enableHistory'=>true,
            'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),
        ));
 ?>
