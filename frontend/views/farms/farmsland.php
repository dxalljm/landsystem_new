<?php
namespace frontend\controllers;
use app\models\Lockstate;
use app\models\Mainmenu;
use app\models\Tables;
use frontend\helpers\Dialog;
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
use frontend\helpers\arraySearch;
use frontend\helpers\grid\GridView;
use frontend\helpers\Echarts;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\farmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'farms';
$this->title = Tables::find()->where(['tablename'=>$this->title])->one()['Ctablename'];
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript" src="vendor/bower/CircleLoader/jquery.shCircleLoader-min.js"></script>
<link href="/vendor/bower/CircleLoader/jquery.shCircleLoader.css" rel="stylesheet">
<?php
	$totalData = clone $dataProvider;
	$totalData->pagination = ['pagesize'=>0];
// 	var_dump($totalData->getModels());exit;
//	$data = arraySearch::find($totalData)->search();
$arrclass = explode('\\',$dataProvider->query->modelClass);
//'<tr>
//						<td></td>
//						<td align="center"><strong>合计</strong></td>
//						<td><strong>'.$data->count('id').'户</strong></td>
//						<td><strong>'.$data->count('farmer_id').'个</strong></td>
//						<td></td>
//						<td></td>
//						<td><strong>'.$data->sum('contractarea').'亩</strong></td>
//						<td></td>
//					</tr>',
?>
<div class="farms-index">

<?php  //echo $this->render('farms_search', ['model' => $searchModel]); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
			<?php User::tableBegin($this->title);?>
			<?php
			if(User::getItemname('信息科'))
				echo Html::a('XLS导出','#',['class'=>'btn btn-success','onclick'=>'dialog()']);
			?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'total' => '<tr height="40">
                                        <td></td>	
                                        <td align="left"><strong>合计</strong></td>
                                        <td align="left" id="t1"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t2"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t3"></td>
                                        <td align="left" id="t4"></td>
                                        <td align="left" id="t5"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td></td>
                                    </tr>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
// 			'id',
            [
            	'attribute' => 'management_area',
            	'headerOptions' => ['width' => '200'],
				'value'=> function($model) {
				     return ManagementArea::getAreanameOne($model->management_area);
				 },
				 'filter' => ManagementArea::getAreaname(),     //此处我们可以将筛选项组合成key-value形式
            ],
            [
            	'attribute' => 'farmname',

            ],
            'farmername',
//             [
//             	'label' => '管理区',
//               	'attribute' => 'areaname',      						
//             	'value' => 'managementarea.areaname',
//             ],
			//'management_area',
			[
				'attribute' => 'address',
				'options' => ['width'=>'300'],
			],
//			'address',
			'telephone',
            'contractarea',
            'contractnumber',
			
			[
				'attribute' => 'create_at',
				'value' => function($model) {
//					var_dump($model->state);
					return date('Y-m-d',$model->create_at);
				}
			],
			[
				'attribute' => 'update_at',
				'value' => function($model) {
//					var_dump($model->state);
					return date('Y-m-d',$model->update_at);
				}
			],
//			'state',
             [
            	'attribute'=> 'state',
             	'value' => function ($model) {
//					var_dump($model->state);exit;
             		return Farms::getStateInfo($model->state);
             },
             'filter' => [0=>'销户',1=>'正常',2=>'未更换合同',3=>'临时性管理',4=>'买断合同',5=>'其它'],
             ],
            [
	            'label'=>'更多操作',
	             'format'=>'raw',
    			'options' => ['width'=>'180'],
				            	//'class' => 'btn btn-primary btn-lg',
				                'value' => function($model,$key){
					                $option = '查看详情';
				                	$url = Url::to(['farms/farmslandview','id'=>$model->id]);               	
				                    $html = Html::a($option,$url, [
						            			'id' => 'moreOperation',
						            			'class' => 'btn btn-primary btn-xs',
// 				                    			'disabled' => $disabled,
						            	]);
// 				                    var_dump(User::getItemname());
									if(User::getItemname('法规科') or User::getItemname('服务大厅')) {
// 										$farmer = Farmer::find()->where(['farms_id'=>$model->id])->one();
// 										if($farmer['photo'] == '' or $farmer['cardpic'] == '' or $farmer['cardpicback'] == '') {
										$html.= '&nbsp;';
										$html.= Html::a('电子信息采集',Url::to(['photograph/photographindex','farms_id'=>$model->id]),['class' => 'btn btn-primary btn-xs',]);
//										$html.= '&nbsp;';
//										$html.= Html::a('导出证件',Url::to(['photograph/photographexplode','farms_id'=>$model->id]),['class' => 'btn btn-primary btn-xs',]);
// 										}
									}
									if(User::getItemname('法规科') or User::getItemname('地产科')) {
										$url = Url::to(['farms/farmsadminupdate','id'=>$model->id]);
										$html.= '&nbsp;';
										$html .= Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
											'title' => Yii::t('yii', '更新'),
											'data-pjax' => '0',
										]);
									}
									if(Yii::$app->user->identity->id == '15') {
										$html .= '&nbsp;';
										if(Lockstate::isLoanLocked($model->id)['state']) {
											if (Lockstate::isUnlockloan($model->id)) {
												$html .= Html::a('临时解锁', '#', ['class' => 'btn btn-primary btn-xs', 'disabled' => 'disabled']);
											} else {
												$html .= Html::a('临时解锁', Url::to(['lockstate/lockstateunset', 'farms_id' => $model->id]), ['class' => 'btn btn-primary btn-xs',]);
											}
										} else {
											$html .= Html::a('临时解锁', '#', ['class' => 'btn btn-primary btn-xs', 'disabled' => 'disabled']);
										}
									}
