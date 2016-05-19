
<?php $this->beginContent('/layouts/main'); ?>

<div class="container">
	<div class="row-fluid" id="content-wrap">
		<div class="span9">
			<?php if(!empty($this->breadcrumbs)){ ?>
			<div id="breadcrumbs">
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array('links'=>$this->breadcrumbs)); ?> 
			</div><!-- breadcrumbs -->
			<?php } ?>
			
			<div id="content">
		
				<?php echo $content; ?>
			</div><!-- content -->
		</div>
		<div class="span3">
			<div id="sidebar">
				<?php $this->widget('bootstrap.widgets.TbMenu', array('type'=>'list', 'items'=>$this->menu)); ?>
			</div><!-- sidebar -->
		</div>
	</div>
</div>
<?php $this->endContent(); ?>