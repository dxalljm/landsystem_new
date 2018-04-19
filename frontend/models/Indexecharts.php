<?php

namespace app\models;

use Yii;
use app\models\Cache;
use app\models\Collectionsum;
use yii\helpers\ArrayHelper;
use app\models\Farms;
use app\models\Projectapplication;
use yii\helpers\Url;
/**
 * This is the model class for table "{{%indexecharts}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $onem
 * @property integer $oner
 * @property integer $twol
 * @property integer $twom
 * @property integer $twor
 */
class Indexecharts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%indexecharts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'onem', 'oner', 'twol', 'twom', 'twor'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'user_id' => '用户ID',
            'onem' => '第一行中间',
            'oner' => '第一行右侧',
            'twol' => '第二行左侧',
            'twom' => '第二行中间',
            'twor' => '第二行右侧',
        ];
    }

//    public static function showEcharts($id,$position,$width='100%',$height='300px')
//    {
//    	if(empty($id))
//    		return '';
//        $html = '';
//        $data = self::echartData($id);
////         var_dump($data);exit;
//        $plate = User::getPlate()['id'];
//        if(count($plate) <= 2) {
//            $html .= '<div class="col-md-6">';
//        } elseif(count($plate == 3)) {
//            $html .= '<div class="col-md-4">';
//        }
//
//		if(empty($data)) echo $id;
//        $html .= '<div class="box box-widget">';
//        $html .= '<div class="box-header with-border">';
//        $html .= '<div class="user-block">';
//        $html .= '<span class="username"><a href="#">'.$data['title'].'</a></span>';
//        $html .= '<span class="description navbar-left">'.$data['ltitle'].'</span>';
//        $html .= '<span class="description navbar-right">'.$data['dw'].'</span>';
//        $html .= '</div>';
//        $html .= '<div class="box-tools">';
//        $html .= '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>';
//        $html .= '</button>';
//        $html .= '</div></div>';
//        $html .= '<div class="box-body">';
//        $html .= '<div id="'.$position.'" style="width:'.$width.';height:'.$height.'"></div>';
//        $html .= '<script type="text/javascript">';
//        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
//        switch ($plantarr[$id]) {
//            case '承包费收缴':
//                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//                break;
//            case '保险业务':
//            	$html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//            	break;
//            case '防火工作':
//           		$html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//           		break;
//           case '宜农林地':
//           		$html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
//           		break;
//            default:
//                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].',"'.$data['echartdw'].'");';
//        }
//
//        $html .= '</script>';
//        $html .= '</div></div></div>';
//        return $html;
//    }

    public static function showEcharts($id,$position,$width='100%',$height='300px')
    {
        if(empty($id))
            return '';
        $html = '';
        $data = self::echartData($id);
//         var_dump($data['cache']);exit;
        $plate = User::getPlate()['id'];
        if(count($plate) <= 2) {
            $html .= '<div class="col-md-6">';
        } elseif(count($plate == 3)) {
            $html .= '<div class="col-md-4">';
        }

        if(empty($data)) echo $id;
        switch (Yii::$app->user->identity->template) {
            case 'default':
                $html .= '<div class="box box-widget">';
                $html .= '<div class="box-header with-border">';
                $html .= '<div class="user-block">';
                $html .= '<span class="username"><a href="#">'.$data['title'].'</a></span>';
                $html .= '<span class="description navbar-left">'.$data['ltitle'].'</span>';
                $html .= '<span class="description navbar-right">'.$data['dw'].'</span>';
                $html .= '</div>';
                $html .= '<div class="box-tools">';
                $html .= '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>';
                $html .= '</button>';
                $html .= '</div></div>';
                $html .= '<div class="box-body">';
                $html .= '<div id="'.$position.'" style="width:'.$width.';height:'.$height.'"></div>';
//                $html .= '<script type="text/javascript">';
//                $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
//                switch ($plantarr[$id]) {
//                    case '承包费收缴':
//                        $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//                        break;
//                    case '保险业务':
//                        $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//                        break;
//                    case '防火工作':
//                        $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
//                        break;
//                    case '宜农林地':
//                        $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
//                        break;
//                    default:
//                        $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].',"'.$data['echartdw'].'");';
//                }
//
//                $html .= '</script>';
                $html .= '</div></div></div>';
                break;
            case 'template2018':

                $html .= '<div class="card card-chart" data-count="1">';
                $html .= '<div class="card-header" data-background-color="white" data-header-animation="true">';
                $html .= '<div class="ct-chart" id="'.$position.'" style="width:'.$width.';height:'.$height.'"></div>';
                $html .= '</div>';
                $html .= '<div class="card-content">';
                $html .= '<div class="card-actions">';
//                $html .= '<button class="btn btn-danger btn-simple fix-broken-card" type="button">';
//                $html .= '<i class="material-icons">build</i> Fix Header!';
//                $html .= '</button>';
                $html .= '<button class="btn btn-info btn-simple" data-placement="bottom" rel="tooltip" title="" type="button" data-original-title="Refresh" onClick=refresh('.$id.',"'.$position.'")>';
                $html .= '<i class="fa fa-refresh">&nbsp;&nbsp;刷新</i>';
                $html .= '</button>';
//                $html .= '<button class="btn btn-default btn-simple" data-placement="bottom" rel="tooltip" title="" type="button" data-original-title="Change Date">';
//                $html .= '<i class="material-icons">edit</i>';
//                $html .= '</button>';
                $html .= '</div>';
                $html .= '<h4 class="card-title">'.$data['title'].'</h4>';
                $html .= '<p class="category">';
                $html .= $data['ltitle'].'</p>';
                $html .= '</div>';
                $html .= '<div class="card-footer">';
                $html .= '<div class="stats">';
                $html .= '上次更新时间:'.date('Y年m月d日 H时i分s秒',$data['time']);
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                break;
        }
        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
        $html .= '<script type="text/javascript">';
        switch ($plantarr[$id]) {
            case '承包费收缴':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
                break;
            case '保险业务':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
                break;
            case '防火工作':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['part'].','.$data['real'].',"'.$data['echartdw'].'");';
                break;
            case '宜农林地':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
                break;
            default:
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
        }
        $html .= '</script>';
        return $html;
    }

    public static function showScript($id,$position)
    {
        $html = '';
        $data = self::echartData($id);
        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
        $html .= '<script type="text/javascript">';
//        $html .= 'alert("test333");';
        switch ($plantarr[$id]) {
            case '承包费收缴':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data[0].','.$data[1].',"'.$data['echartdw'].'");';
                break;
            case '保险业务':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['real'].',"'.$data['echartdw'].'");';
                break;
            case '防火工作':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['all'].','.$data['part'].','.$data['real'].','.$data['echartdw'].');';
                break;
            case '宜农林地':
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
                break;
            default:
                $html .= $data['jsfun'].'("'.$position.'",'.$data['name'].','.$data['echartTitle'].','.$data['cache'].','.$data['echartdw'].');';
        }
        $html .= '</script>';
        return $html;
    }

    public static function echartData($id)
    {
        $result = [];
        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');
        $cache = Cache::getCache(\Yii::$app->getUser()->getId());
//        var_dump($cache);
        switch ($plantarr[$id]) {
            case '宜农林地':
//                var_dump(json_decode($cache['farmscache']));
                $result['title'] = '农场情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['farmstitle'];
                $result['dw'] = '亩|户&nbsp;&nbsp;';
                $result['jsfun'] = 'showBar';
                $result['name'] = json_encode(['面积','数量']);
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['cache'] = $cache['farmscache'];
                $result['echartdw'] = json_encode(['面积'=>'亩','数量'=>'户']);
                $result['time'] = $cache['farmstime'];
                break;
            case '精准农业':
                $result['title'] = '种植结构情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['plantingstructuretitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['plantingstructurecategories'];
                $result['cache'] = $cache['plantingstructurecache'];
                $result['echartdw'] = json_encode('亩');
                $result['name'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['jsfun'] = 'showAllShadow';
                $result['time'] = $cache['plantingstructuretime'];
                break;
            case '农产品':
                $result['title'] = '农产品情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['plantinputproducttitle'];
                $result['dw'] = '单位（斤)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['plantinputproductcategories'];
                $result['cache'] = $cache['plantinputproductcache'];
                $result['echartdw'] = json_encode('斤');
                $result['name'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['jsfun'] = 'showAllShadow';
                $result['time'] = $cache['plantinputproducttime'];
                break;
            case '惠农政策':
                $result['title'] = '补贴发放情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['huinongtitle'];
                $result['dw'] = '单位（元)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['huinongcategories'];
                $result['cache'] = $cache['huinongcache'];
                $result['echartdw'] = json_encode('元');
                $result['name'] = json_encode(['应发','实发']);
                $result['jsfun'] = 'showShadow';
                $result['time'] = $cache['huinongtime'];
                break;
            case '承包费收缴':
//                var_dump($cache);exit;
                $c = json_decode($cache['collectioncache']);
//                var_dump($c);exit;
                if($c) {
                    if(isset($c->all)) {
                        $all = $c->all;
                    } else {
                        $all = [];
                    }
                    if(isset($c->real)) {
                        $real = $c->real;
                    } else {
                        $real = [];
                    }

                } else {
                    $all = [];
                    $real = [];
                }
                $result['title'] = '承包费收缴情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['collectiontitle'];
                $result['dw'] = '单位（元)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['all'] = json_encode($all);
                $result['real'] = json_encode($real);
                $result['echartdw'] = '元';
                $result['name'] = json_encode(['实收金额','应收金额']);
                $result['jsfun'] = 'wdjShowEchart';
                $result['time'] = $cache['collectiontime'];
                break;
            case '防火工作':
                $result['title'] = '防火工作情况统计表('.User::getYear().')';
                $data = json_decode($cache['firecache']);
//                 var_dump($data);exit;
                $result['ltitle'] = $cache['firetitle'].'%';
                $result['dw'] = '单位（户)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
//                 $result['cache'] = $cache['firecategories'];
//                 $result['echartdw'] = '亩';
//                 $result['name'] = json_encode(['面积','数量']);
//                 $result['jsfun'] = 'showAllShadow';
                $all = [];
                $real = [];
                $part = [];
                if($data) {
                    $all = $data->all;
                    $part = $data->part;
                    $real = $data->real;
                }
                $result['all'] = json_encode($all);
                $result['part'] = json_encode($part);
                $result['real'] = json_encode($real);
                $result['echartdw'] = '户';
                $result['name'] = json_encode(['已完成','部分完成','应完成']);
                $result['jsfun'] = 'wdjShowEchartFire';
                $result['time'] = $cache['firetime'];
                break;
            case '畜牧业':
//                var_dump(json_decode($cache['breedinfocache']));
//                var_dump(json_decode($cache['breedinfocategories']));
                $result['title'] = '畜牧业情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['breedinfotitle'];
                $result['dw'] = '单位（只/头/匹)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['breedinfocategories'];
//                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['cache'] = $cache['breedinfocache'];
                $result['echartdw'] = $cache['breedinfodw'];
                $result['name'] = json_encode(['畜牧养殖']);
                $result['jsfun'] = 'showAllShadow';
                $result['time'] = $cache['breedinfotime'];
                break;
            case '项目申报':
                $result['title'] = '项目情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['projectapplicationtitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['projectapplicationcategories'];
                $result['cache'] = $cache['projectapplicationcache'];
                $result['echartdw'] = json_encode('亩');
                $result['name'] = json_encode(['面积','数量']);
                $result['jsfun'] = 'showAllShadowProject';
                $result['time'] = $cache['projectapplicationtime'];
                break;
            case '保险业务':
                $result['title'] = '保险业务情况统计表('.User::getYear().')';
                $data = json_decode($cache['insurancecache']);
                $result['ltitle'] = $cache['insurancetitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $all = [];
                $real = [];
                if($data) {
                    $all = $data->all;
                    $real = $data->real;
                }
//                 var_dump($data->all);exit;
                $result['all'] = json_encode($all);
                $result['real'] = json_encode($real);
                $result['echartdw'] = '亩';
                $result['name'] = json_encode(['参加保险面积','总面积']);
                $result['jsfun'] = 'wdjShowEchart';
                $result['time'] = $cache['insurancetime'];
                break;
            case '贷款':
                $result['title'] = '贷款情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['loantitle'];
                $result['dw'] = '单位（万元）&nbsp;&nbsp;';
//                 var_dump(json_encode(Loan::getBankList('small')));exit;
                $result['echartTitle'] = json_encode(Loan::getBankList('small'));
                $result['cache'] = $cache['loancache'];
                $result['echartdw'] = json_encode('万元');
                $result['name'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['jsfun'] = 'showAllShadow';
                $result['time'] = $cache['loantime'];
                break;
        }
        return $result;
    }

    public static function echartDataReal($id)
    {
        $result = [];
        $plantarr = ArrayHelper::map(Mainmenu::find()->where(['typename'=>1])->all(), 'id', 'menuname');

        switch ($plantarr[$id]) {
            case '宜农林地':
                $result['title'] = '农场情况统计表('.User::getYear().')';
                $farm = Farms::find()->where(['state'=>[1,2,3,4,5]]);
                $result['ltitle'] = '面积:'.$farm->sum('contractarea').'&nbsp;农场户数:'.$farm->count();
                $result['dw'] = '亩|户&nbsp;&nbsp;';
                $result['jsfun'] = 'showBar';
                $result['name'] = json_encode(['面积','数量']);
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['cache'] = json_encode(Farms::getFarmsarea());
                $result['echartdw'] = json_encode(['面积'=>'亩','数量'=>'户']);
                break;
            case '精准农业':
                $result['title'] = '种植结构情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['plantingstructuretitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['plantingstructurecategories'];
                $result['cache'] = $cache['plantingstructurecache'];
                $result['echartdw'] = '亩';
                $result['name'] = json_encode(['面积','数量']);
                $result['jsfun'] = 'showAllShadow';
                break;
            case '农产品':
                $result['title'] = '农产品情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['plantinputproducttitle'];
                $result['dw'] = '单位（斤)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['plantinputproduccategories'];
                $result['cache'] = $cache['plantinputproducache'];
                $result['echartdw'] = '斤';
                $result['name'] = json_encode(Plantingstructure::getPlantname());
                $result['jsfun'] = 'showAllShadow';
                break;
            case '惠农政策':
                $result['title'] = '补贴发放情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['huinongtitle'];
                $result['dw'] = '单位（元)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['huinongcategories'];
                $result['cache'] = $cache['huinongcache'];
                $result['echartdw'] = '元';
                $result['name'] = json_encode(['应发','实发']);
                $result['jsfun'] = 'showShadow';
                break;
            case '承包费收缴':
                $m = Farms::getManagementArea()['id'];
                //             var_dump($m);
                if(count($m) > 1) {
                    $managementarea = 0;
                    $echartsData['all'] = [
                        Collectionsum::find()->where(['management_area'=>1,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>2,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>3,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>4,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>5,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>6,'year'=>User::getYear()])->one()['allsum'],
                        Collectionsum::find()->where(['management_area'=>7,'year'=>User::getYear()])->one()['allsum'],
                    ];
                    $echartsData['real'] = [
                        Collectionsum::find()->where(['management_area'=>1,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>2,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>3,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>4,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>5,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>6,'year'=>User::getYear()])->one()['realsum'],
                        Collectionsum::find()->where(['management_area'=>7,'year'=>User::getYear()])->one()['realsum'],
                    ];
                    $all = Collectionsum::find()->where(['management_area'=>0,'year'=>User::getYear()])->one()['allsum'];
                    $real = Collectionsum::find()->where(['management_area'=>0,'year'=>User::getYear()])->one()['realsum'];
                } else {
                    $managementarea = $m[0];
                    $echartsData['all'] = [
                        Collectionsum::find()->where(['management_area'=>$managementarea,'year'=>User::getYear()])->one()['allsum']
                    ];
                    $echartsData['real'] = [
                        Collectionsum::find()->where(['management_area'=>$managementarea,'year'=>User::getYear()])->one()['realsum'],
                    ];
                    $all = Collectionsum::find()->where(['management_area'=>$managementarea,'year'=>User::getYear()])->one()['allsum'];
                    $real = Collectionsum::find()->where(['management_area'=>$managementarea,'year'=>User::getYear()])->one()['realsum'];
                }
//                var_dump($echartsData);exit;
                $result['title'] = '承包费收缴情况统计表('.User::getYear().')';
                $result['ltitle'] = '应收:'.bcadd($all,$real,2).'元&nbsp;&nbsp;实收:'.$real.'元';
                $result['dw'] = '单位（元)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['all'] = json_encode($echartsData['all']);
                $result['real'] = json_encode($echartsData['real']);
                $result['echartdw'] = '元';
                $result['name'] = json_encode(['实收金额','应收金额']);
                $result['jsfun'] = 'wdjShowEchart';
                break;
            case '防火工作':
                $result['title'] = '防火工作情况统计表('.User::getYear().')';
                $data = json_decode($cache['firecache']);
//                 var_dump($data);exit;
                $result['ltitle'] = $cache['firetitle'].'%';
                $result['dw'] = '单位（户)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
//                 $result['cache'] = $cache['firecategories'];
//                 $result['echartdw'] = '亩';
//                 $result['name'] = json_encode(['面积','数量']);
//                 $result['jsfun'] = 'showAllShadow';
                $all = [];
                $real = [];
                if($data) {
                    $all = $data->all;
                    $real = $data->real;
                }
                $result['all'] = json_encode($all);
                $result['real'] = json_encode($real);
                $result['echartdw'] = '户';
                $result['name'] = json_encode(['已完成','应完成']);
                $result['jsfun'] = 'wdjShowEchart';
                break;
            case '畜牧业':
                $result['title'] = '种植结构情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['breedinfotitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);
                $result['cache'] = $cache['breedinfocategories'];
                $result['echartdw'] = '亩';
                $result['name'] = json_encode(['面积','数量']);
                $result['jsfun'] = 'showAllShadow';
                break;
            case '项目申报':
                $result['title'] = '项目情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['projectapplicationtitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = $cache['projectapplicationcategories'];
                $result['cache'] = $cache['projectapplicationcache'];
                $result['echartdw'] = '亩';
                $result['name'] = json_encode(['面积','数量']);
                $result['jsfun'] = 'showAllShadowProject';
                break;
            case '保险业务':
                $result['title'] = '保险业务情况统计表('.User::getYear().')';
                $data = json_decode($cache['insurancecache']);
                $result['ltitle'] = $cache['insurancetitle'];
                $result['dw'] = '单位（亩)&nbsp;&nbsp;';
                $result['echartTitle'] = json_encode(Farms::getManagementArea('small')['areaname']);

//                 var_dump($data->all);exit;
                $result['all'] = json_encode($data->all);
                $result['real'] = json_encode($data->real);
                $result['echartdw'] = '亩';
                $result['name'] = json_encode(['参加保险面积','总面积']);
                $result['jsfun'] = 'wdjShowEchart';
                break;
            case '贷款':
                $result['title'] = '贷款情况统计表('.User::getYear().')';
                $result['ltitle'] = $cache['loantitle'];
                $result['dw'] = '单位（万元）&nbsp;&nbsp;';
//                 var_dump($cache['loancache']);exit;
                $result['echartTitle'] = json_encode(Loan::getBankList());
                $result['cache'] = $cache['loancache'];
                $result['echartdw'] = '万元';
                $result['name'] = json_encode(['']);
                $result['jsfun'] = 'showAllShadow';
                break;
        }
        return $result;
    }

    public static function getEchartsID($str)
    {
        $indexecharts = Indexecharts::find()->where(['user_id'=>Yii::$app->getUser()->id])->one();
        return $indexecharts[$str];
    }
}
