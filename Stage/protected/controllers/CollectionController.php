<?php

class CollectionController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index', 'create','update','add', 'delete', 'deleteimage', 'deletedocument', 'deletecollection'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'users'=>array('admin'),
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Collection;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Collection']))
		{
			$model->attributes=$_POST['Collection'];
			$model->setAttribute('user_id', Yii::app()->user->getId());
			$model->setAttribute('created_on', new CDbExpression('NOW()'));
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Collection']))
		{
			$model->attributes=$_POST['Collection'];
			$model->setAttribute('modified_on', new CDbExpression('NOW()'));
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

			$this->actionDeleteCollection(null, $id);

			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
	}


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Collection('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Collection']))
			$model->attributes=$_GET['Collection'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Collection('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Collection']))
			$model->attributes=$_GET['Collection'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Add item to a collection
	 */
	public function actionAdd(){

		if(isset($_POST['type'])){
			if($_POST['type'] === 'image'){
				$model=new CollectionImage();
			}elseif($_POST['type'] === 'document'){
				$model=new CollectionDocument();
			}elseif($_POST['type'] === 'collection'){
				$model=new CollectionCollection();
			}else{
				throw new CHttpException(404,'The requested page does not exist.');
			}
			if($model->add((int)$_POST['id'], (int)$_POST['collection']))
				$this->redirect(array('view','id'=>(int)$_POST['collection']));
			else
				throw new CHttpException(404,'The requested page does not exist.');
		}else
			throw new CHttpException(404,'The requested page does not exist.');
	}

	/**
	 * Deletes a particular image from CollectionImage.
	 */
	public function actionDeleteImage($id, $image)
	{
		if(!empty($id)){
			$model = $this->loadModel($id);

			if($model->checkOwnership()){
				$model=new CollectionImage; 
				if($model->deleteImage($image, $id)){
					Yii::app()->user->setFlash('success', "Afbeelding met succes uit collectie verwijderd");
					$this->redirect(array('view','id'=>$id));
				}else{
					throw new CHttpException(401,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
				}
			}else{
				throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
			}
		}else{
			$model=new CollectionImage; 
			if($model->deleteImage($image)){
				Yii::app()->user->setFlash('success', "Afbeeldingen met succes uit collecties verwijderd");
				$this->redirect(array('image/index'));
			}else{
				throw new CHttpException(401,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
			}			
		}
	}	

	/**
	 * Deletes a particular document from CollectionDocument.
	 */
	public function actionDeleteDocument($id, $document)
	{
		$model = $this->loadModel($id);

		if($model->checkOwnership()){
			$model=new CollectionDocument; 
			if($model->deleteDocument($document, $id)){
				Yii::app()->user->setFlash('success', "Document met succes uit collectie verwijderd");
				$this->redirect(array('view','id'=>$id));
			}else{
				throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
			}
		}else{
			throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
		}
	}		

	/**
	 * Deletes a particular collection from CollectionCollection.
	 */
	public function actionDeleteCollection($id, $collection)
	{
		if(!empty($id)){
			$model = $this->loadModel($id);

			if($model->checkOwnership()){
				$model=new CollectionCollection; 
				if($model->deleteCollection($collection, $id)){
					Yii::app()->user->setFlash('success', "Collectie met succes uit collectie verwijderd");
					$this->redirect(array('view','id'=>$id));
				}else{
					throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
				}
			}else{
				throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
			}
		}else{
			$model=new CollectionCollection; 
			if($model->deleteCollection($collection, null)){
				Yii::app()->user->setFlash('success', "Collectie met succes uit collecties verwijderd");
			}else{
				throw new CHttpException(400,'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
			}			
		}
	}				

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Collection::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'De gevraagde pagina bestaat niet.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='collection-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
