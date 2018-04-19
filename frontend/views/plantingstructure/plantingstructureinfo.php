<?php
namespace frontend\controllers;
use app\models\Goodseed;
use app\models\Lease;
use app\models\Mainmenu;
use app\models\Plant;
use app\models\Plantingstructure;
use app\models\Tables;
use frontend\helpers\ES;
use yii\helpers\Html;
use yii;
use app\models\ManagementArea;
use app\models\Farms;
use app\models\Theyear;
use app\models\Dispute;
use app\models\Machineoffarm;
use app\models\User;
use app\models\Farmer;
use yii\helpers\Url;
use frontend\helpers\whereHandle;
use frontend\helpers\arraySearch;
use frontend\helpers\grid\GridView;
use app\models\Plantingstructurecheck;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\farmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
//	$totalData = clone $dataProvider;
//	$totalData->pagination = ['pagesize'=>0];
// 	var_dump($totalData->getModels());exit;
//	$data = arraySearch::find($totalData)->search();
$arrclass = explode('\\',$dataProvider->query->modelClass);
$arrclass2 = explode('\\',$dataProvider2->query->modelClass);
$arrclass2plan = explode('\\',$dataProvider2plan->query->modelClass);
$huinongArray = [];
if(isset($_GET['plantingstructurecheckSearch']['plant_id'])) {
    if($_GET['plantingstructurecheckSearch']['plant_id'])
        $huinongArray = ['farmer'=>'法人:100%','lessee'=>'种植者:100%','f_l'=>'按比例分配'];
}
?>

