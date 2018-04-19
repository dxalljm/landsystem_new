<?php

namespace frontend\controllers;

use app\models\Huinong;
use app\models\Huinonggrant;
use app\models\Subsidyratio;
use app\models\Subsidytypetofarm;
use app\models\User;
use Yii;
use app\models\Lease;
use frontend\models\leaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Farmer;
use app\models\Farms;
use app\models\Theyear;
use app\models\Logs;
use app\models\Plantingstructure;
use app\models\Insurance;
use app\models\Plantingstructurecheck;
use app\models\Plant;
use app\models\Insurancetype;
use app\models\Insuranceplan;
use frontend\helpers\Pinyin;
use app\models\Plantingstructureyearfarmsidplan;
/**
 * LeaseController implements the CRUD actions for Lease model.
 */
class LeaseController extends Controller
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
    /**
     * Lists all Lease models.
     * @return mixed
     */
    public function actionLeaseindex($farms_id)
    {
    	//$farmerarea = Plantingstructure::find()->where(['farms_id'=>$farms_id,'lease_id'=>0])->one()['area'];
         $searchModel = new leaseSearch();
         $params = Yii::$app->request->queryParams;
         $params['leaseSearch']['farms_id'] = $farms_id;
         $dataProvider = $searchModel->search($params);
         $farm = Farms::find()->where(['id'=>$farms_id])->one();
         $measure = $farm['contractarea'];
         $plantingArea = Plantingstructure::getArea($farms_id);
         $overarea = Lease::getAllLeaseArea($farms_id);
// 		var_dump($measure);var_dump($plantingArea);exit;
		Logs::writeLog(User::getYear().'年度'.Farms::find()->where(['id'=>$farms_id])->one()['farmname'].'租赁信息',$farms_id);
        return $this->render('leaseindex', [
             'searchModel' => $searchModel,
             'dataProvider' => $dataProvider,
        	 'areas' => Lease::getNoArea($farms_id),
             'overarea' => $overarea,
        	 'plantingArea' => $plantingArea,
             'farm' => $farm,
        ]);
    }
	
    public function actionLeasearea()
    {
    	$leases = Lease::find()->all();
    	foreach ($leases as $lease) {
    		$model = Lease::findOne($lease['id']);
    		$arrayLeaseArea = explode('、', $model->lease_area);
    		$area = 0.0;
    		foreach ($arrayLeaseArea as $value) {
    			$area += Lease::getArea($value);
    		}
    		$model->lease_area = (string)$area;
    		$model->save();
//    		var_dump($model->getErrors());
    	}
    	echo 'finished';
    }

    public function actionGetlessee($lease_id)
    {
        $lease = Lease::findOne($lease_id);
        $area = 0;
        $insurancetype = Insurancetype::find()->all();
        foreach ($insurancetype as $value) {
            $$value['pinyin'] = 0;
        }
        foreach ($insurancetype as $value) {
            $$value['pinyin'] = Plantingstructure::find()->where(['plant_id'=>$value['plant_id'],'farms_id'=>$lease->farms_id,'lease_id'=>$lease_id,'year'=>User::getYear()])->one()['area'];
            $area += $$value['pinyin'];
            $plantArea[$value['pinyin']] = ['name'=>Plant::find()->where(['id'=>$value['plant_id']])->one()['typename'],'value'=>$$value['pinyin'],'pinyin'=>$value['pinyin']];
        }
        $allArea = Plantingstructure::find()->where(['lease_id'=>$lease_id,'year'=>User::getYear()])->sum('area');
        $otherArea = abs(sprintf("%.2f",$allArea - $area));
        $plantArea['other'] = ['name'=>'其他','value'=>$otherArea,'pinyin'=>'other'];
        $insuredarea = sprintf("%.2f",$area);
        echo json_encode(['lessee'=>$lease->lessee,'cardid'=>$lease->lessee_cardid,'telephone'=>$lease->lessee_telephone,'plantArea'=>$plantArea,'insuredarea'=>$insuredarea]);
    }

    public function actionLeaseallview($farms_id)
    {
    	$lease = Lease::find()->where(['farms_id'=>$farms_id])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
    	$farm = Farms::find()->where(['id'=>$farms_id])->one();
        Logs::writeLog(User::getYear().'年度'.$farm['farmname'].'租赁情况明细',$farms_id);
    	return $this->render('leaseallview', [
    			'leases'=>$lease,
    			'farm' => $farm,
    	]);
    }
    
    /**
     * Displays a single Lease model.
     * @param integer $id
     * @return mixed
     */
    public function actionLeaseview($id)
    {
    	$model = $this->findModel($id);
    	$farm = Farms::find()->where(['id'=>$model->farms_id])->one();
    	$farmer = Farmer::find()->where(['farms_id'=>$model->farms_id])->one();
    	Logs::writeLog('查看租赁信息',$id);
        return $this->render('leaseview', [
            'model' => $model,
        	'farm' => $farm,
        	'farmer' => $farmer,
        	'noarea' => Lease::getNoArea($model->farms_id),
        ]);
    }
    
    public function actionGetoverarea()
    {
    	$lease = new Lease();
    	$area = $lease::getOverArea();
    	echo Json::encode($area);
    }
    public function actionGetarea($zongdiarea)
    {
    	$zongdi = Lease::getZongdi($zongdiarea);
    	$area = Lease::getArea($zongdiarea);
//    		$oldarea = Parcel::find()->where(['unifiedserialnumber'=>$zongdi])->one()['grossarea'];
    	echo json_encode(['area'=>$area,'zongdi'=>$zongdi]);
    }
    /**
     * Creates a new Lease model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionLeasecreate($farms_id)
    {
    	//$this->layout='@app/views/layouts/nomain.php';
    	$farm = Farms::find()->where(['id'=>$farms_id])->one();
    	$farmer = Farmer::find()->where(['farms_id'=>$farms_id])->one();
        $model = new lease();

		$overarea = Lease::scanOverArea($farms_id);
		$noarea = $model::getNoArea($farms_id);
        if ($model->load(Yii::$app->request->post())) {
        	$model->farms_id = $farms_id;
        	$model->create_at = time();
        	$model->update_at = $model->create_at;
            $model->year = User::getYear();
            if($model->renttype) {
            	$model->renttype = implode(',', $model->renttype);
            }
            $model->renttime = (string)strtotime($model->renttime);
            $state = $model->save();
            if($_POST['Lease']['isinsurance']) {
                $planModel = new Insuranceplan();
                $planModel->management_area = $farm['management_area'];
                $planModel->year = User::getYear();
                $planModel->farms_id = $farm['id'];
                $planModel->policyholder = $model->lessee;
                $planModel->cardid = $model->lessee_cardid;
                $planModel->telephone = $model->lessee_telephone;
                $planModel->insuredarea = $model->lease_area;
                $planModel->create_at = time();
                $planModel->update_at = $planModel->create_at;
                $planModel->farmername = $farm['farmername'];
                $planModel->contractarea = $farm['contractarea'];
                $planModel->farmerpinyin = $farm['farmerpinyin'];
                $planModel->policyholderpinyin = Pinyin::encode($model->lessee);
                $planModel->farmstate = $farm['state'];
                $planModel->lease_id = $model->id;

                $insuranceModel = new Insurance();
                $insuranceModel->management_area = $farm['management_area'];
                $insuranceModel->year = User::getYear();
                $insuranceModel->farms_id = $farm['id'];
                $insuranceModel->policyholder = $model->lessee;
                $insuranceModel->cardid = $model->lessee_cardid;
                $insuranceModel->telephone = $model->lessee_telephone;
                $insuranceModel->insuredarea = $model->lease_area;
                $insuranceModel->create_at = time();
                $insuranceModel->update_at = $insuranceModel->create_at;
                $insuranceModel->farmername = $farm['farmername'];
                $insuranceModel->contractarea = $farm['contractarea'];
                $insuranceModel->farmerpinyin = $farm['farmerpinyin'];
                $insuranceModel->policyholderpinyin = Pinyin::encode($model->lessee);
                $insuranceModel->farmstate = $farm['state'];
                $insuranceModel->lease_id = $model->id;
                $insuranceModel->save();
                Logs::writeLogs('新增保险任务', $insuranceModel);
                $planModel->insurance_id = $insuranceModel->id;
                $state = $planModel->save();
                Logs::writeLogs('新增计划保险',$planModel);
                $plantings = Plantingstructure::find()->where(['lease_id'=>$model->id,'year'=>User::getYear()])->all();
                if($plantings) {
                    foreach ($plantings as $planting) {
                        $plantModel = Plantingstructure::findOne($planting['id']);
                        $plantModel->isinsurance = 1;
                        $plantModel->save();
                        Logs::writeLogs('更新种植结构为不参加保险', $plantModel);
                    }
                }
            } else {
                $planModel = Insuranceplan::find()->where(['farms_id'=>$farms_id,'lease_id'=>$model->id,'year'=>User::getYear()])->one();
                $insurance = Insurance::find()->where(['farms_id'=>$farms_id,'lease_id'=>$model->id,'year'=>User::getYear()])->one();
                $plantings = Plantingstructure::find()->where(['lease_id'=>$model->id,'year'=>User::getYear()])->all();
                if($plantings) {
                    foreach ($plantings as $planting) {
                        $plantModel = Plantingstructure::findOne($planting['id']);
                        $plantModel->isinsurance = 0;
                        $plantModel->save();
                        Logs::writeLogs('更新种植结构为不参加保险', $plantModel);
                    }
                }
                Logs::writeLogs('删除计划保险',$planModel);
                $planModel->delete();
                Logs::writeLogs('删除保险任务',$insurance);
                $insurance->delete();
            }
        	if($state) {
                $subsidyratio = Yii::$app->request->post('Subsidyratio');
                foreach ($subsidyratio as $key => $value) {
                    $arr = explode('-', $key);
                    $typeid = Subsidytypetofarm::find()->where(['mark' => $arr[0]])->one()['id'];
                    $sub = Subsidyratio::find()->where(['farms_id'=>$farms_id,'lease_id'=>$model->id,'typeid'=>$typeid])->one();
                    if($sub) {
                        $subModel = Subsidyratio::findOne($sub['id']);
                    } else {
                        $subModel = new Subsidyratio();
                    }
                    $subModel->farms_id = $farms_id;
                    $subModel->typeid = $typeid;
                    $subModel->lease_id = $model->id;
                    $subModel->$arr[1] = $value;
                    $subModel->save();
                    Logs::writeLogs('创建补贴比率分配',$subModel);
                }
            }
        	$new = $model->attributes;
        	Logs::writeLogs('创建租赁信息',$model);
//         	var_dump($model->getErrors());exit;
            return $this->redirect(['leaseindex', 'farms_id' => $farms_id]);
        } else {
            return $this->render('leasecreate', [
                'model' => $model,
            	'overarea' => $overarea,
            	'noarea' => $noarea,
            	'farm' => $farm,
            	'farmer' => $farmer,
                'isinsurance' => false,
            ]);
        }
    }

    public function actionLeasecreateajax($farms_id)
    {
        //$this->layout='@app/views/layouts/nomain.php';
        $farm = Farms::find()->where(['id'=>$farms_id])->one();
        $farmer = Farmer::find()->where(['farms_id'=>$farms_id])->one();
        $model = new lease();
        $overarea = Lease::scanOverArea($farms_id);
        $noarea = $model::getNoArea($farms_id);
        $farmerArea = Plantingstructure::find()->where(['farms_id'=>$farms_id,'lease_id'=>0,'year'=>User::getYear()])->sum('area');
//        if(bccomp($farm['contractarea'],$noarea) == 1) {
//            $noarea = bcsub($noarea, $sum,2);
////            $noarea = bcsub($farm['contractarea'],$newarea,2);
//        } else {
//            if($sum) {
//                $noarea = bcsub($farm['contractarea'],$sum,2);
//                $overarea = $sum;
//            }
//        }
//        var_dump($noarea);exit;
//        if ($model->load(Yii::$app->request->post())) {
//            var_dump($_POST);exit;
//            $model->farms_id = $farms_id;
//            $model->create_at = (string)time();
//            $model->update_at = $model->create_at;
//            $model->year = User::getYear();
//            if($model->renttype) {
//                $model->renttype = implode(',', $model->renttype);
//            }
//            $model->renttime = (string)strtotime($model->renttime);
////            var_dump($model);exit;
////            $save = $model->save();
//            $save = true;
//            if($save) {
//                $modelRatio = new Subsidyratio();
//                $modelRatio->farms_id = $farms_id;
////                $modelRatio
//            }
//            $new = $model->attributes;
//            Logs::writeLogs('创建租赁信息',$model);
////         	var_dump($model->getErrors());exit;
//            return $this->redirect(['leaseindex', 'farms_id' => $farms_id]);
//        } else {
        return $this->renderAjax('leasecreateajax', [
            'model' => $model,
            'overarea' => $overarea,
            'noarea' => $noarea,
            'farm' => $farm,
            'farmer' => $farmer,
            'farmerArea' => $farmerArea == ''?0:$farmerArea,
        ]);

    }

    /**
     * Updates an existing Lease model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionLeaseupdate($id,$farms_id)
    {
        $model = $this->findModel($id);
        $farm = Farms::find()->where(['id'=>$model->farms_id])->one();
        $farmer = Farmer::find()->where(['farms_id'=>$model->farms_id])->one();
        $overarea = Lease::getAllLeaseArea($farms_id);
		$noarea = Lease::getNoArea($farms_id);
        $plan = Insuranceplan::find()->where(['farms_id'=>$farms_id,'lease_id'=>$id,'year'=>User::getYear()])->count();
        if($plan) {
            $isinsurance = true;
        } else {
            $isinsurance = false;
        }
        if ($model->load(Yii::$app->request->post())) {
        	$model->update_at = time();
        	if($model->renttype) {
            	$model->renttype = implode(',', $model->renttype);
            }
            $model->renttime = (string)strtotime($model->renttime);
        	$model->save();
            $huinonggrant = Huinonggrant::find()->where(['farms_id'=>$farms_id])->all();
            foreach ($huinonggrant as $value) {
                $huinonggrantModel = Huinonggrant::findOne($value['id']);
                $huinong = Huinong::find()->where(['id'=>$value['huinong_id']])->one();
                if($huinonggrantModel->subsidyobject == $farm['farmername']) {
                    $huinonggrantModel->proportion = $model->farmerzb;
                    $huinonggrantModel->money = $huinong['subsidiesarea'] * 0.01 * $value['area'] * $huinong['subsidiesmoney'] * (float)$model->farmerzb/100;
                    $huinonggrantModel->area = $value['area'] * (float)$model->farmerzb/100;
                }
                if($huinonggrantModel->subsidyobject == $model['lessee']) {
                    $huinonggrantModel->proportion = $model->lesseezb;
                    $huinonggrantModel->money = $huinong['subsidiesarea'] * 0.01 * $value['area'] * $huinong['subsidiesmoney'] * (float)$model->lesseezb/100;
                    $huinonggrantModel->area = $value['area'] * (float)$model->lesseezb/100;
                }
                $huinonggrantModel->save();
                Logs::writeLogs('更新惠农信息',$huinonggrantModel);
            }
        	Logs::writeLogs('更新租赁信息',$model);
            return $this->redirect(['leaseindex', 'farms_id'=>$model->farms_id]);
        } else {
            return $this->render('leaseupdate', [
                'model' => $model,
            	'farm' => $farm,
            	'farmer' => $farmer,
            	'overarea' => $overarea,
            	'noarea' => $noarea,
                'isinsurance' => $isinsurance,
            ]);
        }
    }

    public function actionLeaseupdateajax($id,$farms_id)
    {
        $model = $this->findModel($id);
        $old = $model->attributes;
        $farm = Farms::find()->where(['id'=>$model->farms_id])->one();
        $farmer = Farmer::find()->where(['farms_id'=>$model->farms_id])->one();
        $overarea = Lease::getAllLeaseArea($farms_id);
        $noarea = Lease::getNoArea($farms_id);
        $ratio = [];
        foreach (Subsidytypetofarm::find()->all() as $value) {
            $ratio[$value['mark']] = Subsidyratio::find()->where(['farms_id' => $farms_id,'typeid'=>$value['id'],'lease_id'=>$id])->one();
        }
        $plan = Insuranceplan::find()->where(['lease_id'=>$id,'year'=>User::getYear()])->count();
//        var_dump($ratio);exit;
        return $this->renderAjax('leaseupdateajax', [
            'model' => $model,
            'farm' => $farm,
            'farmer' => $farmer,
            'overarea' => $overarea,
            'noarea' => $noarea,
            'ratio' => $ratio,
            'isinsurance' => $plan,
        ]);
    }

    /**
     * Deletes an existing Lease model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionLeasedelete($id)
    {
    	$lease = $this->findModel($id);
        $farms_id = $lease->farms_id;
    	$planting = Plantingstructure::find()->where(['lease_id'=>$id])->all();
    	if($planting) {
    		foreach ($planting as $plant) {
    			$plantingModel = Plantingstructure::findOne($plant['id']);
    			$plantingModel->delete();
                Logs::writeLogs('删除种植结构信息',$plantingModel);
    		}
    	}
        $rows = Plantingstructure::find()->where(['farms_id'=>$farms_id,'year'=>User::getYear()])->all();
        if(empty($rows)) {
            Plantingstructureyearfarmsidplan::newPlan($farms_id, 0);
        }
    	$insurance = Insurance::find()->where(['farms_id'=>$lease->farms_id,'lease_id'=>$lease->id,'year'=>User::getYear()])->one();
    	if($insurance) {
    		$insuranceModel = Insurance::findOne($insurance['id']);
    		$insuranceModel->delete();
            Logs::writeLogs('删除保险信息',$insuranceModel);
    	}
        $insuranceplan = Insuranceplan::find()->where(['farms_id'=>$lease->farms_id,'lease_id'=>$lease->id,'year'=>User::getYear()])->one();
        if($insuranceplan) {
            $insuranceplan->delete();
            Logs::writeLogs('删除计划保险信息',$insuranceplan);
        }
        $sub = Subsidyratio::find()->where(['farms_id'=>$lease->farms_id,'lease_id'=>$id])->all();
        foreach ($sub as $value) {
            $subModel = Subsidyratio::findOne($value['id']);
            Logs::writeLogs('删除补贴比率分配',$subModel);
            $subModel->delete();
        }
        $lease->delete();
		Logs::writeLogs('删除租赁信息',$lease);
        return $this->redirect(['leaseindex','farms_id'=>$lease['farms_id']]);
    }

    public function actionLeasedeleteajax($id)
    {
        $lease = $this->findModel($id);
        $farms_id = $lease->farms_id;
        $s = Subsidyratio::find()->where(['lease_id'=>$id])->all();
        foreach ($s as $v) {
            $sModel = Subsidyratio::findOne($v['id']);
            $sModel->delete();
            Logs::writeLogs('删除惠农比率分配信息',$sModel);
        }
        $planting = Plantingstructure::find()->where(['lease_id'=>$id])->all();
        if($planting) {
            foreach ($planting as $plant) {
                $plantingModel = Plantingstructure::findOne($plant['id']);
                $plantingModel->delete();
                Logs::writeLogs('删除种植结构信息',$plantingModel);
            }
        }
        $rows = Plantingstructure::find()->where(['farms_id'=>$farms_id,'year'=>User::getYear()])->all();
        if(empty($rows)) {
            Plantingstructureyearfarmsidplan::newPlan($farms_id, 0);
        }
        $insurance = Insuranceplan::find()->where(['farms_id'=>$lease->farms_id,'policyholder'=>$lease->lessee])->one();
        if($insurance) {
            $insuranceplanModel = Insuranceplan::findOne($insurance['id']);
            $insuranceModel = Insurance::findOne($insuranceplanModel->insurance_id);
            $insuranceModel->delete();
            $insuranceplanModel->delete();
            Logs::writeLogs('删除保险信息',$insuranceModel);
        }
        $lease->delete();
        Logs::writeLogs('删除租赁信息',$lease);
        return $this->redirect(['sixcheck/sixcheckindex','farms_id'=>$lease['farms_id']]);
    }

    /**
     * Finds the Lease model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lease the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lease::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function getAreas($id) {
    	$areas = 0;
    	if(($model = Lease::find()->where(['farms_id'=>$id])->all()) !== null) {
    		foreach($model as $val) {
    			$areas+=$val['lease_area'];
    		}
    	}
    	return $areas;
    }
}