// 									var_dump(User::getItemname('主任'));exit;
					            	if(User::getItemname('管委会领导') or User::getItemname('法规科') or User::getItemname('地产科') or User::getItemname('服务大厅')) {
						            	return $html;
					            	}
					            	else 
					            		return '';
				                }
				            ],
        ],
    ]); ?>
			<?php
			//var_dump(json_encode($dataProvider->query->where));exit;
			echo Dialog::show($arrclass[2],json_encode($dataProvider->query->where));
			?>
			<?php
//			$sdata1 = json_decode('[{"value":10, "name":"rose1"},{"value":5, "name":"rose2"},{"value":15, "name":"rose3"},{"value":25, "name":"rose4"},{"value":20, "name":"rose5"},{"value":35, "name":"rose6"},{"value":30, "name":"rose7"},{"value":40, "name":"rose8"}]',true);
//			$sdata2 = json_decode('[{"value":10, "name":"rose1"},{"value":5, "name":"rose2"},{"value":15, "name":"rose3"},{"value":25, "name":"rose4"},{"value":20, "name":"rose5"},{"value":35, "name":"rose6"},{"value":30, "name":"rose7"},{"value":40, "name":"rose8"}]',true);
//			$this->registerJs(ES::wdj()->DOM('test')->options(['title'=>'测试','legend'=>['总数','已销售'],'xAxis'=>["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"],'unit'=>'元','yAxis'=>[],'series'=>[[60,60,60,60,60,60],[5, 20, 36, 10, 10, 20]]])->JS());
//			$this->registerJs(ES::pie()->DOM('test2')->options(['title'=>'某站点用户访问来源','legend'=>['直接访问','邮件营销','联盟广告','视频广告','搜索引擎'],'series'=>[['value'=>335, 'name'=>'直接访问'], ['value'=>310, 'name'=>'邮件营销'], ['value'=>234, 'name'=>'联盟广告'], ['value'=>135, 'name'=>'视频广告'], ['value'=>1548, 'name'=>'搜索引擎']]])->JS());
//			$this->registerJs(ES::pie2()->DOM('text3')->options(['title'=>'南丁格尔玫瑰图','subtext'=>'虚构','legend'=>['rose1','rose2','rose3','rose4','rose5','rose6','rose7','rose8'],'series'=>['name'=>['半径模式','面积模式'],'data'=>[$sdata1,$sdata2]]])->JS());
//			$this->registerJs(ES::bar()->DOM('test')->settitle('测试')->xAxis(["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"])->series('销量',[5, 20, 36, 10, 10, 20])->JS());
//			$this->registerJs(ES::pie()->DOM('text2')->settitle('某站点用户访问来源')->legendData(['直接访问','邮件营销','联盟广告','视频广告','搜索引擎'])->series('访问来源',[['value'=>335, 'name'=>'直接访问'], ['value'=>310, 'name'=>'邮件营销'], ['value'=>234, 'name'=>'联盟广告'], ['value'=>135, 'name'=>'视频广告'], ['value'=>1548, 'name'=>'搜索引擎']])->JS())
//			$this->registerJs(ES::barGroup()->DOM('test4')->options(['dataset'=>[['product','2015','2016','2017'],['aaa',123,231,234],['bbb',24,254,234],['ccc',423,54,321]]])->JS());
//			$this->registerJs(ES::barLabel()->DOM('test5')->options(['color'=>['#003366', '#006699', '#4cabce', '#e5323e'],'legend'=>['投入品1','投入品2','投入品3','农药1'],'xAxis'=>['小麦','玉米','大豆','杂豆','马铃薯'],'series'=>[[12,53,24,54,43],[65,34,26,34,23],[64,34,24,34,43],[36,54,26,76,14],[54,24,54,23,54]]])->JS());
			?>
                <?php User::tableEnd();?>
        </div>
    </div>
</section>
</div>

<script>

	$('.shclDefault').shCircleLoader({color: "red"});
	$(document).ready(function () {
		$.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-id'}, function (data) {
			$('#t1').html(data + '户');
		});
		$.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-farmer_id'}, function (data) {
			$('#t2').html(data + '人');
		});
		$.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'sum-contractarea'}, function (data) {
			$('#t5').html(data + '亩');
		});
	});
</script>