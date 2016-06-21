<?php

class DocumentController extends Controller
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
				'actions'=>array('admin','delete', 'batchdocs'),
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
	* Upload a single document
	**/
	public function actionUpload()
	{
		$model = new DocumentTemp;
        $uploadedFile = CUploadedFile::getInstanceByName('Document[file]');
        $rnd = rand(0,9999); 
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";  // random number + file name
        if (!is_dir(Yii::app()->basePath.'/../uploads/'.$folderName)) {
        	mkdir(Yii::app()->basePath.'/../uploads/'.$folderName);
        }
		if ($uploadedFile->saveAs(Yii::app()->basePath.'/../uploads/'.$folderName.'/'.$fileName)) { 		
			$id = $model->addTempFile($fileName, $folderName);
			if ($id) {
		        Yii::app()->user->setState('filesToProcess', array($id));	
			} else {
				throw new CHttpException(400,'Upload niet gelukt.');
			}
		}  
	}	

	/**
	* Upload multiple documents
	**/
	public function actionBatchupload()
	{
		$model = new DocumentTemp;
        $uploadedFile = CUploadedFile::getInstanceByName('Document[file]');
        $rnd = rand(0,9999); 
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";  // random number + file name
        if (!is_dir(Yii::app()->basePath.'/../uploads/'.$folderName)) {
        	mkdir(Yii::app()->basePath.'/../uploads/'.$folderName);
        }
		if ($uploadedFile->saveAs(Yii::app()->basePath.'/../uploads/'.$folderName.'/'.$fileName)) { 		
			$id = $model->addTempFile($fileName, $folderName);
			if ($id) {
				$fileQueue = Yii::app()->user->getState('filesToProcess');
		       	array_push($fileQueue, $id);
		        Yii::app()->user->setState('filesToProcess', $fileQueue);		
			} else {
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

		if (isset($_POST['Document'])) {	

			//Haal het bestand op
			$fileQueue = Yii::app()->user->getState('filesToProcess');
			if ($fileQueue) {
				$documentTempModel = DocumentTemp::model()->findByPk($fileQueue[0]);
				$file = $documentTempModel->getAttributes(array('file', 'format', 'location'));
				$existingFile = false;
			} else { 
				if ($model->documents) {
					$file = $model->documents[0];
					$existingFile = true;
				}
			}

			//Het user_id van de gebruiker overnemen
			$model->setAttribute('user_id', Yii::app()->user->getId());
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			$model->attributes = $_POST['Document'];
			$model->setAttribute('published', -1);

			//Kijk welke tags er niet zijn en welke niet
			if (!$this->generateTags()) {
				Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
				goto render;
			}

			//Lees de inhoud van het document uit
			if (!$this->getDocumentContent($model, $file, $existingFile)) {
				Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het uitlezen van het document.");
				goto render;
			}

			//Probeer het op te slaan
			if (!$model->save()) {
				Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
				goto render;
			}

			//Sla de tags op
			if (!$this->saveTags($model->id)) {
				Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
				goto render;
			}

			//Werk de document verwijzing ook bij
			if (DocumentFile::model()->saveDocument($model->id, $this->tags[0], $file)) {
				array_shift($fileQueue); //Verwijder dit bestand uit de wachtrij
				Yii::app()->user->setState('filesToProcess', $fileQueue);
				$this->redirect(array('view', 'id'=>$model->id));
			} else {
				Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");	
				goto render;
			}	

			//Geen andere documenten in de wachtrij? 
			if (!$fileQueue) {
				$this->redirect(array('view', 'id'=>$model->id));
			}


		} else {	
			//Uploads die nog in de sessie leven verwijderen
			Yii::app()->user->setState('filesToProcess', array());
			goto render;
		}

		render: {
			$this->render('update',array(
				'model'=>$model,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			$model=new CollectionDocument; 
			if (!$model->deleteDocument($id, null)) {
				throw new CHttpException(401,'Invalid request. Please do not repeat this request again.');
			}	

			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else 
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	* Start of the upload/adding process
	**/	
	public function actionCreate()
	{
		$model = new Document;
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

		if ($id)
			$model = $this->loadModel($id);
		else
			$model = new Document;

		//Haal de wachtrij op
		$fileQueue = Yii::app()->user->getState('filesToProcess');
		if (!$fileQueue) $this->redirect(array('index'));

		//Haal bestands data op
		if (!$id && isset($_POST['Document'])) {

			//Haal het eerst volgende bestand uit de wachtrij op in de database
			$documentTempModel = DocumentTemp::model()->findByPk($fileQueue[0]);
			$file = $documentTempModel->getAttributes(array('file', 'format', 'location'));
			$model->attributes = $_POST['Document'];

		} elseif ($id) {		
			
			if ($model->documents) {
				$file = $model->documents[0];
			} else {
				//Haal het eerst volgende bestand uit de wachtrij op in de database
				$documentTempModel = DocumentTemp::model()->findByPk($fileQueue[0]);
				$file = $documentTempModel->getAttributes(array('file', 'format', 'location'));
			}
		} else {
			//Haal het eerst volgende bestand uit de wachtrij op in de database
			$documentTempModel = DocumentTemp::model()->findByPk($fileQueue[0]);
			$file = $documentTempModel->getAttributes(array('file', 'format', 'location'));			
		}	

		//Probeer op te slaan
		if (isset($_POST['Document']['included_file'])) {
			//Het user_id van de gebruiker overnemen
			$model->setAttribute('user_id', Yii::app()->user->getId());
			$model->setAttribute('created_on', new CDbExpression('NOW()'));
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			
			if ($this->generateTags()) { //Kijk welke tags er niet zijn en welke niet

				if ($this->getDocumentContent($model, $file)) {

					if ($model->save()) { //Probeer het formulier op te slaan 
											
						if ($this->saveTags($model->id)) { //Probeer de tags te koppelen

							if (isset($_POST['Document']['collection'])) { //Kijk of er een collectie is geselecteerd
								$collection = (int)$_POST['Document']['collection'];
								if ($collection > 0) {
									if (!CollectionDocument::add($model->id, $collection)) //Kijk of het toevoegen aan de collectie is gelukt
										Yii::app()->user->setFlash('warning', "Het document kon niet aan de door u geselecteerde collectie worden toegevoegd.");		
								}
							}
							if (!$model->documents) {
								if (DocumentFile::model()->saveDocument($model->id, $this->tags[0], $file)) { //Kijk of het betand koppelen lukt
									array_shift($fileQueue); //Verwijder dit bestand uit de wachtrij		
									Yii::app()->user->setState('filesToProcess', $fileQueue);

									if (!empty($fileQueue)) {
										$documentTempModel = DocumentTemp::model()->findByPk($fileQueue[0]);
										$file = $documentTempModel->getAttributes(array('file', 'format', 'location'));
									} else {
										Yii::app()->user->setFlash('succes', "Document(en) met succes toegevoegd.");	
									}			
								} else { 
									Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");	
									$this->redirect(array('process', 'id'=>$model->id));
								}
							}

						} else { //Fout bij het koppelen van de tags
							Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");	
							$this->redirect(array('process', 'id'=>$model->id));
						}
					} else { //Fout bij het opslaan
						Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
					}					
				} else {
					Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het uitlezen van het document.");
				}
			} else { //Fout bij het generen van de tags
				Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
			}
		}

		if (!$fileQueue || !isset($file)) $this->redirect(array('index'));

		$list = CHtml::listData(
						Collection::model()->findAll(
							array(	'order' => 'title', 
									'condition' => 'user_id=:id AND published=1',
									'params' => array(':id'=>Yii::app()->user->getId())
							)
			), 'id', 'title');

		render: {
			$this->render('process',array(
				'model'=>$model,
				'file'=>$file,
				'collection_list'=> $list,
			));
		}
	}	

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if (!Yii::app()->user->checkAccess('moderator')) $condition = 'published=1';
		else $condition = '';
		$dataProvider=new CActiveDataProvider('Document', array(
			'criteria'=>array(
				'condition'=>$condition,
				'order'=>'title ASC',
			),
		));
		$this->render('index',array(
			'model'=>new Document(),
			'dataProvider'=>$dataProvider,
		));
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Document('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Document']))
			$model->attributes=$_GET['Document'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

 	/**
 	 * Update document counter
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Document::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


	/**
	 * Read content of existing documents and put it the DB
	 */
	protected function actionBatchdocs() 
	{
 		$fileModel = new DocumentFile;
 		$files = $fileModel->findAll(array('condition' => 'state=1', 'order'=>'id DESC'));

 		foreach ($files as $record => $columns) {
 			$file['file'] = $columns['file'];
 			$file['format'] = $columns['format'];
 			$file['location'] = $columns['location'];

 			$documentModel = Document::model()->findByPk((int)$columns['document_id']);
 			if ($documentModel)
	 			if ($this->getDocumentContent($documentModel, $file, true))
	 				$documentModel->save();	
 		}

 	}


	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='document-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Checks if the tags entered are new and if they should be added to the DB
	 */
	protected function generateTags() 
	{
		$tags = array();
		if (isset($_POST['tags'])) {
			foreach ($_POST['tags'] as $tag) {
				$tags[] = (int)$tag; //Zorg dat de id's nummers zijn
			}
		}
		if (isset($_POST['newtags'])) {
			$newSlugs = array();
			$newTags = array();
			foreach ($_POST['newtags'] as $i => $newtag) {
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

			if (isset($selectedTags)) {
				$compareTags = array();
				foreach($selectedTags as $t) {
				   $compareTags[$t->slug] = $t->id;
				}	
				foreach($newSlugs as $i => $newslug) {
					if (!array_key_exists($newslug, $compareTags)) {
						if (!in_array($newslug, $remainingSlugs))
							$remainingTags[] = $newTags[$i];
							$remainingSlugs[] = $newslug;
					}
				}
			} else {
				$remainingTags = $newTags;
			}

			if (isset($remainingTags) && sizeof($remainingTags)) {
				if ($addedTags = Tag::model()->add($remainingTags, $remainingSlugs)) {
					foreach ($addedTags as $i) {
						$tags[] = $i;
					}
				} else {
					$errorOccured = true;
				}
			}

			if (isset($compareTags) && sizeof($compareTags)) {
				foreach ($compareTags as $i) {
					$tags[] = $i;
				}
			}

		}
		$this->tags = $tags;

		if (sizeof($tags))
			return true;		
	}

	/**
	 * Save the tags 
	 * @param ID of the document in question
	 */
	protected function saveTags($document_id) {
		$errorOccured = false;

		if (!DocumentTag::model()->add($document_id, array_unique($this->tags)))
			$errorOccured = true;
		$prevTags = explode(',',$_POST['Document']['tags_previous']);
		$prevTagsArr = array();
		foreach($prevTags as $i) {
			if ((int)$i)
				$prevTagsArr[] = (int)$i;
		}
		$deleteTagsArr = array_diff($prevTagsArr, $this->tags);
		if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
			if (!DocumentTag::model()->deleteTags($document_id, $deleteTagsArr)) {
				$errorOccured = true;
			}	
		}
		if (!$errorOccured)
			return true;
	}
 

 	/**
 	* Get PDF content
 	* @param the model of the current document
 	* @param the location of the file
 	* @param boolean if the file already existed 
 	*/
 	protected function getDocumentContent($model, $file, $existing = false) {
 		if ($existing)
 			$file = Yii::app()->basePath.'/../uploads/documenten/'.$file['location'].'/'.$file['file'].$file['format'];
 		else
 			$file = Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file'];
 			
 		if (file_exists($file)) {
	 		$parser = new \Smalot\PdfParser\Parser();
	 		$pdf    = $parser->parseFile($file);
	 		$pages  = $pdf->getPages();

	 		$text =  '';
	 		foreach ($pages as $page) {
			    $text .= $page->getText();
			}
			//setlocale(LC_ALL, 'nl_NL'); //Nodig voor iconv
			//$text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
			$text = preg_replace('/[^0-9a-zA-Z ]/', ' ', $text);
			$text = preg_replace(array('/\b\w{1,2}\b/','/\s+/'),array('',' '),$text);

			echo $text;
			$model->setAttribute('content', $text);
			return true;
		} else
			return false;
 	}
}