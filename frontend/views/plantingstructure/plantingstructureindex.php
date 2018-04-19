<?php

use app\models\Tables;
use yii\helpers\Html;
use frontend\helpers\grid\GridView;
use app\models\Farms;
use app\models\Plantingstructure;
use app\models\Plantingstructurecheck;
use app\models\Plant;
use app\models\Help;
use app\models\Theyear;
use frontend\helpers\htmlColumn;
use yii\helpers\Url;
use app\models\User;
use app\models\Lockstate;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\leaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="lease-index">
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3>
                    <?php $farms = Farms::find()->where(['id'=>$_GET['farms_id']])->one();
					$plantings = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()]);?>
                        <?= Help::showHelp3('种植结构调查数据','plantingstructurecheck-index')?><font color="red">(<?= User::getYear()?>年度)</font>&nbsp;
						<?php
						if($plantings->count() == 0)
							echo Html::a('复核',Url::to(['plantingstructurecheck/plantingstructurecheckindex','farms_id'=>$_GET['farms_id']]),['class'=>'btn btn-primary']);
						else
							echo Html::a('复核','#',['class'=>'btn btn-primary','disabled'=>'disabled']);

						?>
                    </h3>
                </div>
                <div class="box-body">
<?php Farms::showRow($_GET['farms_id']);?>
	<script type="text/javascript">
	function openwindows(url)
	{
		window.open(url,'','width=1200,height=600,top=50,left=80, toolbar=no, status=no, menubar=no, resizable=no, scrollbars=yes');
		self.close();
	}
	</script>
	<?php
		$SumArea = 0.0;
		$leaseSumArea = 0.0;
		$farmerArea = 0.0;
		$leaseArea = 0.0;
		$strArea = '';
		$arrayArea = [];
		$plantFarmerArea = 0.00;
		$allarea = $farms['contractarea'];
        $leaseArea0 = \app\models\Lease::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()])->sum('lease_area');
        if(empty($leaseArea0)) {
            $leaseArea = 0;
        } else {
            $leaseArea = sprintf('%.2f',$leaseArea0);
        }
        if($leaseArea > 0) {
            if(bccomp($allarea,$leaseArea) == 1) {
                $plantFarmerArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->sum('area');
                if($plantFarmerArea) {
                    if(bccomp($leaseArea,$plantFarmerArea) == 1) {
                        $farerShowButtion = true;
                    } else {
                        $farerShowButtion = false;
                    }
                } else {
                    $plantFarmerArea = bcsub($allarea,$leaseArea,2);
                }
            } else {
                $farerShowButtion = false;
                $plantLeaseArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()])->andWhere('lease_id>0')->sum('area');
                if($plantLeaseArea) {
                    if(bccomp($leaseArea,$plantLeaseArea) == 1) {
                        $leaseShowButton = true;
                    } else {
                        $leaseShowButton = false;
                    }
                } else {
                    $leaseShowButton = true;
                }
            }
        } else {
            $farerShowButtion = true;
            $plantFarmerArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->sum('area');
            if($plantFarmerArea) {
                if(bccomp($leaseArea,$plantFarmerArea) == 1) {
                    $farerShowButtion = true;
                } else {
                    $farerShowButtion = false;
                }
            } else {
                $plantFarmerArea = $allarea;
            }
        }
////		if($leases) {
////			foreach ($leases as $value) {
////				$leaseArea += $value['lease_area'];
////			}
////		} else {
//			$plantFarmerArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->sum('area');
//            $plantLeaseArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()])->andWhere('lease_id>0')->sum('area');
////    var_dump($plantLeaseArea);
////		}
//        if(empty($plantFarmerArea)) {
//            $plantFarmerArea = 0;
//        }
//        if(empty($plantLeaseArea)) {
//            $plantLeaseArea = 0;
//        }
//
////		foreach ($plantings->all() as $value) {
////			$SumArea += $value['area'];
////		}
//		$isFarmerAdd = false;
//		if($farmerArea > 0) {
//			$isFarmerAdd = true;
//		}
//		$sum = $plantFarmerArea + $plantLeaseArea;
//		$isView = (float)bccomp($allarea, $sum);
//    var_dump($allarea);
//    var_dump($sum);
//    var_dump($isView);exit;
	?>
