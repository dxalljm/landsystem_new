<?php

use app\models\Tables;
use frontend\helpers\grid\GridView;
use app\models\Farms;
use app\models\Subsidiestype;
use app\models\Plant;
use app\models\ManagementArea;
use dosamigos\datetimepicker\DateTimePicker;
use app\models\Search;
use frontend\helpers\arraySearch;
use app\models\Huinong;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\leaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
              
   <?= $this->render('..//search/searchindex',['tab'=>$tab,'begindate'=>$begindate,'enddate'=>$enddate,'params'=>$params]);?>
<?php 
	$totalData = clone $dataProvider;
	$totalData->pagination = ['pagesize'=>0];
	$data = arraySearch::find($totalData)->search();
	$namelist = $data->getName('Subsidiestype', 'typename', ['Huinong','huinong_id','subsidiestype_id'])->getList();
$arrclass = explode('\\',$dataProvider->query->modelClass);
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-pills-warning">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">数据表</a></li>
              <?php foreach(Huinong::getTypename() as $key => $value) {
//               	var_dump($value);exit;
              			echo '<li class=""><a href="#huinongview'.$key.'" data-toggle="tab" aria-expanded="false">'.$value.'图表</a></li>';
			  		}
			  	?>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">
               <?= GridView::widget([
			        'dataProvider' => $dataProvider,
			        'filterModel' => $searchModel,
                   'total' => '<tr height="40">
                                        <td></td>
                                        <td align="left" id="t0"><strong>合计</strong></td>
                                        <td align="left" id="t1"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t2"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t3"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td></td>
                                        <td align="left" id="t4"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t5"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                                        <td align="left" id="t6"><strong><div class="shclDefault" style="width: 25px; height: 25px;"></div></strong></td>
                        
                                    </tr>',
                   'columns' => [
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
                           'label' => '承租人',
                           'attribute' => 'lease_id',
                           'value' => function($model) {
                               return \app\models\Lease::find()->where(['id'=>$model->lease_id])->one()['lessee'];
                           }
                       ],
                       [
                           'label' => '补贴种类',
                           'attribute' => 'subsidiestype_id',
                           'value' => function ($model) {
                               return Subsidiestype::find()->where(['id'=>$model->subsidiestype_id])->one()['typename'];
                           },
                           'filter' => Subsidiestype::getTypelist(),

                       ],
                       [
                           'label' => '作物',
                           'attribute' => 'typeid',
                           'value' => function($model) {
                               $sub = Subsidiestype::find()->where(['id'=>$model->subsidiestype_id])->one()['urladdress'];

                               $classFile = 'app\\models\\'. $sub;
                               $data = $classFile::find()->where(['id'=>$model->typeid])->one();
                               if($sub == 'Plant')
                                   return $data['typename'];
                               if($sub == 'Goodseed') {
                                   $plant = Plant::find()->where(['id'=>$data['plant_id']])->one();
                                   return $plant['typename'].'/'.$data['typename'];
                               }
                           },
                           'filter' => Plant::getAllname($totalData),
                       ],
                       'money',
                       'area',
                   ],
			    ]); ?>
              </div>
              <?php foreach(Huinong::getTypename() as $key => $value) {
              $classname = 'huinong'.$key;
              	?>
              <!-- /.tab-pane -->
              <div class='tab-pane' id="huinongview<?= $key?>">
              <div id="<?= $classname?>" style="width:1000px; height: 600px; margin: 0 auto"></div>
				<?php $echartsData = $data->getName('Subsidiestype', 'typename', 'subsidiestype_id')->huinongShowShadow($key);?>
              </div>
              <script type="text/javascript">
              wdjHuinong('<?= $classname?>',<?= json_encode(['实发金额','应发金额'])?>,<?= json_encode(Farms::getManagementArea('small')['areaname'])?>,<?= json_encode($echartsData['all'])?>,<?= json_encode($echartsData['real'])?>,'元');
			</script>
              <!-- /.tab-pane -->
            <!-- /.tab-content -->
            <?php }?>
          </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
<script>
    $('.shclDefault').shCircleLoader({color: "red"});
    $(document).ready(function () {
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-farmer_id'}, function (data) {
            $('#t1').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-lease_id'}, function (data) {
            $('#t2').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-lease_id'}, function (data) {
            $('#t3').html(data + '人');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'count-huinong_id'}, function (data) {
            $('#t4').html(data + '个');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'sum-money'}, function (data) {
            $('#t5').html(data + '元');
        });
        $.getJSON('index.php?r=search/search', {modelClass: '<?= $arrclass[2]?>',where:'<?= json_encode($dataProvider->query->where)?>',command:'sum-area'}, function (data) {
            $('#t6').html(data + '亩');
        });

    });
</script>
		