<?php

class ImageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */

	public $layout='/layouts/column2';
	protected $tags = array();

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update', 'create', 'process', 'upload', 'batchupload'),
				'roles'=>array('moderator'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	* Upload a single image
	**/
	public function actionUpload()
	{
		$model = new ImageTemp;
        $uploadedFile = CUploadedFile::getInstanceByName('Image[file]');
        $rnd = rand(0,9999); 
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";  // random number + file name
        if(!is_dir(Yii::app()->basePath.'/../uploads/'.$folderName)){
        	mkdir(Yii::app()->basePath.'/../uploads/'.$folderName);
        }
		if($uploadedFile->saveAs(Yii::app()->basePath.'/../uploads/'.$folderName.'/'.$fileName)){ 		
			$id = $model->addTempFile($fileName, $folderName);
			if($id){
		        Yii::app()->user->setState('filesToProcess', array($id));	
			}else{
				throw new CHttpException(400,'Upload niet gelukt.');
			}
		}  
	}	

	/**
	* Upload multiple images
	**/
	public function actionBatchupload()
	{
		$model = new ImageTemp;
        $uploadedFile = CUploadedFile::getInstanceByName('Image[file]');
        $rnd = rand(0,9999); 
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";  // random number + file name
        if(!is_dir(Yii::app()->basePath.'/../uploads/'.$folderName)){
        	mkdir(Yii::app()->basePath.'/../uploads/'.$folderName);
        }
		if($uploadedFile->saveAs(Yii::app()->basePath.'/../uploads/'.$folderName.'/'.$fileName)){ 		
			$id = $model->addTempFile($fileName, $folderName);
			if($id){
				$fileQueue = Yii::app()->user->getState('filesToProcess');
		       	array_push($fileQueue, $id);
		        Yii::app()->user->setState('filesToProcess', $fileQueue);		
			}else{
				throw new CHttpException(400,'Upload niet gelukt.');
			}
		}  
	}

/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Image'])){	

			//Haal het bestand op
			$fileQueue = Yii::app()->user->getState('filesToProcess');
			if($fileQueue){
				$imageTempModel = ImageTemp::model()->findByPk($fileQueue[0]);
				$file = $imageTempModel->getAttributes(array('file', 'format', 'location'));
			}

			//Het user_id van de gebruiker overnemen
			$model->setAttribute('user_id', Yii::app()->user->getId());
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			$model->attributes = $_POST['Image'];
			
			if($this->generateTags()){ //Kijk welke tags er niet zijn en welke niet
				
				if($model->save()){ //Probeer het formulier op te slaan 
										
					if($this->saveTags($model->id)){ //Probeer de tags te koppelen
						if($fileQueue){
							if(ImageFile::model()->saveImage($model->id, $this->tags[0], $file)){ //Kijk of het betand koppelen lukt
								array_shift($fileQueue); //Verwijder dit bestand uit de wachtrij
								Yii::app()->user->setState('filesToProcess', $fileQueue);
								$this->redirect(array('view', 'id'=>$model->id));
							}else{ 
								Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");	
							}
						}else{
							$this->redirect(array('view', 'id'=>$model->id));
						}
					}else{ //Fout bij het koppelen van de tags
						Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
					}
				}else{ //Fout bij het opslaan
					Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
				}
			}else{ //Fout bij het generen van de tags
				Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
			}
		}else{	
			//Uploads die nog in de sessie leven verwijderen
			Yii::app()->user->setState('filesToProcess', array());
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model=new CollectionImage; 
			if(!$model->deleteImage($id, null)){
				throw new CHttpException(401,'Invalid request. Please do not repeat this request again.');
			}	

			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	* Start of the upload/adding process
	**/	
	public function actionCreate()
	{
		$model = new Image;
		Yii::app()->user->setState('filesToProcess', array());
		
		$this->render('create',array(
			'model'=>$model,
		));		
	}

	/**
	* Moving temp files to DB with various data attached.
	**/
	public function actionProcess()
	{
		$id = Yii::app()->request->getParam('id');

		if($id)
			$model = $this->loadModel($id);
		else
			$model = new Image;

		//Haal de wachtrij op
		$fileQueue = Yii::app()->user->getState('filesToProcess');
		if(!$fileQueue) $this->redirect(array('index'));

		//Haal bestands data op
		if(!$id && isset($_POST['Image'])){

			//Haal het eerst volgende bestand uit de wachtrij op in de database
			$imageTempModel = ImageTemp::model()->findByPk($fileQueue[0]);
			$file = $imageTempModel->getAttributes(array('file', 'format', 'location'));
			$model->attributes = $_POST['Image'];

		}elseif($id){		
			
			if($model->images){
				$file = $model->images[0];
			}else{
				//Haal het eerst volgende bestand uit de wachtrij op in de database
				$imageTempModel = ImageTemp::model()->findByPk($fileQueue[0]);
				$file = $imageTempModel->getAttributes(array('file', 'format', 'location'));
			}
		}else{
			//Haal het eerst volgende bestand uit de wachtrij op in de database
			$imageTempModel = ImageTemp::model()->findByPk($fileQueue[0]);
			$file = $imageTempModel->getAttributes(array('file', 'format', 'location'));			
		}			

		//Probeer op te slaan
		if(isset($_POST['Image']['included_file'])){

			//Het user_id van de gebruiker overnemen
			$model->setAttribute('user_id', Yii::app()->user->getId());
			$model->setAttribute('created_on', new CDbExpression('NOW()'));
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			
			if($this->generateTags()){ //Kijk welke tags er niet zijn en welke niet
				
				if($model->save()){ //Probeer het formulier op te slaan 
										
					if($this->saveTags($model->id)){ //Probeer de tags te koppelen

						if(isset($_POST['Image']['collection'])){ //Kijk of er een collectie is geselecteerd
							$collection = (int)$_POST['Image']['collection'];
							if($collection > 0){
								if(!CollectionImage::add($model->id, $collection)) //Kijk of het toevoegen aan de collectie is gelukt
									Yii::app()->user->setFlash('warning', "De afbeelding kon niet aan de door u geselecteerde collectie worden toegevoegd.");		
							}
						}
						if(!$model->images){
							if(ImageFile::model()->saveImage($model->id, $this->tags[0], $file)){ //Kijk of het betand koppelen lukt
								array_shift($fileQueue); //Verwijder dit bestand uit de wachtrij
								Yii::app()->user->setState('filesToProcess', $fileQueue);

								if(!empty($fileQueue)){
									$imageTempModel = ImageTemp::model()->findByPk($fileQueue[0]);
									$file = $imageTempModel->getAttributes(array('file', 'format', 'location'));
								}else{
									Yii::app()->user->setFlash('succes', "Afbeelding(en) met succes toegevoegd.");	
								}		
							}else{ 
								Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");	
								$this->redirect(array('process', 'id'=>$model->id));
							}
						}

					}else{ //Fout bij het koppelen van de tags
						Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");	
						$this->redirect(array('process', 'id'=>$model->id));
					}
				}else{ //Fout bij het opslaan
					Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
				}
			}else{ //Fout bij het generen van de tags
				Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
			}
		}

		if(!$fileQueue || !isset($file)) $this->redirect(array('index'));

		$list = CHtml::listData(
						Collection::model()->findAll(
							array(	'order' => 'title', 
									'condition' => 'user_id=:id AND published=1',
									'params' => array(':id'=>Yii::app()->user->getId())
							)
			), 'id', 'title');

		$this->render('process',array(
			'model'=>$model,
			'file'=>$file,
			'collection_list'=> $list,
		));
	}	


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// if(!Yii::app()->user->checkAccess('moderator')) $condition = 'published=1';
		// else $condition = '';
		// $dataProvider=new CActiveDataProvider('Image', array(
		// 	'criteria'=>array(
		// 		'condition'=>$condition,
		// 		'order'=>'title ASC',
		// 	),
		// ));
		$this->render('index',array(
			//'dataProvider'=>$dataProvider,
			'model'=>new Image(),
		));
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Image('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Image']))
			$model->attributes=$_GET['Image'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Image::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='image-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	protected function generateTags(){
		$tags = array();
		if(isset($_POST['tags'])){
			foreach ($_POST['tags'] as $tag) {
				$tags[] = (int)$tag; //Zorg dat de id's nummers zijn
			}
		}
		if(isset($_POST['newtags'])){
			$newSlugs = array();
			$newTags = array();
			foreach ($_POST['newtags'] as $i => $newtag){
				//Convert to ASCII, remove spaces and convert to lowercase
				$name = (string)$newtag; //Zorg dat elke tag een string is
				setlocale(LC_ALL, 'nl_NL'); //Nodig voor iconv
				$name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        		$name = preg_replace('/[^ \w]+/', '-', $name);
        		$name = mb_strtolower($name);
        		$name = trim($name, '-');

        		$newSlugs[$i] = $name;
        		$newTags[$i] = mb_strtolower($newtag);
			}

			$selectedTags = Tag::model()->check($newSlugs);
			$remainingTags = array();
			$remainingSlugs = array();

			if(isset($selectedTags)){
				$compareTags = array();
				foreach($selectedTags as $t){
				   $compareTags[$t->slug] = $t->id;
				}	
				foreach($newSlugs as $i => $newslug){
					if(!array_key_exists($newslug, $compareTags)){
						if(!in_array($newslug, $remainingSlugs))
							$remainingTags[] = $newTags[$i];
							$remainingSlugs[] = $newslug;
					}
				}
			}else{
				$remainingTags = $newTags;
			}

			if (isset($remainingTags) && sizeof($remainingTags)){
				if($addedTags = Tag::model()->add($remainingTags, $remainingSlugs)){
					foreach ($addedTags as $i) {
						$tags[] = $i;
					}
				}else{
					$errorOccured = true;
				}
			}

			if (isset($compareTags) && sizeof($compareTags)){
				foreach ($compareTags as $i) {
					$tags[] = $i;
				}
			}

		}
		$this->tags = $tags;

		if(sizeof($tags))
			return true;		
	}

	/**
	 * Save the tags 
	 */
	protected function saveTags($image_id){
		$errorOccured = false;

		if(!ImageTag::model()->add($image_id, array_unique($this->tags)))
			$errorOccured = true;
		$prevTags = explode(',',$_POST['Image']['tags_previous']);
		$prevTagsArr = array();
		foreach($prevTags as $i){
			if((int)$i)
				$prevTagsArr[] = (int)$i;
		}
		$deleteTagsArr = array_diff($prevTagsArr, $this->tags);
		if(sizeof($deleteTagsArr) && sizeof($prevTagsArr)){
			if(!ImageTag::model()->deleteTags($image_id, $deleteTagsArr)){
				$errorOccured = true;
			}	
		}
		if(!$errorOccured)
			return true;
	}

}