<h2>调查数据</h2>
<table class="table table-bordered table-hover">
  <tr bgcolor="#faebd7">
    <td width="12%" colspan="2" align="center"><strong>法人</strong></td>
    <td colspan="2" align="center"><strong>种植面积</strong></td>
    <td width="22%" align="center"><strong>操作</strong></td>
    </tr>
  <tr>
    <td width="12%" colspan="2" align="center"><?= $farms['farmername'] ?></td>
    <td colspan="2" align="center"><?= $plantFarmerArea?>亩</td>
    <td align="center"><?php if($farerShowButtion) {
            if(User::disabled()) {
                echo Html::a('添加','#', [
                    'id' => 'employeecreate',
                    'title' => '给'.$farms['farmername'].'添加',
                    'class' => 'btn btn-primary',
                    'disabled' => User::disabled(),
                ]);
            } else {
                echo Html::a('添加','index.php?r=plantingstructure/plantingstructurecreate&lease_id=0&farms_id='.$_GET['farms_id'], [
            			'id' => 'employeecreate',
            			'title' => '给'.$farms['farmername'].'添加',
            			'class' => 'btn btn-primary',
            			]);
            }
        }?></td>
    </tr>
  <?php
  	$farmerplantings = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->all();
		if($farmerplantings) {
	  	  	foreach($farmerplantings as $v) {
  ?>
  <tr>
    <td colspan="2" align="center">|_</td>

    <td align="center">种植作物面积：<?= $v['area']?>亩</td>
    <td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
    <td align="center"><?php
        $plants = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()])->count();
        if($plants == 0) {
            htmlColumn::show(['id'=>$v['id'],'lease_id'=>$v['lease_id'],'farms_id'=>$v['farms_id']]);
        }?></td>
    </tr>
  <?php }?>
</table>
			<br>
<?php }
if($leases) {
?>
<table class="table table-bordered table-hover">
  <tr bgcolor="#faebd7">
    <td width="12%" colspan="2" align="center"><strong>承租人</strong></td>
    <td colspan="2" align="center"><strong>承租面积</strong></td>
    <td width="22%" align="center"><strong>操作</strong></td>
    </tr>
  <?php
  		$isLeaseViewAdd = 0;
  		foreach($leases as $val) {
	  	$isLeaseViewAdd += Plantingstructure::find()->where(['lease_id'=>$val['id']])->one()['area'];
  ?>
  <tr>
    <td colspan="2" align="center"><?= $val['lessee'] ?></td>
     <td colspan="2"  align="center"><?= $val['lease_area']?>亩</td>
    <?php
    	  $leaseData = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>$val['id']])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
    ?>
    <td align="center"><?php if($leaseShowButton) {
            if(User::disabled()) {
                echo Html::a('添加','#', [
                    'id' => 'employeecreate',
                    'title' => '给'.$val['lessee'].'添加',
                    'class' => 'btn btn-primary',
                    'disabled' => User::disabled(),
                ]);
            } else {
                echo Html::a('添加','index.php?r=plantingstructure/plantingstructurecreate&lease_id='.$val['id'].'&farms_id='.$_GET['farms_id'], [
            			'id' => 'employeecreate',
            			'title' => '给'.$val['lessee'].'添加',
            			'class' => 'btn btn-primary',
            			]);
            }
        }?></td>
    </tr>
  <?php

	  	foreach($leaseData as $v) {
  ?>
  <tr>
    <td colspan="2" align="center">|_</td>
    <td align="center">种植作物面积：<?= $v['area']?>亩</td>
    <td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
    <td align="center"><?php
            $plants = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>$v['lease_id'],'year'=>User::getYear()])->count();
            if($plants == 0) {
                htmlColumn::show(['id'=>$v['id'],'lease_id'=>$v['lease_id'],'farms_id'=>$v['farms_id']]);
            }?></td>
    </tr>
  <?php }}?>
</table>
<?php }?>
<?php
		$SumArea = 0.0;
		$leaseSumArea = 0.0;
		$farmerArea = 0.0;
		$leaseArea = 0.0;
		$strArea = '';
		$arrayArea = [];
		$plantFarmerArea = 0.00;
		$allarea = $farms['contractarea'];
