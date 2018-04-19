<?php

namespace frontend\controllers;

use app\models\Plantingstructure;
use app\models\User;
use Yii;
use app\models\Plantingstructurecheck;
use frontend\models\plantingstructurecheckSearch;
use frontend\models\PlantingstructureyearfarmsidSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Farms;
use app\models\Lease;
use app\models\Plantinputproductcheck;
use app\models\Plantpesticidescheck;
use app\models\Plantinputproduct;
use app\models\Plantpesticides;
use app\models\Logs;
use app\models\Theyear;
use app\models\Plant;
use app\models\ManagementArea;
/**
 * PlantingstructurecheckController implements the CRUD actions for Plantingstructurecheck model.
 */
class PlantingstructurecheckController extends Controller
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
			return $this->redirect(['site/logout']);
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
    
    public function actionPlantingstructurecheckarea()
    {
    	$planting = Plantingstructurecheck::find()->all();
    	foreach ($planting as $value) {
    		$model = $this->findModel($value['id']);
    		$model->management_area = Farms::getFarmsAreaID($farms_id);
    		$model->save();
    	}
    }
    
    /**
     * Lists all Plantingstructurecheck models.
     * @return mixed
     */
    public function actionPlantingstructurecheckindex($farms_id)
    {
        $lease = Lease::find()->where(['farms_id'=>$farms_id])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
		$plantings = Plantingstructure::find()->where(['farms_id'=>$farms_id])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
		$checks = Plantingstructurecheck::find()->where(['farms_id'=>$farms_id])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
		$farmname = Farms::findOne($farms_id)['farmname'];
		Logs::writeLogs($farmname.'的种植结构复核数据');
        return $this->render('plantingstructurecheckindex', [
            'leases' => $lease,
			'plantings' => $plantings,
			'checks' => $checks,
        ]);
    }

    public function actionPlantingstructurecheckinfo()
    {
    	$searchModel = new plantingstructurecheckSearch();
    	$params = Yii::$app->request->queryParams;
    	$whereArray = Farms::getManagementArea()['id'];

		if(count($whereArray) == 7)
			$whereArray = null;

//    	if (empty($params['plantingstructurecheckSearch']['management_area'])) {
			$params ['plantingstructurecheckSearch'] ['management_area'] = $whereArray;
//		}


		$params ['plantingstructurecheckSearch'] ['year'] = User::getYear();
		$dataProvider = $searchModel->searchIndex ( $params );


		$searchModel2 = new PlantingstructureyearfarmsidSearch();

		$params2 = Yii::$app->request->queryParams;
// 		$params ['farmsSearch'] ['state'] = [1,2,3,4,5];
		// 管理区域是否是数组
//		if (empty($params2['PlantingstructureyearfarmsidSearch']['management_area'])) {
			$params2 ['PlantingstructureyearfarmsidSearch'] ['management_area'] = $whereArray;
//		}
		$dataProvider2 = $searchModel2->search ( $params2 );
//        var_dump($dataProvider->getModels());exit;
		//种植结构查询
//		$plantsearchModel = new plantingstructurecheckSearch();
//		$plantparams = Yii::$app->request->queryParams;
//		if (empty($plantparams['plantingstructurecheckSearch']['management_area'])) {
//			$plantparams ['plantingstructurecheckSearch'] ['management_area'] = $whereArray;
//		}
//		$plantparams['plantingstructurecheckSearch']['year'] = User::getYear();
//		$plantdataProvider = $plantsearchModel->searchIndex ( $plantparams );
//		if (is_array($plantsearchModel->management_area)) {
//			$plantsearchModel->management_area = null;
//		}
//		$plantdataProvider = $plantsearchModel->search ( $plantparams );
		Logs::writeLogs('首页十大板块-精准农业复核数据');
    	return $this->render('plantingstructurecheckinfo',[
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider,
    			'params' => $params,
				'searchModel2' => $searchModel2,
				'dataProvider2' => $dataProvider2,
				'params2' => $params2,
    	]);
    }
    
    
    
    public function actionPlantingstructurechecksearch($tab,$begindate,$enddate)
    {
//		var_dump('search');exit;
    	if(isset($_GET['tab']) and $_GET['tab'] !== \Yii::$app->controller->id) {
    		if($_GET['tab'] == 'yields')
    			$class = 'plantingstructurecheckSearch';
    		else
    			$class = $_GET['tab'].'Search';
    		
    		return $this->redirect ([$_GET['tab'].'/'.$_GET['tab'].'search',
    				'tab' => $_GET['tab'],
    				'begindate' => strtotime($_GET['begindate']),
    				'enddate' => strtotime($_GET['enddate']),
//					$class =>['management_area' =>  $_GET['management_area']],
    		]);
    	} 
//     	var_dump($_GET);exit;
    	$searchModel = new plantingstructurecheckSearch();
		if(!is_numeric($_GET['begindate']))
			 $_GET['begindate'] = strtotime($_GET['begindate']);
		if(!is_numeric($_GET['enddate']))
			 $_GET['enddate'] = strtotime($_GET['enddate']);

    	$dataProvider = $searchModel->searchIndex ( $_GET );
		Logs::writeLogs('综合查询-种植结构复核数据');
    	return $this->render('plantingstructurechecksearch',[
	    			'searchModel' => $searchModel,
	    			'dataProvider' => $dataProvider,
	    			'tab' => $_GET['tab'],
	    			'begindate' => $_GET['begindate'],
	    			'enddate' => $_GET['enddate'],
	    			'params' => $_GET,
    	]);    	
    }
    
    /**
     * Displays a single Plantingstructurecheck model.
     * @param integer $id
     * @return mixed
     */
    public function actionPlantingstructurecheckview($id)
    {
    	$model = $this->findModel($id);
    	$farm = Farms::find()->where(['id'=>$model->farms_id])->one();
    	$plantinputproductModel = Plantinputproductcheck::find()->where(['planting_id' => $id])->all();
    	$plantpesticidesModel = Plantpesticidescheck::find()->where(['planting_id'=>$id])->all();
    	Logs::writeLogs('查看种植结构复核数据',$model);
        return $this->render('plantingstructurecheckview', [
            'model' => $model,
        	'plantinputproductModel' => $plantinputproductModel,
        	'plantpesticidesModel' => $plantpesticidesModel,
        	'farm' => $farm,
        ]);
    }

    /**
     * Creates a new Plantingstructurecheck model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    

    //获取承租人的宗地信息，如果已经添加过，则过滤掉
    public function getListZongdi($lease_id)
    {
    	//$zongdi = array();
    	$lease = Lease::find()->where(['id'=>$lease_id])->one();
    	$zongdiarr = explode('、', $lease['lease_area']);
    	$plantings = Plantingstructurecheck::find()->where(['lease_id'=>$lease_id])->all();
    	
    	$zongdi = [];
    	if($plantings) {
	    	foreach($zongdiarr as $value) {
	    		foreach ($plantings as $plants) {
	    			$plantArray = explode('、', $plants['zongdi']);
	    			foreach($plantArray as $plant) {
	    				//echo Lease::getArea($value) .'-'. Lease::getArea($plant).'<br>';
		    			if(Lease::getZongdi($value) == Lease::getZongdi($plant)){		
		    				if(Lease::getArea($value) !== Lease::getArea($plant)){
		    					//echo Lease::getArea($value) .'-'. Lease::getArea($plant).'<br>';
		    					$areac = Lease::getArea($value) - Lease::getArea($plant);
		    					$v = Lease::getZongdi($value).'('.$areac.')';
		    					//var_dump($v);
		    					$zongdi[$v] = $v;
		    					//echo 'zongdi_l=';var_dump($zongdi);
		    				}
		    			} else {
		    				//var_dump($zongdiarr);
		    				$zongdi[$value] = $value;
		    				//var_dump($zongdi);
		    				//$zongdi = array_diff($zongdi,$zongdiarr);
		    			}
		    		}
	    		}
	    	}	
	    	//var_dump($zongdi);
	    	return $zongdi;
    	}
    	else {
    		foreach($zongdiarr as $key => $value) {
    			$zongdi[$value] = $value;
    		}
    		//var_dump($zongdi);
    		return $zongdi;
    	}
    }
    
    
    
    //对Plantingstructurecheck中获取的面积进程累加处理
    public function zongdiAreaSum($arrayArea) 
    {
    	//var_dump($arrayArea[0]['zongdi']);
    	
    	for($i=0;$i<count($arrayArea);$i++) {
    		for($j=$i+1;$j<count($arrayArea);$j++) {
    			if(isset($arrayArea[$j]['zongdi'])) {
	    			if(Lease::getZongdi($arrayArea[$i]['zongdi']) == Lease::getZongdi($arrayArea[$j]['zongdi'])) {
	    				$areaSum = $arrayArea[$i]['area']+$arrayArea[$j]['area'];
	    				//$arrayArea[$i]['zongdi'] = Lease::getZongdi($arrayArea[$i]['zongdi']).'('.$areaSum.')';
	    				$arrayArea[$i]['area'] = $areaSum;
	    				unset($arrayArea[$j]);
	    				sort($arrayArea);
	    				//var_dump($arrayArea);
	    				$arrayArea = self::zongdiAreaSum($arrayArea);
	    			}
    			}
    		}
    	}
    	return $arrayArea;
    }
    //已经使用投入品的面积
    public function actionPlantingstructurecheckgetarea($zongdi) 
    {
    	$area = Lease::getListArea($zongdi);
    	echo json_encode(['status'=>1,'area'=>$area]);
    }
    //获取作物面积
    public function actionGetplantarea($farms_id,$plant_id)
    {
    	$area = 0;
    	$planting = Plantingstructurecheck::find()->where(['farms_id'=>$farms_id,'plant_id'=>$plant_id])->all();
    	foreach ($planting as $value) {
    		$area += $value['area'];
    	}
    	echo json_encode(['status'=>1,'area'=>$area]);
    }

	public function actionPlantingstructurechecksame($planting_id)
	{
		$model = new Plantingstructurecheck();
		$load = Plantingstructure::findOne($planting_id);
		$model->plant_id = $load->plant_id;
		$model->area = $load->area;
		$model->goodseed_id = $load->goodseed_id;
		$model->zongdi = $load->zongdi;
		$model->farms_id = $load->farms_id;
		$model->lease_id = $load->lease_id;
		$model->management_area = $load->management_area;
		$model->plant_father = $load->plant_father;
		$model->create_at = time();
		$model->update_at = $model->create_at;
		$model->issame = 1;
		$model->save();
		$plantInput = Plantinputproduct::find()->where(['planting_id'=>$planting_id])->all();
		foreach ($plantInput as $value) {
			$plantinputModel = new Plantinputproductcheck();
			Logs::writeLogs('新增复核种植结构的关联投入品',$model);
			$plantinputModel->farms_id = $value['farms_id'];
			$plantinputModel->lessee_id = $value['lessee_id'];
        	$plantinputModel->planting_id = $model->id;
            $plantinputModel->father_id = $value['father_id'];
            $plantinputModel->son_id = $value['son_id'];
            $plantinputModel->inputproduct_id = $value['inputproduct_id'];
            $plantinputModel->pconsumption = $value['pconsumption'];
            $plantinputModel->zongdi = $value['zongdi'];
            $plantinputModel->plant_id = $value['plant_id'];
        	$plantinputModel->create_at = time();
        	$plantinputModel->update_at = $plantinputModel->create_at;
        	$plantinputModel->management_area = $value['management_area'];
			$plantinputModel->save();
		}
		$plantpesticides = Plantpesticides::find()->where(['planting_id'=>$planting_id])->all();
		foreach ($plantpesticides as $value) {
			$plantpesticidesModel = new Plantpesticidescheck();
			Logs::writeLogs('新增复核种植结构的关联农药',$model);
			$plantpesticidesModel->planting_id = $model->id;
            $plantpesticidesModel->farms_id = $value['farms_id'];
            $plantpesticidesModel->lessee_id = $value['lessee_id'];
        	$plantpesticidesModel->plant_id = $value['plant_id'];
            $plantpesticidesModel->pesticides_id = $value['pesticides_id'];
            $plantpesticidesModel->pconsumption = $value['pconsumption'];
        	$plantpesticidesModel->create_at = time();
        	$plantpesticidesModel->update_at = $plantpesticidesModel->create_at;
        	$plantpesticidesModel->management_area = $value['management_area'];
			$plantpesticidesModel->save();
		}
		return $this->redirect(['plantingstructurecheckindex', 'farms_id' => $load->farms_id]);

	}

    public function actionPlantingstructurecheckcreate($lease_id,$farms_id)
    {
//    	var_dump($lease_id);exit;
    	$model = new Plantingstructurecheck();
    
    	$farm = Farms::find()->where(['id'=>$farms_id])->one();
    	$noarea = Plantingstructurecheck::getNoArea($lease_id, $farms_id);
    	$overarea = Plantingstructurecheck::getLeaseArea($farms_id);
//     	var_dump($noarea);exit;
		$plantinputproductModel = new Plantinputproductcheck();
    	$plantpesticidesModel = new Plantpesticidescheck();
    	if ($model->load(Yii::$app->request->post())) {
    		
    		//$model->zongdi = Lease::getZongdi($model->zongdi);
    		$model->create_at = time();
    		$model->update_at = time();
    		$model->management_area = Farms::getFarmsAreaID($farms_id);
			$model->issame = 0;
			$model->contractarea = $farm['contractarea'];
			$model->year = User::getYear();
    		$model->save();
    		
    		$new = $model->attributes;
    		//var_dump($new);
    		Logs::writeLogs('为'.Lease::find()->where(['id'=>$lease_id])->one()['lessee'].'创建种植结构信息',$model);
    		
    		
    		//$plantinputproducts = Plantinputproduct::find()->where(['farms_id'=>$planting->farms_id,'lessee_id'=>$planting->lease_id,'plant_id'=>$planting->plant_id,'zongdi'=>$planting->zongdi])->all();
    		$parmembersInputproduct = Yii::$app->request->post('PlantInputproductPost');
    		//var_dump($parmembersInputproduct);
    		if ($parmembersInputproduct) {
    			//var_dump($parmembers);
    			for($i=1;$i<count($parmembersInputproduct['inputproduct_id']);$i++) {
					$plantinputproductModel = new Plantinputproductcheck();
    				$plantinputproductModel->farms_id = $model->farms_id;
    				$plantinputproductModel->lessee_id = $model->lease_id;
    				$plantinputproductModel->zongdi = $model->zongdi;
    				$plantinputproductModel->plant_id = $model->plant_id;
    				$plantinputproductModel->planting_id = $model->id;
    				$plantinputproductModel->father_id = $parmembersInputproduct['father_id'][$i];
    				$plantinputproductModel->son_id = $parmembersInputproduct['son_id'][$i];
    				$plantinputproductModel->inputproduct_id = $parmembersInputproduct['inputproduct_id'][$i];
    				$plantinputproductModel->pconsumption = $parmembersInputproduct['pconsumption'][$i];
    				$plantinputproductModel->create_at = time();
    				$plantinputproductModel->update_at = time();
    				$plantinputproductModel->save();
    				Logs::writeLogs('添加投入品',$plantinputproductModel);
    			}
    		}
    		$parmembersPesticides = Yii::$app->request->post('PlantpesticidesPost');
    		//var_dump($parmembersPesticides);
    		if($parmembersPesticides) {
    			for($i=1;$i<count($parmembersPesticides['pesticides_id']);$i++) {
    				$plantpesticidesModel = new Plantpesticidescheck();
    				$plantpesticidesModel->farms_id = $model->farms_id;
    				$plantpesticidesModel->lessee_id = $model->lease_id;
    				$plantpesticidesModel->plant_id = $model->plant_id;
    				$plantpesticidesModel->planting_id = $model->id;
    				$plantpesticidesModel->pesticides_id = $parmembersPesticides['pesticides_id'][$i];
    				$plantpesticidesModel->pconsumption = $parmembersPesticides['pconsumption'][$i];
    				$plantpesticidesModel->create_at = time();
    				$plantpesticidesModel->update_at = time();
    				$plantpesticidesModel->save();
    				Logs::writeLogs('添加投入品',$plantpesticidesModel);
    			}
    		}
    		
    		return $this->redirect(['plantingstructurecheckindex', 'farms_id' => $farms_id]);
    	} else {
    		return $this->render('Plantingstructurecheckcreate', [
    				'plantinputproductModel' => $plantinputproductModel,
    				'plantpesticidesModel' => $plantpesticidesModel,
    				'model' => $model,
    				'farm' => $farm,
    				'noarea' => $noarea,
    				'overarea' => $overarea,
    		]);
    	}
    }
    /**
     * Updates an existing Plantingstructurecheck model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionPlantingstructurecheckupdate($id,$lease_id,$farms_id)
    {
        $model = $this->findModel($id);
        $farm = Farms::find()->where(['id'=>$model->farms_id])->one();
    
        $area = Lease::getAllLeaseArea($lease_id, $farms_id);
        $overarea = Plantingstructurecheck::getOverArea($lease_id, $farms_id);
		if($overarea)
			$noarea = $area - $overarea;
		else 
			$noarea = $area;
        $plantings = Plantingstructurecheck::find()->where(['lease_id'=>$lease_id,'farms_id'=>$farms_id])->all();
        $plantinputproductModel = Plantinputproductcheck::find()->where(['planting_id' => $id])->all();
        $plantpesticidesModel = Plantpesticidescheck::find()->where(['planting_id'=>$id])->all();
        
        if ($model->load(Yii::$app->request->post())) {
        	$model->update_at = time();
        	$model->save();
        	Logs::writeLogs('更新租赁信息',$model);
//         	var_dump($model->farms_id);
//         	exit;
        	$parmembersInputproduct = Yii::$app->request->post('PlantInputproductPost');
        	$this->deletePlantinput($plantinputproductModel, $parmembersInputproduct['id']);
        	if ($parmembersInputproduct) {
        		//var_dump($parmembers);
        		for($i=1;$i<count($parmembersInputproduct['inputproduct_id']);$i++) {
        			$plantinputproductModel = Plantinputproductcheck::findOne($parmembersInputproduct['id'][$i]);
        			if(empty($plantinputproductModel))
        				$plantinputproductModel = new Plantinputproductcheck();
        			$plantinputproductModel->id = $parmembersInputproduct['id'][$i];
        			$plantinputproductModel->farms_id = $model->farms_id;
        			$plantinputproductModel->lessee_id = $model->lease_id;
        			$plantinputproductModel->zongdi = $model->zongdi;
        			$plantinputproductModel->plant_id = $model->plant_id;
        			$plantinputproductModel->planting_id = $model->id;
        			$plantinputproductModel->father_id = $parmembersInputproduct['father_id'][$i];
        			$plantinputproductModel->son_id = $parmembersInputproduct['son_id'][$i];
        			$plantinputproductModel->inputproduct_id = $parmembersInputproduct['inputproduct_id'][$i];
        			$plantinputproductModel->pconsumption = $parmembersInputproduct['pconsumption'][$i];
        			$plantinputproductModel->create_at = time();
        			$plantinputproductModel->update_at = time();
        			$plantinputproductModel->save();
        			Logs::writeLogs('添加投入品',$plantinputproductModel);
        		}
        	}
        	//exit;
        	$parmembersPesticides = Yii::$app->request->post('PlantpesticidesPost');
        	$this->deletePlantpesticides($plantpesticidesModel, $parmembersPesticides['id']);
        	if($parmembersPesticides) {
        		for($i=1;$i<count($parmembersPesticides['pesticides_id']);$i++) {
        			$plantpesticidesModel = Plantpesticidescheck::findOne($parmembersPesticides['id'][$i]);
        			if(empty($plantpesticidesModel))
        				$plantpesticidesModel = new Plantpesticidescheck();
        			$plantpesticidesModel->farms_id = $model->farms_id;
        			$plantpesticidesModel->lessee_id = $model->lease_id;
        			$plantpesticidesModel->plant_id = $model->plant_id;
        			$plantpesticidesModel->planting_id = $model->id;
        			$plantpesticidesModel->pesticides_id = $parmembersPesticides['pesticides_id'][$i];
        			$plantpesticidesModel->pconsumption = $parmembersPesticides['pconsumption'][$i];
        			$plantpesticidesModel->create_at = time();
        			$plantpesticidesModel->update_at = time();
        			$plantpesticidesModel->save();
        			Logs::writeLogs('添加投入品',$plantpesticidesModel);
        		}
        	}
            return $this->redirect(['plantingstructurecheckindex', 'farms_id' => $model->farms_id]);
        } else {
            return $this->render('plantingstructurecheckupdate', [
            	'plantinputproductModel' => $plantinputproductModel,
            	'plantpesticidesModel' => $plantpesticidesModel,
                'model' => $model,
            	'farm' => $farm,
            	'noarea' => $noarea + $model->area,
            	'overarea' =>$overarea,
            	//'leases' => $lease,
            ]);
        }
    }

    private function deletePlantinput($nowdatabase,$postdataidarr) {
    	$databaseid = array();
    	foreach($nowdatabase as $value) {
    		$databaseid[] = $value['id'];
    	}
    	$result = array_diff($databaseid,$postdataidarr);
    	if($result) {
    		foreach($result as $val) {
    			$model = Plantinputproduct::findOne($val);
    			Logs::writeLogs('删除投入品',$model);
    			$model->delete();
    		}
    		return true;
    	} else
    		return false;
    }
    
    private function deletePlantpesticides($nowdatabase,$postdataidarr) {
    	$databaseid = array();
    	foreach($nowdatabase as $value) {
    		$databaseid[] = $value['id'];
    	}
    	$result = array_diff($databaseid,$postdataidarr);
    	if($result) {
    		foreach($result as $val) {
    			$model = Plantpesticides::findOne($val);
    			Logs::writeLogs('删除投入品',$model);
    			$model->delete();
    		}
    		return true;
    	} else
    		return false;
    }
    
    /**
     * Deletes an existing Plantingstructurecheck model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionPlantingstructurecheckdelete($id)
    {
        $model = $this->findModel($id);
    	Logs::writeLogs('删除租赁信息',$model);
        $model->delete();
		$plantInput = Plantinputproductcheck::find()->where(['planting_id'=>$id])->all();
		foreach ($plantInput as $value) {
			$plantinputModel = Plantinputproductcheck::findOne($value['id']);
			Logs::writeLogs('删除种植结构的关联投入品',$model);
			$plantinputModel->delete();
		}
		$plantpesticides = Plantpesticidescheck::find()->where(['planting_id'=>$id])->all();
		foreach ($plantpesticides as $value) {
			$plantpesticidesModel = Plantpesticidescheck::findOne($value['id']);
			Logs::writeLogs('删除种植结构的关联农药',$model);
			$plantpesticidesModel->delete();
		}
        return $this->redirect(['plantingstructurecheckindex','farms_id'=>$model->farms_id]);
    }

    /**
     * Finds the Plantingstructurecheck model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plantingstructurecheck the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Plantingstructurecheck::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
