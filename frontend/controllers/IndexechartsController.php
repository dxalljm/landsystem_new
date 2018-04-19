<?php

namespace frontend\controllers;

use app\models\Breed;
use app\models\User;
use app\models\Yields;
use Yii;
use app\models\Inputproduct;
use frontend\models\inputproductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Logs;
use app\models\Farms;
use app\models\Inputproductbrandmodel;
use yii\helpers\ArrayHelper;
use app\models\Mainmenu;
use app\models\Cache;
use app\models\Indexecharts;
use app\models\Collection;
use app\models\Loan;
use app\models\Plantingstructurecheck;
use app\models\Insurance;
use app\models\Fireprevention;
use app\models\Huinong;
use app\models\Breedinfo;
use app\models\Huinonggrant;
use app\models\Projectapplication;
/**
 * InputproductController implements the CRUD actions for Inputproduct model.
 */
class IndexechartsController extends Controller
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
    public function actionRefresh($id,$address)
    {
        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
        switch ($plantarr[$id]) {
            case '宜农林地':
                $this->Farmscacheone();
                break;
            case '精准农业':
                $this->Plantingstructurecacheone();
                break;
            case '农产品':
                $this->Yieldscacheone();
                break;
            case '惠农政策':
                $this->Huinongcacheone();
                break;
            case '承包费收缴':
                $this->Collectioncacheone();
                break;
            case '防火工作':
                $this->Firecacheone();
                break;
            case '畜牧业':
                $this->Breedinfocacheone();
                break;
            case '项目申报':
                $this->Projectapplicationcacheone();
                break;
            case '保险业务':
                $this->Insurancecacheone();
                break;
            case '贷款':
                $this->Loancacheone();
                break;
        }
        echo json_encode(Indexecharts::showEcharts($id,$address));
    }

    //以下为单个用户
    public function Farmscacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one()) {
            $landcache = Cache::findOne($cache->id);
        } else {
            $landcache = new Cache();
        }
        $landcache->user_id = Yii::$app->user->id;
        $landcache->farmscache = Farms::getFarmarea();
        $landcache->farmstitle = '面积：'.Farms::totalArea().' 农场户数：'.Farms::totalNum();
        $landcache->farmscategories = json_encode(Farms::getUserManagementAreaname(Yii::$app->user->id));
        $landcache->year = User::getYear();
        $landcache->farmstime = time();
        $landcache->save();
//        var_dump($landcache->getErrors());
//        echo '宜林农地首页图表更新完成！';
//        return $result;
    }

    public function Firecacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
// 			var_dump(Fireprevention::getBfblist($id));
        $landcache->firecache = Fireprevention::getBfblist();
// 			var_dump($landcache->firecache);
        $landcache->firetitle = '防火完成度：'.Fireprevention::getAllbfb();
        $landcache->firecategories = json_encode(Farms::getUserManagementAreaname(Yii::$app->user->id));
        $landcache->year = User::getYear();
        $landcache->firetime = time();
        $landcache->save();
//        var_dump($landcache->getErrors());
    }

    public function Breedinfocacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
//        var_dump(Collection::getCollection(Yii::$app->user->id));
        $landcache->breedinfocache = Breedinfo::getBreedinfoCache('data');
        $landcache->breedinfotitle = '畜牧养殖';
        $landcache->breedinfocategories = Breedinfo::getBreedinfoCache('typename');
        $landcache->breedinfodw = Breedinfo::getBreedinfoCache('dw');
        $landcache->year = User::getYear();
        $landcache->breedinfotime = time();
//        var_dump($landcache);
        $landcache->save();
//        var_dump($landcache->getErrors());
    }

    public function Collectioncacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
//        var_dump($landcache);
        $landcache->user_id = Yii::$app->user->id;
//        var_dump(Collection::getCollection(Yii::$app->user->id));
        $landcache->collectioncache = Collection::getCollection();
        $landcache->collectiontitle = '应收：'. Collection::totalAmounts().' 实收：'.Collection::totalReal().' 完成百分比:'.Collection::totalBfb();
        $landcache->collectioncategories = json_encode(Farms::getUserManagementAreaname(Yii::$app->user->id));
        $landcache->year = User::getYear();
        $landcache->collectiontime = time();
        $landcache->save();
