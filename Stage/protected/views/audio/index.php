<?php
$this->breadcrumbs=array(
    'Audio',
);

$this->menu=array(
    array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
    array('label'=>'Maak audio bestand aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('user')),
    array('label'=>'Beheer audio bestand','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);

function objectToTagString($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    $string = array();
    foreach ($tags as $tag) {
        $string[] = $tag["name"];
    }
    return implode(", ", $string);
}

function fileLocation($id, $title) {
    return CHtml::link($title,array('audio/view', 'id'=>$id));;
}


?>

<h1>Audio</h1>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
            'id'=>'audio-grid',
            'type'=>'striped bordered',
            'dataProvider'=>$model->search(),
            'columns'=>array(
                 array('name'=>'title', 'header'=>'Naam audiobestand', 'value'=>'fileLocation($data->id, $data->title)', 'type'=>'raw'),
                 array('name'=>'tag_search', 'header'=>'Tags', 'value'=>'objectToTagString($data->tags)'), //Omdat een result meerdere tags kan hebben moeten we deze verwerken.
                 array('name'=>'year', 'header'=>'Jaar'),
             ),
            'enableHistory'=>true,
            'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),
        ));
 ?>
