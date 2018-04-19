<?php

use app\models\Tables;
use yii\helpers\Html;
use frontend\helpers\grid\GridView;
use app\models\Farms;
use frontend\helpers\htmlColumn;
use app\models\Plant;
use app\models\Lease;
use app\models\Inputproduct;
use app\models\Pesticides;
use app\models\Plantpesticides;
use app\models\Plantinputproduct;
use app\models\User;
use app\models\Plantingstructurecheck;
use app\models\Theyear;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\leaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<style>
	.table-bordered {
		border: 1px solid #dddddd;  /* 整体表格边框 */
	}
</style>
<div class="lease-index">
	<script type="text/javascript">
	function openwindows(url)
	{
		window.open(url,'','width=1200,height=600,top=50,left=80, toolbar=no, status=no, menubar=no, resizable=no, scrollbars=yes');
		self.close();
	}
	</script>
	<?php
	$sumArea = 0;
	foreach($plantings as $v) {
		?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<?php Farms::showRow($_GET['farms_id']);?>
						<h3>
							<?php $farms = Farms::find()->where(['id'=>$_GET['farms_id']])->one();?>
							<?= $farms['farmname']; ?> 的种植结构<font color="red">(<?= User::getYear()?>年度)</font>
						</h3>
					</div>
					<div class="box-body">
	<table class="table table-bordered table-hover">
	<tr>
		<td width="20%" align="center">种植者</td>
		<td width="12%" align="center">姓名</td>
		<td width="12%" align="center">种植面积</td>
		<td width="12%" align="center">种植作物</td>
		<td width="12%" align="center">操作</td>
	</tr>
  <tr>
	<td align="center"><?php if($v['lease_id'] == 0) echo '法人'; else echo '租赁者'; ?></td>
    <td align="center"><?php if($v['lease_id'] == 0) echo $farms['farmername']; else echo Lease::find()->where(['id'=>$v['lease_id']])->one()['lessee']; ?></td>
    <?php
    	  	$sumArea += (float)$v['area'];
    ?>
    <td align="center"><?= $v['area']?>亩</td>
    <td align="center"><?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename'];?></td>
	  <td align="center"><?php echo Html::a('相同',Url::to(['plantingstructurecheck/plantingstructurechecksame','planting_id'=>$v['id']]),['class'=>'btn btn-primary','help'=>'plantingstructurecheck-samebutton','data' => ['confirm' => '您确定复核数据与此数据相同吗？',],]);?></td>
    </tr>
	<tr>
		<td colspan="5">
	<?php
		$plantinput = Plantinputproduct::find()->where(['planting_id'=>$v['id']])->all();
		if($plantinput) {?>
			<h5 class="box-title">投入品使用情况</h5>
	<table class="table table-hover">
		<tr>
			<td align="center">投入品大类</td>
			<td align="center">投入品小类</td>
			<td align="center">投入品</td>
			<td align="center">用量</td>
		</tr>
		<?php
			foreach ($plantinput as $value) {?>
		<tr>
			<td align="center"><?php echo Inputproduct::find()->where(['id'=>$value['father_id']])->one()['fertilizer'];?></td>
			<td align="center"><?php echo Inputproduct::find()->where(['id'=>$value['son_id']])->one()['fertilizer']; ?></td>
			<td align="center"><?php echo Inputproduct::find()->where(['id'=>$value['inputproduct_id']])->one()['fertilizer']; ?></td>
			<td align="center"><?php echo $value['pconsumption'].'公斤/亩';?></td>
		</tr>
		<?php }
		?>
	</table>
	<?php }?>
	<?php
		$pesticides = Plantpesticides::find()->where(['planting_id'=>$v['id']])->all();
		if($pesticides) {?>
			<h5 class="box-title">农药使用情况</h5>
		<table class="table table-hover">
			<tr>
				<td width=40% align='center'>农药</td>
				<td align='center'>农药用量</td>
			</tr>
			<?php
			foreach ($pesticides as $value) {?>
				<tr>
					<td align="center"><?php echo Pesticides::find()->where(['id'=>$value['pesticides_id']])->one()['pesticidename']; ?></td>
					<td align="center"><?php echo $value['pconsumption'].'公斤/亩'; ?></td>
				</tr>
			<?php }
			?>
		</table>
	<?php }
	?>
		</td>
	</tr>
	</table>
					</div>
				</div>
			</div>
		</div>
	</section>
  <?php }?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<?php $farms = Farms::find()->where(['id'=>$_GET['farms_id']])->one();
							$plantings = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();?>
							<?= $farms['farmname']; ?> 的种植结构复核
						</h3>
					</div>
					<div class="box-body">
						<script type="text/javascript">
							function openwindows(url)
							{
								window.open(url,'','width=1200,height=600,top=50,left=80, toolbar=no, status=no, menubar=no, resizable=no, scrollbars=yes');
								self.close();
							}
						</script>
						<?php
						$farmerSumArea = 0.0;
						$leaseSumArea = 0.0;
						$farmerArea = 0.0;
						$leaseArea = 0.0;
						$isLeaseViewAdd = 0.0;
						$strArea = '';
						$arrayArea = [];
						$plantingLeaseArea = 0.0;
						$allarea = $farms['contractarea'];
						foreach ($leases as $value) {
							$leaseArea += $value['lease_area'];
							$plantingLeaseArea = Plantingstructurecheck::find()->where(['lease_id'=>$value['id']])->sum('area');
						}

						$farmerArea = (float)bcsub($allarea , $leaseArea,2);
						foreach ($plantings as $value) {
							$farmerSumArea += $value['area'];
						}
						$isLeaseViewAdd = (float)bcsub($leaseArea, $plantingLeaseArea,2);
						$isPlantingViewAdd = (float)bcsub($farmerArea , $farmerSumArea,2);
						$sum = $leaseArea + $farmerArea;
						$isView = bcsub($allarea, $sum,2);
						if($isView) {
//			$arrayZongdi = Lease::getNOZongdi($_GET['farms_id']);
//		if(is_array($arrayZongdi))
//			$zongdilist = implode('、',$arrayZongdi);
//		else
//			$zongdilist =  bcsub($farms['measure'] , $arrayZongdi,2);
							//var_dump($arrayZongdi);
							?>
							<table class="table table-bordered table-hover">
							<tr bgcolor="#faebd7">
								<td width="12%" colspan="2" align="center"><strong>法人</strong></td>
								<td colspan="2" align="center"><strong>种植面积</strong></td>
								<td width="22%" align="center"><strong>操作</strong></td>
							</tr>
							<tr>
								<td width="12%" colspan="2" align="center"><?= $farms['farmername'] ?></td>
								<?php

								//     	  var_dump($plantings);
								$sumArea = 0;
								foreach($plantings as $value) {
									$sumArea += (float)$value['area'];
								}
								?>
								<td colspan="2" align="center"><?= $farmerArea?>亩</td>
								<td align="center"><?php if($isPlantingViewAdd) {?><?= Html::a('添加','index.php?r=plantingstructurecheck/plantingstructurecheckcreate&lease_id=0&farms_id='.$_GET['farms_id'], [
										'id' => 'employeecreate',
										'title' => '给'.$farms['farmername'].'添加',
										'class' => 'btn btn-primary',
									]);?><?php }?></td>
							</tr>
							<?php
							$farmerplantings = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>0])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
							if($farmerplantings) {
								foreach($farmerplantings as $v) {
									?>
									<tr>
										<td colspan="2" align="center">|_</td>

										<td align="center">种植作物面积：<?= $v['area']?>亩</td>
										<td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
										<td align="center"><?php htmlColumn::show(['id'=>$v['id'],'lease_id'=>$v['lease_id'],'farms_id'=>$v['farms_id']]);?></td>
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
									foreach($leases as $val) {
										?>
										<tr>
											<td colspan="2" align="center"><?= $val['lessee'] ?></td>
											<td colspan="2"  align="center"><?= $val['lease_area']?>亩</td>
											<?php
											$leaseData = Plantingstructurecheck::find()->where(['farms_id'=>$_GET['farms_id'],'lease_id'=>$val['id']])->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
											?>
											<td align="center"><?php if($isLeaseViewAdd) {?><?= Html::a('添加','index.php?r=plantingstructurecheck/plantingstructurecheckcreate&lease_id='.$val['id'].'&farms_id='.$_GET['farms_id'], [
													'id' => 'employeecreate',
													'title' => '给'.$val['lessee'].'添加',
													'class' => 'btn btn-primary',
												]);?><?php }?></td>
										</tr>
										<?php

										foreach($leaseData as $v) {
											?>
											<tr>
												<td colspan="2" align="center">|_</td>
												<td align="center">种植作物面积：<?= $v['area']?>亩</td>
												<td align="center">作物：<?= Plant::find()->where(['id'=>$v['plant_id']])->one()['typename']?></td>
												<td align="center"><?php htmlColumn::show(['id'=>$v['id'],'lease_id'=>$v['lease_id'],'farms_id'=>$v['farms_id']]);?></td>
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