<div class="plant-index">

    <?php  //echo $this->render('farms_search', ['model' => $searchModel]); ?>
    <?= Html::hiddenInput('tempFarmsid','',['id'=>'farmsid'])?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <?php User::tableBegin('精准农业统计表');?>
                        <ul class="nav nav-pills nav-pills-warning">
                            <li class="active" id="pinfo"><a href="#plantingstructureinfo" data-toggle="tab" aria-expanded="true" onclick="tabclick('pinfo',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">农作物调查数据统计</a></li>
                            <li class="" id="pcinfo"><a href="#plantingstructurecheckinfo" data-toggle="tab" aria-expanded="false" onclick="tabclick('pcinfo',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">农作物复核数据统计</a></li>
                            <li class="" id="farmplan"><a href="#plantinfoplan" data-toggle="tab" aria-expanded="false" onclick="tabclick('farmplan',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">按农场统计(调查数据)</a></li>
                            <li class="" id="farm"><a href="#plantinfo" data-toggle="tab" aria-expanded="false" onclick="tabclick('farm',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">按农场统计(复合数据)</a></li>
                            <li class="" id="tbzz"><a href="#plantingstructurezhu" data-toggle="tab" aria-expanded="false" onclick="tabclick('tbzz',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">柱状图表</a></li>
                            <li class="" id="tbbz"><a href="#plantingstructurebing" data-toggle="tab" aria-expanded="false" onclick="tabclick('tbbz',['pinfo':'plantingstructureinfo','pcinfo':'plantingstructurecheckinfo','farmplan':'plantinfoplan','farm':'plantinfo','tbzz':'plantingstructurezhu','tbbz':'plantingstructurebing'])">饼状图表</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class='tab-pane active' id="plantingstructureinfo">
                                <?php
                                $totalData = clone $dataProvider;
                                $totalData->pagination = ['pagesize'=>0];
                                $data = arraySearch::find($totalData)->search();
                                $plantarrclass = explode('\\',$dataProvider->query->modelClass);
                                if(isset($_GET['plantingstructureSearch']['plant_id'])) {
                                    $goodseedNumber = count(Goodseed::getPlantGoodseed($_GET['plantingstructureSearch']['plant_id']));
                                    $goodseedArray = Goodseed::getPlantGoodseed($_GET['plantingstructureSearch']['plant_id']);
                                } else {
                                    $goodseedNumber = 0;
                                    $goodseedArray = [];
                                }
                                ?>
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'filterModel' => $searchModel,
                                    'total' => '<tr height="40">
                                        <td></td>	
                                        <td align="left" id="pt0"><strong>合计</strong></td>
                                        <td align="left" id="pt1"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pt2"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pt3"></td>
                                        <td align="left" id="pt4"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pt5"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pt6"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pt7"><strong>'.count(Plant::getAllname()).'种</strong></td>
                                        <td align="left" id="pt8"><strong></strong></td>
                                        <td align="left" id="pt9"><strong></strong></td>
                                    </tr>',
                                    'columns' =>[
                                        ['class' => 'yii\grid\SerialColumn'],
                                        [
                                            'label'=>'管理区',
                                            'attribute'=>'management_area',
                                            'headerOptions' => ['width' => '130'],
                                            'value'=> function($model) {
// 				            	var_dump($model);exit;
                                                return ManagementArea::getAreanameOne($model->management_area);
                                            },
                                            'filter' => ManagementArea::getAreaname(),
                                        ],
                                        [
                                            'label' => '农场名称',
                                            'attribute' => 'farms_id',
                                            'options' =>['width'=>120],
                                            'value' => function ($model) {

                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['farmname'];

                                            }
                                        ],
                                        [
                                            'label' => '法人名称',
                                            'attribute' => 'farmer_id',
                                            'options' =>['width'=>120],
                                            'value' => function ($model) {

                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['farmername'];

                                            }
                                        ],
                                        [
                                            'label' => '合同号',
                                            'attribute' => 'state',
                                            'value' => function($model) {
                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['contractnumber'];
                                            },
                                            'filter' => [1=>'正常',2=>'未更换合同',3=>'临时性管理',4=>'买断合同'],
                                        ],
                                        [
                                            'label' => '合同面积',
                                            'attribute' => 'contractarea',
                                            'value' => function($model) {
                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['contractarea'];
                                            }
                                        ],
                                        'area',
                                        [
                                            'label' => '种植者',
                                            'attribute' => 'lease_id',
                                            'value' => function($model) {
                                                $lessee =  \app\models\Lease::find()->where(['id'=>$model->lease_id])->one()['lessee'];
                                                if($lessee) {
                                                    return $lessee;
                                                } else {
                                                    return Farms::find ()->where ( [
                                                        'id' => $model->farms_id
                                                    ] )->one ()['farmername'];
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '种植结构',
                                            'attribute' => 'plant_id',
                                            'value' => function($model) {
                                                return Plant::find()->where(['id'=>$model->plant_id])->one()['typename'];
                                            },
                                            'filter' => Plantingstructure::getPlantname($totalData),
                                        ],
                                        [
                                            'label' => '良种',
                                            'attribute' => 'goodseed_id',
                                            'value' => function($model) {
                                                return Goodseed::find()->where(['id'=>$model->goodseed_id])->one()['typename'];
                                            },
                                            'filter' => Plantingstructure::getAllname($totalData),
                                        ],
                                        [
                                            'label' => '补贴归属',
//                                                'attribute' => 'huinong',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['id'=>$model->plant_id])->one();
                                                if($model->lease_id == 0) {
                                                    if ($plant['typename'] == '大豆') {
                                                        return '<span class="text-green">法人:100%</span>';
                                                    }
                                                    if($plant['typename'] == '玉米') {
                                                        return '<span class="text-blue">种植者:100%</span>';
                                                    }
                                                } else {
                                                    $lease = Lease::find()->where(['id'=>$model->lease_id])->one();
                                                    if ($plant['typename'] == '大豆') {
                                                        if ($lease['ddcj_farmer'] == '100%') {
                                                            return '<span class="text-green">法人:100%</span>';
                                                        }
                                                        if ($lease['ddcj_lessee'] == '100%') {
                                                            return '<span class="text-blue">种植者:100%</span>';
                                                        }
                                                        return '<span class="text-red">法人:' . $lease['ddcj_farmer'] . ' 种植者:' . $lease['ddcj_lessee'].'</span>';
                                                    }
                                                    if ($plant['typename'] == '玉米') {
                                                        if ($lease['ymcj_farmer'] == '100%') {
                                                            return '<span class="text-green">法人:100%</span>';
                                                        }
                                                        if ($lease['ymcj_lessee'] == '100%') {
                                                            return '<span class="text-blue">种植者:100%</span>';
                                                        }
                                                        return '<span class="text-red">法人:' . $lease['ymcj_farmer'] . ' 种植者:' . $lease['ymcj_lessee'].'</span>';
                                                    }
                                                }
                                                return '';
                                            },
//                                                'filter' => $huinongArray,
                                        ],

                                        [
                                            'label' => '筛选',
                                            'attribute' => 'planter',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                if($model->planter) {
                                                    return '<span class="text-green">承租者种植</span>';
                                                } else {
                                                    return '<span class="text-blue">法人种植</span>';
                                                }
                                            },
                                            'filter' => [1=>'承租者种植',0=>'法人种植'],
                                        ],
                                    ],

                                ]); ?>
                            </div>
                            <div class='tab-pane' id="plantingstructurecheckinfo">
                                <?php
                                $totalDataCheck = clone $dataProviderCheck;
                                $totalDataCheck->pagination = ['pagesize'=>0];
                                $datacheck = arraySearch::find($totalDataCheck)->search();
                                $planteeheckarrclass = explode('\\',$dataProviderCheck->query->modelClass);

                                ?>
                                <?= GridView::widget([
                                    'dataProvider' => $dataProviderCheck,
                                    'filterModel' => $searchCheckModel,
                                    'total' => '<tr height="40">
                                        <td></td>	
                                        <td align="left" id="pct0"><strong>合计</strong></td>
                                        <td align="left" id="pct1"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pct2"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pct3"></td>
                                        <td align="left" id="pct4"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pct5"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pct6"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="pct7"><strong>'.count(Plant::getAllname()).'种</strong></td>
                                        <td align="left" id="pct8"><strong></strong></td>
                                        <td align="left" id="pct9"><strong></strong></td>
                                    </tr>',
                                    'columns' =>[
                                        ['class' => 'yii\grid\SerialColumn'],
                                        [
                                            'label'=>'管理区',
                                            'attribute'=>'management_area',
                                            'headerOptions' => ['width' => '130'],
                                            'value'=> function($model) {
// 				            	var_dump($model);exit;
                                                return ManagementArea::getAreanameOne($model->management_area);
                                            },
                                            'filter' => ManagementArea::getAreaname(),
                                        ],
                                        [
                                            'label' => '农场名称',
                                            'attribute' => 'farms_id',
                                            'options' =>['width'=>120],
                                            'value' => function ($model) {

                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['farmname'];

                                            }
                                        ],
                                        [
                                            'label' => '法人名称',
                                            'attribute' => 'farmer_id',
                                            'options' =>['width'=>120],
                                            'value' => function ($model) {

                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['farmername'];

                                            }
                                        ],
                                        [
                                            'label' => '合同号',
                                            'attribute' => 'state',
                                            'value' => function($model) {
                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['contractnumber'];
                                            },
                                            'filter' => [1=>'正常',2=>'未更换合同',3=>'临时性管理',4=>'买断合同'],
                                        ],
                                        [
                                            'label' => '合同面积',
                                            'attribute' => 'contractarea',
                                            'value' => function($model) {
                                                return Farms::find ()->where ( [
                                                    'id' => $model->farms_id
                                                ] )->one ()['contractarea'];
                                            }
                                        ],
                                        'area',
                                        [
                                            'label' => '种植者',
                                            'attribute' => 'lease_id',
                                            'value' => function($model) {
                                                $lessee =  \app\models\Lease::find()->where(['id'=>$model->lease_id])->one()['lessee'];
                                                if($lessee) {
                                                    return $lessee;
                                                } else {
                                                    return Farms::find ()->where ( [
                                                        'id' => $model->farms_id
                                                    ] )->one ()['farmername'];
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '种植结构',
                                            'attribute' => 'plant_id',
                                            'value' => function($model) {
                                                return Plant::find()->where(['id'=>$model->plant_id])->one()['typename'];
                                            },
                                            'filter' => Plantingstructurecheck::getAllname($totalDataCheck),
                                        ],
                                        [
                                            'label' => '补贴归属',
//                                                'attribute' => 'huinong',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['id'=>$model->plant_id])->one();
                                                if($model->lease_id == 0) {
                                                    if ($plant['typename'] == '大豆') {
                                                        return '<span class="text-green">法人:100%</span>';
                                                    }
                                                    if($plant['typename'] == '玉米') {
                                                        return '<span class="text-blue">种植者:100%</span>';
                                                    }
                                                } else {
                                                    $lease = Lease::find()->where(['id'=>$model->lease_id])->one();
                                                    if ($plant['typename'] == '大豆') {
                                                        if ($lease['ddcj_farmer'] == '100%') {
                                                            return '<span class="text-green">法人:100%</span>';
                                                        }
                                                        if ($lease['ddcj_lessee'] == '100%') {
                                                            return '<span class="text-blue">种植者:100%</span>';
                                                        }
                                                        return '<span class="text-red">法人:' . $lease['ddcj_farmer'] . ' 种植者:' . $lease['ddcj_lessee'].'</span>';
                                                    }
                                                    if ($plant['typename'] == '玉米') {
                                                        if ($lease['ymcj_farmer'] == '100%') {
                                                            return '<span class="text-green">法人:100%</span>';
                                                        }
                                                        if ($lease['ymcj_lessee'] == '100%') {
                                                            return '<span class="text-blue">种植者:100%</span>';
                                                        }
                                                        return '<span class="text-red">法人:' . $lease['ymcj_farmer'] . ' 种植者:' . $lease['ymcj_lessee'].'</span>';
                                                    }
                                                }
                                                return '';
                                            },
//                                                'filter' => $huinongArray,
                                        ],
                                        [
//                                                    'label' => '补贴归属(种植者)',
                                            'attribute' => 'verifydate',
//                                                    'value' => function($model) {
//                                                        return date('Y年m月d日',$model->verifydate);
//                                                    }
                                        ],


                                        [
                                            'label' => '筛选',
                                            'attribute' => 'issame',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                if($model->issame) {
                                                    return '<span class="text-green">与计划一致</span>';
                                                } else {
                                                    return '<span class="text-blue">与计划不一致</span>';
                                                }
                                            },
                                            'filter' => [0=>'与计划不一致',1=>'与计划一致'],
                                        ],
                                    ],

                                ]); ?>
                            </div>

                            <?php