//        var_dump($landcache->getErrors());
//        var_dump($landcache);
    }
    public function Plantingstructurecacheone($user_id=null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->plantingstructurecache = Plantingstructurecheck::getPlantingstructure();
        $landcache->plantingstructuretitle = '作物面积：'.Plantingstructurecheck::getPlantGoodseedSum()['plantSum'].'亩&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;良种面积：'.Plantingstructurecheck::getPlantGoodseedSum()['goodseedSum'].'亩';
        $plant = Plantingstructurecheck::getUserPlantname();
        $landcache->plantingstructurecategories = json_encode($plant['plantname']);
        $landcache->year = User::getYear();
        $landcache->plantingstructuretime = time();
        $landcache->save();
    }

    public function Huinongcacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->huinongcache = Huinonggrant::getHuinonggrantinfo();
        $landcache->huinongtitle = '惠农政策补贴发放情况';
        $landcache->huinongcategories = json_encode(Farms::getUserManagementAreaname(Yii::$app->user->id));
        $landcache->year = User::getYear();
        $landcache->huinongtime = time();
        $landcache->save();
//        var_dump($landcache);
    }

//    public function Plantinputproductcacheone()
//    {
//        if($cache = Cache::find()->where(['user_id'=>Yii::$app->user->id,'year'=>User::getYear()])->one())
//            $landcache = Cache::findOne($cache->id);
//        else
//            $landcache = new Cache();
//        $landcache->user_id = Yii::$app->user->id;
//        $landcache->plantinputproductcache = Yields::getYieldsCache();
//        $landcache->plantinputproducttitle = '投入品使用情况';
//        $landcache->plantinputproductcategories = json_encode(Plantinputproduct::getTypenamelist(Yii::$app->user->id)['typename']);
//        $landcache->year = User::getYear();
//        $landcache->plantinputproducttime = time();
//        $landcache->save();
//    }

    public function Yieldscacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->plantinputproductcache = Yields::getYieldsCache();
        $landcache->plantinputproducttitle = '农产品产量情况';
        $landcache->plantinputproductcategories = json_encode(Yields::getTypename()['typename']);
        $landcache->year = User::getYear();
        $landcache->plantinputproducttime = time();
        $landcache->save();
//        var_dump($landcache);
    }

    public function Projectapplicationcacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->projectapplicationcache = Projectapplication::getProjectapplicationcache();
        $landcache->projectapplicationtitle = '项目情况';
        $landcache->projectapplicationcategories = json_encode(Projectapplication::getTypename()['projecttype']);
        $landcache->projectapplicationdw = json_encode(Projectapplication::getTypename()['unit']);
        $landcache->year = User::getYear();
        $landcache->projectapplicationtime = time();
        $landcache->save();
    }
    public function Insurancecacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->insurancecache = Insurance::getInsurancecache();
        $landcache->insurancetitle = '保险业务';
        $landcache->insurancecategories = json_encode(Farms::getUserManagementAreaname(Yii::$app->user->id));
        $landcache->insurancetime = time();
        $landcache->year = User::getYear();
        $landcache->save();
//        var_dump($landcache);
    }

    public function Loancacheone($user_id = null)
    {
        if(empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        if($cache = Cache::find()->where(['user_id'=>$user_id,'year'=>User::getYear()])->one())
            $landcache = Cache::findOne($cache->id);
        else
            $landcache = new Cache();
        $landcache->user_id = Yii::$app->user->id;
        $landcache->loancache = Loan::getLoancache(Yii::$app->user->id);
        $landcache->loantitle = '贷款金额：'.Loan::getLoanMoney(Yii::$app->user->id).'万元';
        $landcache->loancategories = json_encode(Loan::getBankList('small'));
        $landcache->year = User::getYear();
        $landcache->loantime = time();
        $landcache->save();
//        var_dump($landcache);
//        var_dump($landcache->getErrors());
    }
    public function Newyear()
    {
        $oldyear = Theyear::getYear();
        if(Theyear::getYear() !== User::getYear()) {
            Theyear::setYear(User::getYear());
            User::setAllUserYear(User::getYear());
            $collection = Collection::find()->all();
            foreach ($collection as $coll) {
                $model = Collection::findOne($coll['id']);
                if ($model->state == 0 and $model->payyear == $oldyear) {
                    $model->owe = $model->ypaymoney;
// 					$model->update_at = time();
                    $model->ypayyear = $oldyear;
                }

                $model->save();
            }
        }
    }
}
