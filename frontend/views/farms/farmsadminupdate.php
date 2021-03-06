<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\helpers\ActiveFormrdiv;
use yii\helpers\ArrayHelper;
use app\models\Cooperative;
use dosamigos\datetimepicker\DateTimePicker;
use app\models\Parcel;
use app\models\Lease;
use app\models\ManagementArea;
use app\models\Farms;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Farms */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
	.remove{cursor:pointer}
</style>
<div class="farms-form">
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3>&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->title ?><font color="red">(<?= User::getYear()?>年度)</font></h3></div>
					<div class="box-body">

    <?php $form = ActiveFormrdiv::begin(); ?>
<?php
	if(\app\models\User::getItemname('法规科')) {
		$readonly = false;
	} else
		$readonly = true;
?>
<table class="table table-bordered table-hover">
		<tr>
			<td width=15% align='right'>农场名称</td>
			<td align='left'><?= $form->field($model, 'farmname')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false) ?></td>
			<td align='right'>承包人姓名</td>
			<td align='left'><?= $form->field($model, 'farmername')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false) ?></td>
			<td align='right'>身份证号</td>
			<td colspan="3" align='left'><?= $form->field($model, 'cardid')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false) ?></td>
		</tr>
		<tr>
			<td width=15% align='right'>电话号码</td>
			<td align='left'><?= $form->field($model, 'telephone')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false) ?></td>
			<td align='right'>农场位置</td>
			<td align='left'><?= $form->field($model, 'address')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false) ?></td>
			<td align='right'>经度</td>
			<td align='left'><?= $form->field($model, 'longitude')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false) ?></td>
			<td align='right'>纬度</td>
			<td align='left'><?= $form->field($model, 'latitude')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false) ?></td>
		</tr>
			<tr>
			<td width=15% align='right'>管理区</td>
			<td align='left'><?= ManagementArea::getAreanameOne($model->management_area) ?></td>
			<td align='right'>合同号</td><?= Html::hiddenInput('temp_contractnumber',$model->contractnumber,['id'=>'tempContractNumber'])?>
			<td align='left'><?php if($model->contractnumber == '') $model->contractnumber = Farms::getNewContractnumber()?><?= $form->field($model, 'contractnumber')->textInput(['readonly' => true])->label(false)->error(false) ?></td>
			<td align='right'>审批年度</td>
			<td align='left'><?= $form->field($model, 'spyear')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false)?></td>
			<td align='right'>合同领取日期</td><?php if($model->surveydate) $model->surveydate = date('Y-m-d',$model->surveydate);?>
			<td align='left'><?= $form->field($model, 'surveydate')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false) ?></td>
			</tr>
		<tr>
			<td width=15% align='right'>承包年限</td><?php if(empty($model->begindate)) $model->begindate = date('Y-m-d');?>
			<td align='center'>自</td>
			<td align='center'><?= $form->field($model, 'begindate')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false)->widget(
    DateTimePicker::className(), [
        // inline too, not bad
        'inline' => false, 
    	'language'=>'zh-CN',
        
        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
        'clientOptions' => [
            'autoclose' => true,
        	'minView' => 2,
        	'maxView' => 4,
            'format' => 'yyyy-mm-dd'
        ]]) ?></td>
			<td align='center'>至</td><?php //$model->enddate = '2025-09-13'?>
			<td align='center'><?= $form->field($model, 'enddate')->textInput(['maxlength' => 500,'readonly'=>$readonly])->label(false)->error(false)->widget(
    DateTimePicker::className(), [
        // inline too, not bad
        'inline' => false, 
    	'language'=>'zh-CN',
        
        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
        'clientOptions' => [
            'autoclose' => true,
        	'minView' => 2,
        	'maxView' => 4,
            'format' => 'yyyy-mm-dd'
        ]]) ?></td>
			<td align='center'>止</td>
			<td align='center'>&nbsp;</td>
			<td align='center'>&nbsp;</td>
		</tr>
		<tr>
			<?= Html::hiddenInput('newzongdi',Lease::getZongdiToNumber($model->zongdi),['id'=>'new-zongdi']) ?>
			<?= Html::hiddenInput('oldchangezongdi','',['id'=>'oldchangezongdi']) ?>
			<?= $form->field($model, 'zongdi')->hiddenInput()->label(false)->error(false) ?>
			<td width=15% align='right'>宗地</td><?= html::hiddenInput('tempzongdi','',['id'=>'temp-zongdi'])?>
			<td colspan="7" align='left'>
			
			<span id="inputZongdi" class="select2-container select2-container--default select2-container--below" dir="ltr" style="width: 100%; color: #000;">
	<span id="inputZongdi" class="select2-container select2-container--default select2-container--below" dir="ltr" style="width: 100%; color: #000;">
	<span class="selection">
		<span class="select2-selection select2-selection--multiple" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="0">
			<ul class="select2-selection__rendered">
				<li class="select2-search select2-search--inline">
					<input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" placeholder="" style="width: 0.75em;">
					<?php
					if($model->zongdi) {
					$iden = explode('、',$model->zongdi);
					if($iden) {
						foreach ($iden as $zongdi) {
//							echo '<a href="#" id="zongdiinfo"><li class="select2-selection__choice" id="new' . Lease::getZongdi($zongdi) . '" title="' . $zongdi . '"><span class="remove text-red" role="presentation" onclick=zongdiRemove("' . Lease::getZongdi($zongdi) . '","' . Lease::getArea($zongdi) . '","dialog")>×</span>' . $zongdi . '</li></a>';
							echo '<li class="select2-selection__choice" id="new' . Lease::getZongdi($zongdi) . '" title="' . $zongdi . '"><span class="remove text-red" role="presentation" onclick=zongdiRemove("' . Lease::getZongdi($zongdi) . '","' . Lease::getArea($zongdi) . '","dialog")>×</span>' . $zongdi . '</li>';
						}
					}} else {
						echo '<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" placeholder="" style="width: 0.75em;"></li>';
					}
					?>
				</li>
			</ul>
		</span>
	</span>
	<span class="dropdown-wrapper" aria-hidden="true"></span>

			</td>
		</tr>
		<tr>
		  <td align='right'>合同面积</td>
		  <td align='left'><?= $form->field($model, 'contractarea')->textInput(['readonly'=>true])->label(false)->error(false) ?></td>
		  <td align='right'>宗地面积</td>
		  <td align='left'><?= $form->field($model, 'measure')->textInput(['readonly'=>true])->label(false)->error(false) ?></td>
		  <td align='right'>未明确地块面积</td>
		  <td align='left'><?= $form->field($model, 'notclear')->textInput(['maxlength' => 500,'readonly'=>true])->label(false)->error(false) ?></td>
		  <td align='right'><?= $form->field($model, 'notstateinfo')->dropDownList(Farms::notstateInfo([6,7,8,9,10]))->label(false)->error(false)?></td>
		  <td align='left'><?= $form->field($model, 'notstate')->textInput(['maxlength' => 500,'readonly'=>true])->label(false)->error(false) ?></td>
    </tr>
		<tr>
			<td width=15% align='right'>地产科签字</td>
			<td align='left'><?= $form->field($model, 'groundsign')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
			<td align='right'>农场法人签字</td>
			<td align='left'><?= $form->field($model, 'farmersign')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
			<td align='right'>状态</td>
			<td colspan="3" align='left'><?php if(User::getItemname('法规科','科长')) echo $form->field($model, 'state')->radioList(Farms::getFarmsState([0,1,2,3,4,5]))->label(false)->error(false); else Farms::getStateInfo($model->state); ?></td>
		</tr>
		<tr>
			<td width=15% align='right'>冻结状态</td>
			<td colspan="7" align='left'><?php if($model->locked) echo '已冻结'; else echo '未冻结'; ?></td>
			<?php if(!$model->state) $model->state = 1;?>
		</tr>
	<tr>
			<td width=15% align='right'>备注</td>
			<td colspan="7" align='left'><?= $form->field($model, 'remarks')->textarea(['rows' => 5])->label(false)->error(false) ?></td>
			<?php if(!$model->state) $model->state = 1;?>
		</tr>
	</table>
	<div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	<div id="dialog" title="宗地信息">
		<table width=100%>
			<tr>
				<td align="right">宗地号：</td>
				<td><?= html::textInput('zongdinumber','',['id'=>'zongdi','disabled'=>true])?></td>
			</tr>
			<tr>
				<td align="right">面积：</td>
				<td><?= html::textInput('zongdimeasure','',['id'=>'measure'])?></td>
			</tr>
		</table>
	</div>
	<div id="dialog2" title="宗地信息">
	<?= html::hiddenInput('tempMeasure','',['id'=>'temp-measure'])?>
		<table width=100%>
			<tr>
				<td align="right">宗地号：</td>
				<td><?= html::textInput('findzongdi','',['id'=>'findZongdi'])?></td>
			</tr>
			<tr>
				<td align="right">面积：</td>
				<td><?= html::textInput('findmeasure','',['id'=>'findMeasure'])?></td>
			</tr>
		</table>
	</div>
	<div id="dialog-contractnumber" title="更改合同流水号">
		<?php
		if(Yii::$app->controller->action->id == 'farmsupdate') {
			$contractnumberValue = Farms::getContractserialnumber($model->id);
		} else {
			$contractnumberValue = Farms::getNewContractserialnumber();
		}
		?>
		<table width=100%>
			<tr>
				<td align="right">合同流水号:</td>
				<td><?= html::textInput('contractnumber',$contractnumberValue,['id'=>'contract-number'])?></td>
				<td><?= html::a('自动获取','#',['class'=>'btn btn-default','id'=>'autoNumber'])?></td>
			</tr>
		</table>
	</div>
	<div id="dialogMsg" title="信息">
		<div id="msg" data-info=""></div>
	</div>
	<?php ActiveFormrdiv::end(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
	$( "#dialogMsg" ).dialog({
		autoOpen: false,
		autoFocus: true,
		width: 400,
		buttons: [
			{
				text: "确定",
				id: 'msgyes',
				click: function() {
					$( this ).dialog( "close" );

				}

			},

		],
		focus : function (e, ui) {
			$($("#message").siblings(".ui-dialog-buttonpane").find("button:eq(1)")).focus();
		}
	});
	$('#autoNumber').click(function(){
		$('#contract-number').val('<?= Farms::getNewContractserialnumber()?>');
	});

	$('#farms-state').change(function(){
		var val = $('input:radio[name="Farms[state]"]:checked').val();
		var hth = $('#farms-contractnumber').val();
		var arrayhth = hth.split('-');
		if(val == 1 || val == 0 || val == 5) {
			var newcontractnumber = $('#tempContractNumber').val();
		}
		if(val == 2) {
			var newcontractnumber = $('#contract-number').val() + '-' + 'W' + '-' + arrayhth[2] + '-' + arrayhth[3];
		}
		if(val == 3) {
			var newcontractnumber = $('#contract-number').val() + '-' + 'L' + '-' + arrayhth[2] + '-' + arrayhth[3];
		}
		if(val == 4) {
			var newcontractnumber = $('#contract-number').val() + '-' + 'M' + '-' + arrayhth[2] + '-' + arrayhth[3];
		}
		$('#farms-contractnumber').val(newcontractnumber);

	});
	function getStateInfo()
	{
		var val = $('input:radio[name="Farms[state]"]:checked').val();
		if(val == 2 || val == 3) {
			var area = $('#farms-measure').val()*1 + $('#farms-notclear').val()*1 - $('#farms-notstate').val()*1;
			$('#farms-contractarea').val(area.toFixed(2));
			return false;
		}
		else
			return true;
	}
	$('#farms-management_area').change(function(){
		var input = $(this).val();
		var hth = $('#farms-contractnumber').val();
		var arrayhth = hth.split('-');
		arrayhth[3] = input;
		$('#farms-contractnumber').val(arrayhth.join('-'));
	});
	$('#farms-notstate').keyup(function (event) {
		var input = $(this).val();
		if(/^[0-9]{0}([0-9]|[.])+$/.test(input)) {
			if(event.keyCode == 8) {
				$(this).val('');

				if($('#temp_notclear').val() !== '') {
					var result = $('#farms-measure').val()-$('#temp_notclear').val();
					$('#farms-measure').val(result.toFixed(2));
				}

			}
		} else {
			alert('输入的必须为数字');
			var last = input.substr(input.length-1,1);
			$('#farms-notclear').val(input.substring(0,input.length-1));
		}
	});
	$('#farms-notclear').keyup(function (event) {
		var input = $(this).val();
		if(/^[0-9]{0}([0-9]|[.])+$/.test(input)) {
			if(event.keyCode == 8) {
				$(this).val('');

				if($('#temp_notclear').val() !== '') {
					var result = $('#farms-measure').val()-$('#temp_notclear').val();
					$('#farms-measure').val(result.toFixed(2));
				}

			}
		} else {
			alert('输入的必须为数字');
			var last = input.substr(input.length-1,1);
			$('#farms-notclear').val(input.substring(0,input.length-1));
		}
	});
	$('#farms-notclear').blur(function(){
		if(getStateInfo())
			toHTH();
	});
	function zongdiRemove(zongdi,measure,dialogID)
	{
		removeZongdiForm(zongdi,measure);
		removeNowZongdi(zongdi);
		var contractarea = Number($('#farms-contractarea').val());
		$('#new'+zongdi).remove();
// 	var zongdiarr = zongdi.split('_');

//		if(dialogID == 'dialog') {
			//宗地面积计算开始
//			var newvalue = $('#farms-measure').val()*1 - measure*1;
//			$('#farms-measure').val(newvalue.toFixed(2));
//			$('#temp_measure').val(newvalue.toFixed(2));
			$('#'+zongdi).text($('#'+zongdi).val());
			//如果存在未明确状态面积，那么先减未明确状态面积
		var notstate = Number($('#farms-notstate').val());
		var notclear = Number($('#farms-notclear').val());
		var newvalue = $('#farms-measure').val() * 1 - measure * 1;
		$('#farms-measure').val(newvalue.toFixed(2));
		$('#temp_measure').val(newvalue.toFixed(2));
// 		alert($('#farms-measure').val());
		if($('#farms-measure').val()*1 > contractarea) {
			if(measure > contractarea) {				
				$('#farms-notstate').val(0);
				var newnotclear = contractarea*1 - $('#farms-measure').val()*1 + $('#farms-notstate').val()*1;
				$('#farms-notclear').val(newnotclear.toFixed(2));
			} else {
//				var newvalue = $('#farms-measure').val() * 1 - measure * 1;

				$('#farms-measure').val(newvalue.toFixed(2));
				$('#temp_measure').val(newvalue.toFixed(2));
//				alert(notstate);alert(measure);
				if(notstate == 0) {
					var newnotstate = 0;
					var newnotclear = measure*1 + $('#farms-notclear').val();
					$('#farms-notclear').val(newnotclear.toFixed(2));
				} else {
					if (notstate > measure) {
						var newnotstate = notstate - measure;
					} else {
						var newnewnotclear = measure - notstate;
						var newnotstate = 0;
						$('#farms-notclear').val(newnewnotclear.toFixed(2));
					}
				}
//				alert(newnotstate);
					$('#farms-notstate').val(newnotstate.toFixed(2));

			}
		} else {
// 			var newvalue = $('#farms-measure').val() * 1 - measure * 1;
// 			$('#farms-measure').val(newvalue.toFixed(2));
// 			$('#temp_measure').val(newvalue.toFixed(2));
			if(notstate == 0) {
				var newnotstate = 0;
				var newnotclear = measure*1 + $('#farms-notclear').val()*1;
				$('#farms-notclear').val(newnotclear.toFixed(2));
			} else {
				if (notstate > measure) {
					var newnotstate = notstate - measure;
				} else {
					var newnewnotclear = measure*1 - notstate;
					var newnotstate = 0;
					$('#farms-notclear').val(newnewnotclear.toFixed(2));
				}
			}
//			var newnotclear = contractarea*1 - $('#farms-measure').val()*1 + notstate*1;
//			$('#farms-notclear').val(newnotclear.toFixed(2));
			$('#farms-notstate').val(newnotstate.toFixed(2));
		}





//		}
//		if(dialogID == 'dialog2') {
//			if($('#farms-measure').val()*1 > $('#farms-contractarea').val()*1) {
//				var value =
//			} else {

//				$('#' + zongdi).text($('#' + zongdi).val());
//				如果存在未明确状态面积，那么先减未明确状态面积
//				var notstate = Number($('#farms-notstate').val());
//				var newnotclear = contractarea * 1 - $('#farms-measure').val() * 1 + notstate * 1;
//				$('#farms-notclear').val(newnotclear.toFixed(2));
//			}
//		}

	}
	function removeZongdiForm(zongdi,measure)
	{
		var findzongdi = zongdi + "("+measure+")";
		var zongdi = $('#farms-zongdi').val();

		var arr1 = zongdi.split('、');
		$.each(arr1, function(i,val){
			if(val === findzongdi)
				arr1.splice(i,1);
		});
		var newnewzongdi = arr1.join('、');
		$('#farms-zongdi').val(newnewzongdi);
	}
	function removeNowZongdi(zongdi)
	{
// 		alert(zongdi);
		var nowzongdi = $('#new-zongdi').val();
		var arr1 = nowzongdi.split('、');
		$.each(arr1, function(i,val){
// 			alert(val);
			if(val === zongdi)
				arr1.splice(i,1);
		});
		var newnewzongdi = arr1.join('、');
// 	alert(newnewzongdi);
		$('#new-zongdi').val(newnewzongdi);
// 	return result;
	}
//	function oldZongdiChange(zongdi,measure,state)
//	{
//		var yzongdi = $('#oldzongdiChange').val();
//// 	alert(yzongdi);
//		$.getJSON("<?//= Url::to(['farms/oldzongdichange'])?>//", {yzongdi: yzongdi, zongdi:zongdi,measure:measure,state:state}, function (data) {
//			$('#oldzongdiChange').val(data.zongdi);
//		});
//	}
	function nowZongdiFind(zongdi)
	{
		var result = false;
		var newzongdi = $('#new-zongdi').val();
// 		alert(newzongdi);
		if(newzongdi != '') {
			var arr1 = newzongdi.split('、');
			$.each(arr1, function(i,val){
				if(val === zongdi)
					result = true;
			});
		}
		return result;
	}
	//处理宗地里的"、"号,将最后一个或最前一个删除
	function zongdiForm(zongdi,measure)
	{
		var newfarmszongdi = $('#farms-zongdi').val();
		var zongdistr = zongdi+"("+measure+")";
		$('#farms-zongdi').val(newfarmszongdi +'、'+ zongdistr);
// 	alert(zongdistr);
		var farmszongdi = $('#farms-zongdi').val();
		var first = farmszongdi.substr(0,1);
		var last = farmszongdi.substr(farmszongdi.length-1,1);
		if(first == '、') {
			$('#farms-zongdi').val(farmszongdi.substring(1));
		}
		if(last == '、') {
			$('#farms-zongdi').val(farmszongdi.substring(0,farmszongdi.length-1));
		}

	}

	$( "#dialog" ).dialog({
		autoOpen: false,
		width: 400,
		buttons: [
			{
				text: "确定",
				click: function() {
					var zongdi = $('#zongdi').val();
					var measure = Number($('#measure').val());
					var ymeasure = Number($('#ymeasure').val());
					if(measure == '') {
						alert("对不起，您面积不能为空。");
						$('#measure').val(ymeasure);
					} else {
						$( this ).dialog( "close" );
						zongdiForm(zongdi,measure);
						var newzongdi = zongdi+'('+measure+')';
						var newzongdihtml = '<li class="select2-selection__choice" id="new'+zongdi+'" title="'+newzongdi+'"><span class="remove text-red" role="presentation" onclick=zongdiRemove("'+zongdi+'","'+measure+'","dialog")>×</span>'+newzongdi+'</li>';
						$('.select2-selection__rendered').append(newzongdihtml);
						var newvalue = $('#farms-measure').val()*1 + measure*1;
						$('#farms-measure').val(newvalue.toFixed(2));
						$('#temp_measure').val(newvalue.toFixed(2));
						if(getStateInfo())
							toHTH();
						var ycontractarea = parseFloat($('#farms-contractarea').val());

					}
				}
			},
			{
				text: "取消",
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		]
	});

	$('#dialog2').dialog({
		autoOpen: false,
		modal:true,
//		show:'scale',
//		hide:'clip',
		width:400,

		buttons: [
			{
				text: "确定",
				click: function() {
					
					var zongdi = $('#findZongdi').val();
	  				
					var measure = Number($('#findMeasure').val());
//					var ymeasure = Number($('#ymeasure').val());
					if(measure == '' || zongdi == '') {
						alert("对不起，宗地或面积不能为空。");
						$('#findMeasure').val();
					} else {
//						if(measure > ymeasure) {
//							alert("对不起，您输入的面积不能大于原宗地面积。");
//							$('#findMeasure').val(ymeasure);
//						} else {
						$( this ).dialog( "close" );
						zongdiForm(zongdi,measure);
//								oldZongdiChange(zongdi,measure,'change');
						var newzongdi = zongdi+'('+measure+')';
						var newzongdihtml = '<li class="select2-selection__choice" id="new'+zongdi+'" title="'+newzongdi+'"><span class="remove text-red" role="presentation" onclick=zongdiRemove("'+zongdi+'","'+measure+'","dialog2")>×</span>'+newzongdi+'</li>';

						$('.select2-selection__rendered').append(newzongdihtml);
						$('#'+zongdi).attr('disabled',true);
						var newvalue = $('#farms-measure').val()*1 + measure*1;
						$('#farms-measure').val(newvalue.toFixed(2));
						$('#temp_measure').val(newvalue.toFixed(2));
// 								var oldcontractnumber = $('#farms-contractarea').val();
// 								var newcontractnumber = oldcontractnumber*1 - measure*1;
// 								$('#farms-contractarea').val(newcontractnumber.toFixed(2));
// 								alert($('#farms-contractarea').val());
// 								alert($('#farms-measure').val());
						var notclear = Number($('#farms-notclear').val());
						var farmMeasure = Number($('#farms-measure').val());
// 						alert(notclear);alert(farmMeasure);
						if(notclear > 0) {
							var newnotclear = notclear*1 - measure*1;
// 							alert(newnotclear);
							$('#farms-notclear').val(newnotclear.toFixed(2));
							
						}

							if($('#farms-measure').val()*1 > $('#farms-contractarea').val()*1) {
// 								alert($('#farms-measure').val());
								var oldnotstate = $('#farms-notstate').val();
								var newnotstate = $('#farms-measure').val()*1 - $('#farms-contractarea').val()*1;
								alert(newnotstate);
								$('#farms-notclear').val(0);
								$('#farms-notstate').val(newnotstate.toFixed(2));
							}
				
// 								var ycontractarea = parseFloat($('#farms-contractarea').val());

						var newtempzongdi = $('#new-zongdi').val();
						$("#new-zongdi").val(zongdi+'、'+newtempzongdi);
//								$('#ymeasure').val(0);
						$('#findZongdi').val('');
						$('#findMeasure').val('');

					}

//					}
				}
			},
			{
				text: "取消",
				click: function() {
// 					alert(33444);
					$('#findZongdi').val('');
					$('#findMeasure').val('');
					$( this ).dialog( "close" );
				}
			}
		]
	});

//	$('#farms-contractnumber').click(function(){
//		$( "#dialog-contractnumber" ).dialog( "open" );
//	});

	$('#dialog-contractnumber').dialog({
		autoOpen: false,
		width:400,

		buttons: [
			{
				text: "确定",
				click: function() {
					var hth = $('#farms-contractnumber').val();
					var arrayhth = hth.split('-');
					var newcontractnumber = $('#contract-number').val() + '-' + arrayhth[1] + '-' + arrayhth[2] + '-' + arrayhth[3];
					$('#farms-contractnumber').val(newcontractnumber);
					$( this ).dialog( "close" );
				}
			},
			{
				text: "取消",
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		]
	});

	$('#inputZongdi').dblclick(function(){
			$("#dialogSelect").val('dialog2');
			$("#dialog2").dialog("open");
			$('#findZongdi').val('');
			$('#findMeasure').val('');
	});
	//点击宗地输入框弹出宗地信息查找框
	$('#zongdiinfo').click(function(){
		$("#dialogSelect").val('dialog');
		$( "#dialog2" ).dialog( "open" );
//			$('#findZongdi').val('');
//			$('#findMeasure').val('');
	});
	$('#findZongdi').keyup(function (event) {
		var input = $(this).val();
		if(event.keyCode == 13) {
			if(nowZongdiFind(input)){
				alert('您已经输入过此宗地号，请不要重复输入');
				$('#findZongdi').val('');
				$('#findMeasure').val('');
				$('#temp-measure').val('');
			} else {
				$.getJSON("<?= Url::to(['parcel/parcelarea'])?>", {zongdi: input,farms_id:<?= $_GET['id']?>}, function (data) {
					if (data.status == 1) {
// 					if(data.showmsg) {
// 						$("#msg").text("");
// 						$('#msg').append(data.message);
// 						$("#dialogMsg").dialog("open");
// 					}
						$('#findMeasure').val(data.area);
						$('#temp-measure').val(data.area);
						$('#ymeasure').val(data.area);
						$("#findMeasure").focus();
					}
					else {
						if(input != '') {
							if(data.showmsg) {
								$("#msg").text("");
								$('#msg').append(data.message);
								$("#dialogMsg").dialog("open");

							}
							$("#findZongdi").val('');
							$('#findMeasure').val('');
							$('#temp-measure').val('');
							$("#findZongdi").focus();
						}
					}
				});
			}
		}
	});
	$('#findMeasure').keyup(function (event) {
		var input = $(this).val()*1;
		var zongdiarea = $('#temp-measure').val()*1;
		if(/^[0-9]{0}([0-9]|[.])+$/.test(input)) {
// 			alert(input+'-'+zongdiarea);
			if(input > zongdiarea) {
				alert('对不起，不能输入超过宗地面积的数值');
				$(this).val(zongdiarea);
			}
		}
		if(event.keyCode == 13) {
			var zongdi = $('#findZongdi').val();
// 	  				alert(zongdi);
			var measure = Number($('#findMeasure').val());
//					var ymeasure = Number($('#ymeasure').val());
			if(measure == '' || zongdi == '') {
				alert("对不起，宗地或面积不能为空。");
				$('#findMeasure').val();
			} else {
				$( "#dialog2" ).dialog( "close" );
				zongdiForm(zongdi,measure);
//								oldZongdiChange(zongdi,measure,'change');
				var newzongdi = zongdi+'('+measure+')';
				var newzongdihtml = '<li class="select2-selection__choice" id="new'+zongdi+'" title="'+newzongdi+'"><span class="remove text-red" role="presentation" onclick=zongdiRemove("'+zongdi+'","'+measure+'","dialog2")>×</span>'+newzongdi+'</li>';

				$('.select2-selection__rendered').append(newzongdihtml);
				$('#'+zongdi).attr('disabled',true);
				var newvalue = $('#farms-measure').val()*1 + measure*1;
				$('#farms-measure').val(newvalue.toFixed(2));
				$('#temp_measure').val(newvalue.toFixed(2));
// 								var oldcontractnumber = $('#farms-contractarea').val();
// 								var newcontractnumber = oldcontractnumber*1 - measure*1;
// 								$('#farms-contractarea').val(newcontractnumber.toFixed(2));
// 								alert($('#farms-contractarea').val());
// 								alert($('#farms-measure').val());
				var notclear = Number($('#farms-notclear').val());
				var farmMeasure = Number($('#farms-measure').val());
//						alert(notclear);alert(farmMeasure);
				if(notclear >0) {
					var newnotclear = notclear*1 - measure*1;
//							alert(newnotclear);
					$('#farms-notclear').val(newnotclear.toFixed(2));

				}

				if($('#farms-measure').val()*1 > $('#farms-contractarea').val()*1) {

					var oldnotstate = $('#farms-notstate').val();
					var newnotstate = $('#farms-measure').val()*1 - $('#farms-contractarea').val()*1;
					$('#farms-notclear').val(0);
					$('#farms-notstate').val(newnotstate.toFixed(2));
				}

// 								var ycontractarea = parseFloat($('#farms-contractarea').val());

				var newtempzongdi = $('#new-zongdi').val();
				$("#new-zongdi").val(zongdi+'、'+newtempzongdi);
//								$('#ymeasure').val(0);
				$('#findZongdi').val('');
				$('#findMeasure').val('');

			}
		}
	});
	$('#findZongdi').blur(function (event) {
		var input = $(this).val();
		if(input != '') {
			if(nowZongdiFind(input)){
				alert('您已经输入过此宗地号，请不要重复输入');
				$('#findZongdi').val('');
				$('#findMeasure').val('');
				$('#temp-measure').val('');
			} else {
				$.getJSON("<?= Url::to(['parcel/parcelarea'])?>", {zongdi: input,farms_id: <?= $model->id?>}, function (data) {
					if (data.status == 1) {
						if(data.showmsg) {
							$("#msg").text("");
							$('#msg').append(data.message);
							$("#dialogMsg").dialog("open");
						}
						$('#findMeasure').val(data.area);
						$('#temp-measure').val(data.area);
						$('#ymeasure').val(data.area);

					}
					else {
						if(input != '') {
							if(data.showmsg) {
								$("#msg").text("");
								$('#msg').append(data.message);
								$("#dialogMsg").dialog("open");
							}
							$("#findZongdi").val('');
							$('#findMeasure').val('');
							$('#temp-measure').val('');
							$("#findZongdi").focus();
						}
					}
				});
			}
		}
	});
	// Link to open the dialog
	$( ".dialog-link" ).click(function( event ) {
		$("#dialogSelect").val('dialog1');
		$( "#dialog" ).dialog( "open" );

		event.preventDefault();
	});
	function resetZongdi(zongdi,area)
	{
		$('#'+zongdi).attr('disabled',false);
		var oldmeasure = $('#oldfarms-measure').val()*1 + area*1;
		$('#oldfarms-measure').val(oldmeasure.toFixed(2));
	}
	function getArea(zongdi)
	{
		re = /-([\s\S]*)\(([0-9.]+?)\)/
		var area = zongdi.match(re);
		return area[2];

	}

	function toZongdi(zongdi,area){
		$( "#dialog" ).dialog( "open" );
		event.preventDefault();
		$('#zongdi').val(zongdi);
		$('#measure').val(area);
		$('#ymeasure').val(area);
	}
	$('#reset').click(function() {

		location.reload();

	});
	// 	$('#farms-notclear').blur(function(){
	// 		var input = $(this).val();
	// 		if(input*1 > $('#temp_oldnotclear').val()*1) {

	// 			alert('输入的数值不能大于'+$('#temp_oldnotclear').val());
	// 			$('#oldfarms-notclear').val($('#temp_oldnotclear').val());
	// 			$(this).val(0);
	// 			$('#farms-notclear').val(0);
	// 			$(this).focus();
	// 			toHTH();
	// 		}});

	// 	$('#farms-notclear').keyup(function (event) {
	// 		var input = $(this).val();
	// 		if(event.keyCode == 8) {
	// 			$(this).val('');
	// 			$('#farms-notclear').val($('#temp_notclear').val());
	// 			$('#oldfarms-notclear').val($('#temp_oldnotclear').val());

	// 			$('#temp_oldcontractarea').val($('#oldfarms-contractarea').val());

	// 		} else {
	// 			if(/^[0-9]{0}([0-9]|[.])+$/.test(input)) {
	// 				if($('#temp_notclear').val() != '') {
	// 					var result = $('#temp_oldnotclear').val()*1 - input*1;
	// 					$('#oldfarms-notclear').val(result.toFixed(2));
	// 					$('#farms-notclear').val(input);
	// 					toHTH();
	// 				} else {
	// 					var result = $('#temp_oldcontractarea').val()*1 - input*1
	// 					$('#oldfarms-notclear').val(result.toFixed(2));
	// 					$('#farms-notclear').val(input);
	// 					toHTH();
	// 				}
	// 			} else {
	// 				alert('输入的必须为数字');
	// 				var last = input.substr(input.length-1,1);
	// 				$('#input-notclear').val(input.substring(0,input.length-1));

	// 			}
	// 		}
	// 		toHTH();
	// 	});

</script>