//                            var_dump($dataProvider2plan->getModels());
                            ?>
                            <div class='tab-pane' id="plantinfoplan">
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider2plan,
                                    'filterModel' => $searchModel2plan,
//                                    'pager' => [
//                                        'class' => \frontend\helpers\page\LinkPager::className(),
//                                        'template' => '{pageButtons} {customPage} {pageSize}', //分页栏布局
//                                        'pageSizeList' => [10, 20, 30, 50], //页大小下拉框值
//                                        'customPageWidth' => 50,            //自定义跳转文本框宽度
//                                        'customPageBefore' => ' 跳转到第 ',
//                                        'customPageAfter' => ' 页 ',
//                                    ],
                                    'total' => '<tr height="40">
                                                <td></td>	
                                                <td align="left"><strong>合计</strong></td>
                                                <td align="left" id="t21p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t22p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t23p"></td>
                                                <td align="left" id="t24p"></td>
                                                <td align="left" id="t25p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t26p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t27p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t28p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t29p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t210p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t211p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t212p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t213p"><strong></strong></td>
                                                <td align="left" id="t214p"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                            </tr>',
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],
//         			'id',
                                        [
                                            'attribute' => 'management_area',
                                            'headerOptions' => ['width' => '160'],
                                            'value'=> function($model) {
                                                return ManagementArea::getAreanameOne($model->management_area);
                                            },
                                            'filter' => ManagementArea::getAreaname(),     //此处我们可以将筛选项组合成key-value形式
                                        ],
                                        [
                                            'attribute' => 'farmname',
                                            'options' => ['width'=>'150'],
                                        ],
                                        [
                                            'attribute' => 'farmername',
                                            'options' => ['width'=>'100'],
                                        ],
                                        [
                                            'label' => '合同号',
                                            'attribute' => 'state',
                                            'options' => ['width'=>'150'],
                                            'value' => function($model) {
                                                return $model->contractnumber;
                                            },
                                            'filter' => [1=>'正常',2=>'未更换合同',3=>'临时性管理',4=>'买断合同'],
                                        ],
                                        [
                                            'attribute' => 'contractarea',
                                            'options' => ['width'=>'120'],
                                        ],

                                        [
                                            'label' => '种植面积',
                                            'options' => ['width'=>'110','align'=>'right'],
                                            'value' => function($model) {
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear()])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '大豆',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'大豆'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '玉米',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'玉米'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '小麦',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'小麦'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '马铃薯',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'马铃薯'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '杂豆',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'杂豆'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '北药',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'北药'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '蓝莓',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'蓝莓'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '其它',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'其它'])->one();
                                                $area = Plantingstructure::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            },
                                        ],
                                        [
                                            'label'=>'完成/户数',
                                            'attribute' => 'isfinished',
//                                                'format'=>'raw',
                                            'options' => ['width'=>'80'],
                                            //'class' => 'btn btn-primary btn-lg',
                                            'value' => function($model,$key){
                                                if($model->isfinished) {
                                                    $text = '完成';
                                                } else {
                                                    $text = '未完成';
                                                }
                                                return $text;
                                            },
                                            'filter' => [0=>'未完成',1=>'已完成'],
                                        ],

                                    ],
                                ]); ?>
                            </div>
                            <div class='tab-pane' id="plantinfo">
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider2,
                                    'filterModel' => $searchModel2,
