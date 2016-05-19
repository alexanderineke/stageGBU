<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="nl" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
</head>

<body>
<div id="wrap">
    <?php $this->widget('bootstrap.widgets.TbNavbar', array(
        'type'=>'inverse', // null or 'inverse'
        'brand'=>CHtml::encode(Yii::app()->name),
        'brandUrl'=>'index.php',
        'collapse'=>true, // requires bootstrap-responsive.css
        'items'=>array(
            array(
                'class'=>'bootstrap.widgets.TbMenu',
                'items'=>array(
                    array('label'=>'Home', 'url'=>array('/site/index')),
                    array('label'=>'Gebruikers', 'url'=>array('/user'), 'visible'=>Yii::app()->user->checkAccess('user')),
                    array('label'=>'Afbeeldingen', 'url'=>array('/image'), 'visible'=>Yii::app()->user->checkAccess('moderator')),
    				array('label'=>'Inloggen', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
    				array('label'=>'Uitloggen ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
                ),
            ),
        ),
    )); ?>

    <?php echo $content; ?>
    <div id="push"></div>
</div>
<footer id="footer">
	<div class="container">
		<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/gbu.png" alt="GBU Grafici" />
        <span>Pagina geladen in: <?php echo round(Yii::getLogger()->getExecutionTime(), 6); ?> seconden</span>
	</div>
</footer>

</body>
</html>
