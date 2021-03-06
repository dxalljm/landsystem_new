<?php

namespace frontend\controllers;

use app\models\Auditprocess;
use app\models\Estate;
use app\models\Mortgage;
use Yii;
use app\models\Loan;
use frontend\models\loanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Farms;
use app\models\Lockedinfo;
use app\models\Logs;
use app\models\Theyear;
use app\models\Reviewprocess;
use app\models\User;
/**
 * LoanController implements the CRUD actions for Loan model.
 */
class LoanController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    public function beforeAction($action)
    {
        if(Yii::$app->user->isGuest) {
            $this->redirect(['site/logout']);
        } else {
            return true;
        }
    }
//     public function beforeAction($action)
//     {
//     	$action = Yii::$app->controller->action->id;
//     	if(\Yii::$app->user->can($action)){
//     		return true;
//     	}else{
//     		throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
//     	}
//     }
    /**
     * Lists all Loan models.
     * @return mixed
     */
    public function actionLoanindex($farms_id)
    {
        $farm = Farms::findOne($farms_id);
        $searchModel = new loanSearch();
        $params = Yii::$app->request->queryParams;
        $params['loanSearch']['farms_id'] = (integer)$farms_id;
        $dataProvider = $searchModel->searchone($params);
		Logs::writeLog('贷款列表',$farms_id);
        return $this->render('loanindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'farm' => $farm,
        ]);
    }

    
    public function actionLoaninfo()
    {
    	$searchModel = new loanSearch();
    	$params = Yii::$app->request->queryParams;
    	$whereArray = Farms::getManagementArea()['id'];
    	if (!isset($params ['loanSearch'] ['management_area'])) {
    		$params ['loanSearch'] ['management_area'] = $whereArray;
    	}
    	
    	if(!isset($params['loanSearch']['lock']))
     		$params['loanSearch']['lock'] = 1;
    	$params['loanSearch']['year'] = User::getYear();
        $params['loanSearch']['state'] = 1;
    	
    	$dataProvider = $searchModel->search ( $params );
        Logs::writeLogs('首页板块-贷款信息');
    	return $this->render('loaninfo',[
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider,
    	]);
    }
    /**
     * Displays a single Loan model.
     * @param integer $id
     * @return mixed
     */
    public function actionLoanview($id,$farms_id)
    {
        $model = $this->findModel($id);
    	Logs::writeLogs('查看一条贷款信息',$model);
        return $this->render('loanview', [
            'model' => $model,
        	'farms_id' => $farms_id,
        ]);
    }

    public function actionLoanexamine($farms_id)
    {
    	
    }
    
    /**
     * Creates a new Loan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionLoancreate($farms_id)
    {
        $model = new Loan();
        $process = Auditprocess::find()->where(['actionname'=>'loancreate'])->one()['process'];

//        var_dump($process);exit;
// 		var_dump(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post())) {
//            var_dump($_POST);exit;
            $auditprocessID = Auditprocess::find('id')->where(['actionname'=>Yii::$app->controller->action->id])->one()['id'];
        	$reviewprocessID =Reviewprocess::processRun($auditprocessID,$farms_id);
        	$model->create_at = time();
        	$model->update_at = $model->create_at;
        	$model->reviewprocess_id = $reviewprocessID;
        	$model->state = 2;
        	$model->management_area = Farms::getFarmsAreaID($farms_id);
            $model->auditprocess_id = $auditprocessID;
            $model->lock = 1;
            $model->year = date('Y');
            $model->farmstate = Farms::find()->where(['id'=>$farms_id])->one()['state'];
        	if($model->save())
        	{
                $mModel = new Estate();
                $mModel->isself = Yii::$app->request->post('isself');
                $mModel->iscontract = Yii::$app->request->post('iscontract');
                $mModel->iscardid = Yii::$app->request->post('iscardid');
                $mModel->islocked = Yii::$app->request->post('islocked');
                $mModel->reviewprocess_id = $reviewprocessID;
                $mModel->save();

        		$farmsModel = Farms::findOne($farms_id);
        		$farmsModel->locked = 1;
        		$farmsModel->save();
        		Logs::writeLog('冻结农场',$farms_id);
        		$lockedinfoModel = new Lockedinfo();
        		$lockedinfoModel->farms_id = $farms_id;
                $loan = Loan::find()->where(['farms_id'=>$farms_id])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->one();
        		$lockedinfoModel->lockedcontent = '因农场在贷款期限中，已被冻结，不能进行此操作，贷款期限为'.$loan['begindate'].'——'.$loan['enddate'];
        		$lockedinfoModel->save();
        		Logs::writeLog('增加冻结信息',$lockedinfoModel->id,'',$lockedinfoModel->attributes);
        		
        	}
        	Logs::writeLogs('新增贷款信息',$model);
            return $this->redirect([
                'loanindex',
                'farms_id' => $farms_id,
            ]);
//            return $this->redirect ( [
//					'print/printloan',
//					'farms_id' => $farms_id,
//            		'loan_id' => $model->id,
//					'reviewprocessid' => $reviewprocessID
//			] );
        } else {
            return $this->render('loancreate', [
                'model' => $model,
                'process' => explode('>', $process),
            ]);
        }
    }

    public function actionLoanunlock($id)
    {
    	$model = $this->findModel($id);
        $model->lock = 0;
        $model->update_at = time();
        $model->save();
        Logs::writeLogs('解锁贷款冻结-解锁贷款',$model);
    	$farmModel = Farms::findOne($model->farms_id);
//        var_dump($farmModel);exit;
    	$farmModel->locked = 0;
    	$farmModel->save();
        Logs::writeLogs('解锁贷款冻结-解锁农场',$farmModel);
        $lockedinfo = Lockedinfo::find()->where(['farms_id'=>$farmModel->id])->one();
        if($lockedinfo) {
            $lockedinfoModel = Lockedinfo::findOne($lockedinfo['id']);
            $lockedinfoModel->delete();
        }
    	return $this->redirect(['reviewprocess/reviewprocessunlock']);
    }
    
    /**
     * Updates an existing Loan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionLoanupdate($id,$farms_id)
    {
        $model = $this->findModel($id);
        $process = Auditprocess::find()->where(['actionname'=>'loancreate'])->one()['process'];
        $old = $model->attributes;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//         	$auditprocessID = Auditprocess::find()->where(['actionname'=>'loancreate'])->one()['id'];
            $revieprocessModel = Reviewprocess::findOne($model->reviewprocess_id);
            $revieprocessModel->estate = 1;
            $revieprocessModel->mortgage = 2;
            $revieprocessModel->regulations = 3;
            $revieprocessModel->finance = 3;
            $revieprocessModel->leader = 3;
            $revieprocessModel->update_at = time();
            $revieprocessModel->estatetime = time();
            $revieprocessModel->save();
//         	estate>mortgage>regulations>finance>leader
            $model->create_at = time();
            $model->update_at = $model->create_at;
            $model->reviewprocess_id = $revieprocessModel->id;
            $model->state = 2;
            $model->management_area = Farms::getFarmsAreaID($farms_id);
            $model->auditprocess_id = $model->auditprocess_id;
            if($model->save())
            {
                $estate = Estate::find()->where(['reviewprocess_id'=>$model->reviewprocess_id])->one();
                if($estate) {
                    $mModel = Estate::findOne($estate['id']);
                } else {
                    $mModel = new Estate();
                }
                $mModel->isself = Yii::$app->request->post('isself');
                $mModel->iscontract = Yii::$app->request->post('iscontract');
                $mModel->iscardid = Yii::$app->request->post('iscardid');
                $mModel->islocked = Yii::$app->request->post('islocked');
                $mModel->reviewprocess_id = $revieprocessModel->id;;
                $mModel->save();

                $farmsModel = Farms::findOne($farms_id);
                $farmsModel->locked = 1;
                $farmsModel->save();
                Logs::writeLog('冻结农场',$farms_id);
                $lockedinfo = Lockedinfo::find()->where(['farms_id'=>$farms_id])->one();
                if($lockedinfo) {
                    $lockedinfoModel = Lockedinfo::findOne($lockedinfo['id']);
                } else {
                    $lockedinfoModel = new Lockedinfo();
                }
                $lockedinfoModel->farms_id = $farms_id;
                $lockedinfoModel->lockedcontent = '因农场在贷款期限中，已被冻结，不能进行此操作，解冻日期为'.Loan::find()->where(['farms_id'=>$farms_id,'year'=>date('Y')])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->one()['enddate'];
                $lockedinfoModel->save();
                Logs::writeLogs('更新冻结信息',$lockedinfoModel);

            }
            Logs::writeLogs('更新贷款信息',$model);
            return $this->redirect(['loanview', 'id' => $model->id,'farms_id'=>$model->farms_id]);
        } else {
            return $this->render('loanupdate', [
                'model' => $model,
                'process' => explode('>', $process),
                'farms_id' => $farms_id,
            ]);
        }
    }

    /**
     * Deletes an existing Loan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionLoandelete($id,$farms_id)
    {
        $loanModel = $this->findModel($id);
        Logs::writeLogs('删除贷款信息',$loanModel);
        $reviewprocessModel = Reviewprocess::findOne($loanModel->reviewprocess_id);
        $reviewprocessModel->delete();
        Logs::writeLogs('删除贷款审核流程信息',$reviewprocessModel);
		$loanModel->delete();
        $farm = Farms::findOne($farms_id);
		$farm->locked = 0;
		$farm->save();
        Logs::writeLogs('解冻农场',$farm);
		$lockedinfo = Lockedinfo::find()->where(['farms_id'=>$farms_id])->one();
		$lockedinfoModel = Lockedinfo::findOne($lockedinfo->id);
		$lockedinfoModel->delete();
		Logs::writeLogs('删除冻结信息',$lockedinfoModel);
        return $this->redirect(['loanindex','farms_id'=>$farms_id]);
    }

    public function actionLoansearch($tab,$begindate,$enddate)
    {
    	if(isset($_GET['tab']) and $_GET['tab'] !== \Yii::$app->controller->id) {
    		return $this->redirect ([$_GET['tab'].'/'.$_GET['tab'].'search',
    				'tab' => $_GET['tab'],
    				'begindate' => strtotime($_GET['begindate']),
    				'enddate' => strtotime($_GET['enddate']),
//    				$_GET['tab'].'Search' => ['management_area'=>$_GET['management_area']],
    		]);
    	} 
    	$searchModel = new loanSearch();
		if(!is_numeric($_GET['begindate']))
			 $_GET['begindate'] = strtotime($_GET['begindate']);
		if(!is_numeric($_GET['enddate']))
			 $_GET['enddate'] = strtotime($_GET['enddate']);

    	$dataProvider = $searchModel->searchIndex ( $_GET );
        Logs::writeLogs('结合查询-贷款信息');
    	return $this->render('loansearch',[
	    			'searchModel' => $searchModel,
	    			'dataProvider' => $dataProvider,
	    			'tab' => $_GET['tab'],
	    			'begindate' => $_GET['begindate'],
	    			'enddate' => $_GET['enddate'],
	    			'params' => $_GET,
    	]);    	
    }
    
    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