//                                    'pager' => [
//                                        'class' => \frontend\helpers\page\LinkPager::className(),
//                                        'template' => '{pageButtons} {customPage} {pageSize}', //分页栏布局
//                                        'pageSizeList' => [10, 20, 30, 50], //页大小下拉框值
//                                        'customPageWidth' => 50,            //自定义跳转文本框宽度
//                                        'customPageBefore' => ' 跳转到第 ',
//                                        'customPageAfter' => ' 页 ',
//                                    ],
                                    'total' => '<tr height="40">
                                                <td></td>	
                                                <td align="left"><strong>合计</strong></td>
                                                <td align="left" id="t21"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t22"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t23"></td>
                                                <td align="left" id="t24"></td>
                                                <td align="left" id="t25"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t26"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t27"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t28"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t29"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t210"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t211"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t212"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                                <td align="left" id="t213"><strong></strong></td>
                                                <td align="left" id="t214"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                            </tr>',
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],
//         			'id',
                                        [
                                            'attribute' => 'management_area',
                                            'headerOptions' => ['width' => '160'],
                                            'value'=> function($model) {
                                                return ManagementArea::getAreanameOne($model->management_area);
                                            },
                                            'filter' => ManagementArea::getAreaname(),     //此处我们可以将筛选项组合成key-value形式
                                        ],
                                        [
                                            'attribute' => 'farmname',
                                            'options' => ['width'=>'150'],
                                        ],
                                        [
                                            'attribute' => 'farmername',
                                            'options' => ['width'=>'100'],
                                        ],
                                        [
                                            'label' => '合同号',
                                            'attribute' => 'state',
                                            'options' => ['width'=>'150'],
                                            'value' => function($model) {
                                                return $model->contractnumber;
                                            },
                                            'filter' => [1=>'正常',2=>'未更换合同',3=>'临时性管理',4=>'买断合同'],
                                        ],
                                        [
                                            'attribute' => 'contractarea',
                                            'options' => ['width'=>'120'],
                                        ],

                                        [
                                            'label' => '种植面积',
                                            'options' => ['width'=>'110','align'=>'right'],
                                            'value' => function($model) {
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear()])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '大豆',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'大豆'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '玉米',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'玉米'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '小麦',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'小麦'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '马铃薯',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'马铃薯'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '杂豆',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'杂豆'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '北药',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'北药'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '蓝莓',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'蓝莓'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            }
                                        ],
                                        [
                                            'label' => '其它',
                                            'value' => function($model) {
                                                $plant = Plant::find()->where(['typename'=>'其它'])->one();
                                                $area = Plantingstructurecheck::find()->where(['farms_id'=>$model->farms_id,'year'=>User::getYear(),'plant_id'=>$plant['id']])->sum('area');
                                                if($area) {
                                                    return sprintf("%.2f", $area);
                                                } else {
                                                    return 0;
                                                }
                                            },
                                        ],
                                        [
                                            'label'=>'完成/户数',
                                            'attribute' => 'isfinished',
//                                                'format'=>'raw',
                                            'options' => ['width'=>'80'],
                                            //'class' => 'btn btn-primary btn-lg',
                                            'value' => function($model,$key){
                                                if($model->isfinished) {
                                                    $text = '完成';
                                                } else {
                                                    $text = '未完成';
                                                }
                                                return $text;
                                            },
                                            'filter' => [0=>'未完成',1=>'已完成'],
                                        ],

                                    ],
                                ]); ?>
                            </div>

                            <div class='tab-pane' id="plantingstructurezhu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-content">
