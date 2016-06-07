<?php
namespace app\views\site;

use app\models\Search;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use app\models\Collection;
use app\assets\AppAsset;

?>
<?php $collection = new Collection; 
AppAsset::register($this)?>
<?php if(!empty($collection)): ?>
    <?php// $this->registerJsFile("collections-spotlight", "jQuery('#collections-spotlight').carousel();"); ?>
    <div id="collections-spotlight" class="carousel slide">
        <div class="carousel-inner">
            <?php foreach ($collection->collections as $i => $subcollection): ?>
                <?php if($i % 3 == 0): ?>
                    <?php if($i !== 0 && $i !== count($collection->collections)): ?>
                        </div>
                    <?php endif; ?>
                    <?php if($i == 0): ?>
                        <div class="item active">
                    <?php else: ?>
                        <div class="item">
                    <?php endif;?>
                <?php endif; ?>
                        <article class="span4 collection-thumb">
                            <?php if($subcollection->thumb): ?>
                                <?php echo Html::img('uploads/afbeeldingen/'.$subcollection->thumb->location.'/thumb/'.$subcollection->thumb->file.$subcollection->thumb->format, $subcollection->title, ['class'=>'img-polaroid collection-thumb-img span5']); ?>
                            <?php endif; ?>
                            <a href="<?php echo Url::to("collection/view", ["id"=>$subcollection->id]); ?>"  class="span7">
                                <h3 class="collection-thumb-title"><?php echo $subcollection->title; ?></h3>
                                <p><?php echo substr(strip_tags($subcollection->description), 0, 70); ?>...</p>
                                <small class="collection-thumb-items"><?php echo (count($subcollection->documents)+count($subcollection->images)+count($subcollection->collections)); ?> items</small>
                            </a>
                        </article>
                <?php if(count($collection->collections) == $i+1): ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <a class="carousel-control left" href="#collections-spotlight" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
        <a class="carousel-control right" href="#collections-spotlight" data-slide="next"><i class="fa fa-chevron-right"></i></a>
    </div>
    <hr />
<?php endif; ?>

<div class="container">
    <div class="span6">
        <h3>Laatste nieuws</h3>
        <div id="twitter">Berichten aan het laden...</div>
    </div>
    <div class="span6">
        <h3>Populaire zoektermen</h3>
        <?php
        $model=new Search;
        $popularTags = $model->popularTags();
        if(!empty($popularTags)){
            foreach($popularTags as $tag) {
                echo '<a class="btn btn-primary tag" href="'.Yii::getAlias('@web').'/index.php?r=search/tag&tag='.$tag['slug'].'">'.$tag['name'].'</a>';
            }
        }
        ?>
    </div>
</div>