//		if($leases) {
//			foreach ($leases as $value) {
//				$leaseArea += $value['lease_area'];
//			}
//		} else {
			$plantFarmerArea = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->sum('area');
            $plantLeaseArea = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'year'=>User::getYear()])->andWhere('lease_id>0')->sum('area');
//		}
        if(empty($plantFarmerArea)) {
            $plantFarmerArea = 0;
        }
        if(empty($plantLeaseArea)) {
            $plantLeaseArea = 0;
        }
		$farmerArea = (float)bcsub($allarea , $plantFarmerArea,2);
//		foreach ($plantings->all() as $value) {
//			$SumArea += $value['area'];
//		}
		$isFarmerAdd = false;
		if($farmerArea > 0) {
            $isFarmerAdd = true;
        }
//		$isPlantingViewAdd = (float)bcsub($farmerArea , $farmerSumArea,2);
//    var_dump($isPlantingViewAdd);
//		$plantFarmerArea = Plantingstructure::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->sum('area');

//		if($plantFarmerArea < $farmerArea) {
//			$isFarmerAdd = true;
//		}
		$sum = $plantFarmerArea + $plantLeaseArea;
        if($sum > 0) {
            $isView = bcsub($allarea, $sum,2);
        } else {
            $isView = false;
        }

		if($isView) {
            ?>
            <h3>复合数据</h3>
            <table class="table table-bordered table-hover">
            <tr bgcolor="#faebd7">
                <td width="12%" colspan="2" align="center"><strong>法人</strong></td>
                <td colspan="2" align="center"><strong>种植面积</strong></td>
                <td width="22%" align="center"><strong>操作</strong></td>
            </tr>
            <tr>
                <td width="12%" colspan="2" align="center"><?= $farms['farmername'] ?></td>
                <td colspan="2" align="center"><?= $plantFarmerArea?>亩</td>
                <td align="center"></td>
            </tr>
            <?php
            $farmerplantings = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0,'year'=>User::getYear()])->all();
            if($farmerplantings) {
                foreach($farmerplantings as $v) {
                    ?>
                    <tr>
                        <td colspan="2" align="center">|_</td>

                        <td align="center">种植作物面积：<?= $v['area']?>亩</td>
                        <td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
                        <td align="center"><?php if($v['issame']) echo '<h4 class="text-green">一致</h4>';else echo '<h4 class="text-red">不一致</h4>';?></td>
                    </tr>
                <?php }?>
                </table>
                <br>
            <?php }
            if($leases) {
                ?>
                <table class="table table-bordered table-hover">
                    <tr bgcolor="#faebd7">
                        <td width="12%" colspan="2" align="center"><strong>承租人</strong></td>
                        <td colspan="2" align="center"><strong>承租面积</strong></td>
                        <td width="22%" align="center"><strong>操作</strong></td>
                    </tr>
                    <?php
                    $isLeaseViewAdd = 0;
                    foreach($leases as $val) {
                        $isLeaseViewAdd += Plantingstructurecheck::find()->where(['lease_id'=>$val['id']])->one()['area'];
                        ?>
                        <tr>
                            <td colspan="2" align="center"><?= $val['lessee'] ?></td>
                            <td colspan="2"  align="center"><?= $val['lease_area']?>亩</td>
                            <?php
                            $leaseData = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>$val['id']])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
                            ?>
                            <td align="center"></td>
                        </tr>
                        <?php

                        foreach($leaseData as $v) {
                            ?>
                            <tr>
                                <td colspan="2" align="center">|_</td>
                                <td align="center">种植作物面积：<?= $v['area']?>亩</td>
                                <td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
                                <td align="center"><?php if($v['issame']) echo '<h4 class="text-green">一致</h4>';else echo '<h4 class="text-red">不一致</h4>';?></td>
                            </tr>
                        <?php }}?>
                </table>
            <?php }}?>

                </div>
            </div>
        </div>
    </div>
</section>
</div>
<script>
$('#rowjump').keyup(function(event){
	input = $(this).val();
	$.getJSON('index.php?r=farms/getfarmid', {id: input}, function (data) {
		$('#setFarmsid').val(data.farmsid);
	});
});
</script>