<!--                                                <div class="card-header" data-background-color="white">-->
                                                    <?php
                                                    $js = ES::bar()->DOM('plantingstructuredatazhushow',true,'800px','700px')->obj($dataProvider)->where(['field'=>'plant_id','sum'=>'area','unit'=>'亩'])->options(['title'=>'计划种植结构'])->JS();
                                                    echo $js;
                                                    ?>
<!--                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-content">
                                                <!--                                                <div class="card-header" data-background-color="white">-->
                                                <?php
                                                echo ES::bar()->DOM('plantingstructuredatacheckshow',true,'800px','700px')->obj($dataProviderCheck)->where(['field'=>'plant_id','sum'=>'area','unit'=>'亩'])->options(['title'=>'复核种植结构'])->JS();
                                                ?>
                                                <!--                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class='tab-pane' id="plantingstructurebing">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-content">
                                                <?php
                                                echo ES::pie()->DOM('plantingstructuredataingshow',true,'800px','700px')->obj($dataProvider)->where(['field'=>'plant_id','sum'=>'area','unit'=>'亩'])->options(['title'=>'计划种植结构'])->JS();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-content">
                                                <?php
                                                echo ES::pie()->DOM('plantingstructuredataingcheckshow',true,'800px','700px')->obj($dataProviderCheck)->where(['field'=>'plant_id','sum'=>'area','unit'=>'亩'])->options(['title'=>'复核种植结构'])->JS();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php User::tableEnd();?>
            </div>
        </div>
    </section>
</div>

<?php
$where = whereHandle::toPlantWhere($dataProvider2->query->where);
$whereplan = whereHandle::toPlantWhere($dataProvider2plan->query->where);
//var_dump($where);exit;
?>
<script>


</script>
<script>

    $('.shclDefault').shCircleLoader({color: "red"});
    $(document).ready(function () {
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $plantarrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-farms_id'}, function (data) {
            $('#pt1').html(data + '户');
        });

        $.getJSON('index.php?r=search/search', {modelClass: '<?= $plantarrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-farmer_id'}, function (data) {
            $('#pt2').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $plantarrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'sum-contractarea'}, function (data) {
            $('#pt4').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $plantarrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-lease_id',andwhere:'<?= json_encode('lease_id>0')?>'}, function (data) {
            $('#pt6').html('承租者'+ data + '人');
        });


//        $.getJSON('index.php?r=search/search', {modelClass: 'Farms',where:'<?//= json_encode($dataProvider->query->where)?>//',command:'count-plant_id'}, function (data) {
//             $('#t4').html(data + '种');
//         });
        //$.getJSON('index.php?r=search/search', {modelClass: '<?//= $arrclass[2]?>',where:'<?//= json_encode($dataProvider->query->where)?>',command:'count-goodseed_id'}, function (data) {
//             $('#t5').html(data + '种');
//         });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $plantarrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'sum-area'}, function (data) {
            $('#pt5').html(data + '亩');
        });

    });

    $(document).ready(function () {
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2[2]?>',where:'<?= json_encode($dataProvider2->query->where)?>',command:'count-farms_id'}, function (data) {
            $('#t21').html(data + '户');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2[2]?>',where:'<?= json_encode($dataProvider2->query->where)?>',command:'count-farmer_id'}, function (data) {
            $('#t22').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2[2]?>',where:'<?= json_encode($dataProvider2->query->where)?>',command:'sum-contractarea'}, function (data) {
            $('#t24').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area'}, function (data) {
            $('#t25').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>6])?>'}, function (data) {
            $('#t26').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>3])?>'}, function (data) {
            $('#t27').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>2])?>'}, function (data) {
            $('#t28').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>4])?>'}, function (data) {
            $('#t29').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>14])?>'}, function (data) {
            $('#t210').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>8])?>'}, function (data) {
            $('#t211').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>17])?>'}, function (data) {
            $('#t212').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($where)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>16])?>'}, function (data) {
            $('#t213').html(data + '亩');
        });
        $.getJSON('index.php?r=search/plantfinished', {where:'<?= json_encode($dataProvider2->query->where)?>'}, function (data) {
            $('#t214').html(data.finished+'/'+data.all);
        });
    });

    $(document).ready(function () {
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2plan[2]?>',where:'<?= json_encode($dataProvider2plan->query->where)?>',command:'count-farms_id'}, function (data) {
            $('#t21p').html(data + '户');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2plan[2]?>',where:'<?= json_encode($dataProvider2plan->query->where)?>',command:'count-farmer_id'}, function (data) {
            $('#t22p').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass2plan[2]?>',where:'<?= json_encode($dataProvider2plan->query->where)?>',command:'sum-contractarea'}, function (data) {
            $('#t24p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area'}, function (data) {
            $('#t25p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>6])?>'}, function (data) {
            $('#t26p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>3])?>'}, function (data) {
            $('#t27p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>2])?>'}, function (data) {
            $('#t28p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>4])?>'}, function (data) {
            $('#t29p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>14])?>'}, function (data) {
            $('#t210p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>8])?>'}, function (data) {
            $('#t211p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>17])?>'}, function (data) {
            $('#t212p').html(data + '亩');
        })
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructure',where:'<?= json_encode($whereplan)?>',command:'sum-area',andwhere:'<?= json_encode(['plant_id'=>16])?>'}, function (data) {
            $('#t213p').html(data + '亩');
        });
        $.getJSON('index.php?r=search/plantfinished', {where:'<?= json_encode($dataProvider2plan->query->where)?>'}, function (data) {
            $('#t214p').html(data.finished+'/'+data.all);
        });
    });

    $(document).ready(function () {
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $planteeheckarrclass[2]?>',where:'<?= json_encode($dataProviderCheck->query->where)?>',command:'count-farms_id'}, function (data) {
            $('#pct1').html(data + '户');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $planteeheckarrclass[2]?>',where:'<?= json_encode($dataProviderCheck->query->where)?>',command:'count-farmer_id'}, function (data) {
            $('#pct2').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $planteeheckarrclass[2]?>',where:'<?= json_encode($dataProviderCheck->query->where)?>',command:'sum-contractarea'}, function (data) {
            $('#pct4').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($dataProviderCheck->query->where)?>',command:'sum-area'}, function (data) {
            $('#pct5').html(data + '亩');
        });
        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?= json_encode($dataProviderCheck->query->where)?>',command:'count-lease_id',andwhere:'<?= json_encode('lease_id>0')?>'}, function (data) {
            $('#pct6').html('承租者'+ data + '人');
        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>3])?>//'}, function (data) {
//            $('#pct7').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>2])?>//'}, function (data) {
//            $('#pct8').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>4])?>//'}, function (data) {
//            $('#pct9').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>14])?>//'}, function (data) {
//            $('#pct10').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>8])?>//'}, function (data) {
//            $('#pct11').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>17])?>//'}, function (data) {
//            $('#pct12').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/search', {modelClass: 'Plantingstructurecheck',where:'<?//= json_encode($dataProviderCheck->query->where)?>//',command:'sum-area',andwhere:'<?//= json_encode(['plant_id'=>16])?>//'}, function (data) {
//            $('#pct13').html(data + '亩');
//        });
//        $.getJSON('index.php?r=search/plantfinished', {where:'<?//= json_encode($dataProviderCheck->query->where)?>//'}, function (data) {
//            $('#pct14').html(data.finished+'/'+data.all);
//        });
    });
</script>