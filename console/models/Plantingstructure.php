<?php

namespace console\models;

use Yii;
use console\models\Plant;
use console\models\ManagementArea;
use console\models\Farms;
use console\models\Goodseed;
use console\models\Theyear;
/**
 * This is the model class for table "{{%plantingstructure}}".
 *
 * @property integer $id
 * @property integer $plant_id
 * @property double $area
 * @property integer $inputproduct_id
 * @property integer $pesticides_id
 * @property integer $is_goodseed
 * @property integer $goodseed_id
 * @property string $zongdi
 * @property integer $farms_id
 */
class Plantingstructure extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plantingstructure}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plant_id', 'plant_father','farms_id', 'lease_id'], 'integer'],
            [['area'], 'number'],
            [['zongdi'], 'string'],
        	[['goodseed_id'],'safe'],
        ]; 
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'plant_id' => '种植结构',
        	'plant_father'=>'种植结构父ID',
            'area' => '种植面积',
            'goodseed_id' => '良种使用信息',
            'zongdi' => '宗地',
            'farms_id' => '农场ID',
        	'lease_id' => '承租人ID',
        	'create_at' => '创建日期',
        	'update_at' => '更新日期',
        ];
    }
   
   
    
    //得到已经填写种植信息的宗地
    public static function getOverZongdi($lease_id,$farms_id)
    {
    	$result = [];
    	$plantings = Plantingstructure::find()->where(['lease_id'=>$lease_id,'farms_id'=>$farms_id])->all();
    	if($plantings) {
    		foreach ($plantings as $value) {
    			if(!strstr($value['zongdi'],'-')) {
	    			$result[$value['zongdi']] = $value['area'];
    			} else {
	    			$arrZongdi = explode('、', $value['zongdi']);
	    			foreach ($arrZongdi as $val) {
	    				$result[] = $val;
	    			}
    			}
    		}
    	}
//     	var_dump($result);
//     	exit;
    	return $result;
    }
    
    public static function getNoZongdi($lease_id,$farms_id)
    {
    	if($lease_id == 0) {
    		$over = self::getOverZongdi($lease_id, $farms_id);
    		$all = Lease::getNOZongdi($farms_id);
    		if($over) {
    			$result = self::getLastArea($over, $all);
    		} else 
    			$result = $all;
    	} else {
	    	$over = self::getOverZongdi($lease_id, $farms_id);
	    	$all = Lease::getLeaseArea($lease_id);
	    	$result = self::getLastArea($over, $all);
    	}
    	return $result;
    }
    
    //处理种植结构剩余宗地面积 $over=已经有种植结构的地块，$all为当前承租人的所有地块
    public static function getLastArea($over,$all)
    {
//     	    	var_dump($all);
//     	    	var_dump($over);
//     	    	exit;
    	foreach ($all as $val) {
    		$result[Lease::getZongdi($val)] = Lease::getArea($val);
    	}
// 		var_dump($result);
// 		var_dump($over);
// 		exit;
    	foreach($result as $key => $value) {
    		
	    	foreach ($over as $k => $v) {
	    		if(!strstr($v,'(')) {
    				$result[$k] = $k - $v;
    			} else {
	    			if($key == Lease::getZongdi($v)) {
	    				
	    				if($value == Lease::getArea($v)) {
	    					unset($result[$key]);
	    				} else {
	    					$area = $result[$key] - Lease::getArea($v);
							$result[$key] = $area;
	    				}
	    			}
    			}
	    	}		
    	}
    	$zongdi = [];
    	foreach ($result as $key=>$value) {
//     		if(preg_match('/^\d+\.?/iU', $value)) {
//     			$zongdi[0] = $value;
//     		} else {
// 	    		if($value !== 0.0 and $key !== '')
// 	    			$zongdi[] = $key.'('.$value.')';
// 	    		else 
// 	    			$zongdi[] = $value;
//     		}
			$zongdi[] = $key.'('.$value.')';
    	}
//     	var_dump($zongdi);
//     	exit;
    	return $zongdi;
    }

//     public static function getPlantname($userid)
//     {
//     	$data = [];
//     	$result = [];
//     	$allid = [];
//     	$area = Farms::getUserManagementArea($userid);
//     	$plantallid = [];
// 			// 农场区域
			
// // 			$array['areaname'] = $area['areaname'][$key];
			
// 			$farm = Farms::find()->where(['management_area'=>$area])->all();
//     		foreach ($farm as $val) {
//     			$allid[] = $val['id'];
//     		}
//     		$plantsum = 0;
//     		$goodseedsum = 0;
//     		$planting = Plantingstructure::find()->where(['farms_id'=>$allid])->all();
//     		foreach ($planting as $v) {
//     			$plantallid[] = $v['plant_id'];
    			
//     		}
//     		$plantname = Plant::find()->where(['id'=>$plantallid])->all();
// //     		var_dump($plantname);exit;
//     		foreach ($plantname as $pname) {
//     			$data[$pname['typename']] = $pname['typename'];
//     		}
    		
    	
//     	foreach ($data as $value) {
//     		$result[] = $value;
//     	}
//     	return $result;
//     }
    
    public static function getPlantname($userid)
    {
    	$result['id'] = [];
    	$result['plantname'] = [];
    	$where = Farms::getUserManagementArea($userid);
//     	var_dump($userid);
//     	var_dump($where);
    	$Plantingstructure = Plantingstructure::find ()->where (['management_area' => $where])->all ();
    	$data = [];
    	foreach($Plantingstructure as $value) {
    		$data[] = ['id'=>$value['plant_id']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		foreach ($newdata as $value) {
    			$result['id'][] = $value;
    			$result['plantname'][] = Plant::find()->where(['id' => $value])->one()['typename'];
    		}
    	}
    	return $result;
    }
    
    public static function getGoodseedname($userid)
    {
    	$result = [];
    	$where = Farms::getUserManagementArea($userid);
    	$Plantingstructure = Plantingstructure::find ()->where (['management_area' => $where])->all ();
    	$data = [];
    	foreach($Plantingstructure as $value) {
    		$data[] = ['id'=>$value['goodseed_id']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		foreach ($newdata as $value) {
    			$result['id'][] = $value;
    			$result['goodseedname'][] = Goodseed::find()->where(['id' => $value])->one()['plant_model'];
    		}
    	}
    	return $result;
    }
    
    public static function getPlantingstructure($userid)
    {
    	$data = [];
    	$result = [];
    	$areaNum = 0;
    	$plant = [];
    	$goodseed = [];
    	$goodseedresult = [];
    	$plantresult = [];
		$area = Farms::getUserManagementArea($userid);
		$plantid = self::getPlantname($userid);
// 		var_dump($plantid);
// 		$goodseedid = self::getGoodseedname($userid)['id'];
    	foreach ( $area as $key => $value ) {
    		$areaNum++;

			// 农场区域
			
// 			$array['areaname'] = $area['areaname'][$key];
			
// 			$farm = Farms::find()->where(['management_area'=>$value])->all();
    		$plantArea = [];
    		foreach ($plantid['id'] as $val) {
    			$plantsum = 0.0;
    			$goodseedsum = 0.0;
    			$area = Plantingstructure::find()->where(['management_area'=>$value,'plant_id'=>$val,'year'=>User::getYear($userid)])->sum('area');
    			$plantArea[] = (float)sprintf("%.2f", $area);
    		}
    		$result[] = [
    				'name' => str_ireplace('管理区', '', ManagementArea::find()->where(['id'=>$value])->one()['areaname']),
    				'type' => 'bar',
    				'stack' => $value,
    				'data' => $plantArea
    		];
    	}
//     	var_dump($result);
    	$jsonData = json_encode ($result);
    	
    	return $jsonData;
    }
    

    
    public static function getPlantGoodseedSum($userid) {
    	$plant = [];
    	$goodseed = [];
    	$area = Farms::getUserManagementArea($userid);
//     	foreach ( $area as $key => $value ) {

    			$planting = Plantingstructure::find()->where(['management_area'=>$area,'year'=>date('Y')])->all();
    			foreach ($planting as $v) {
    				$plantname = Plant::find()->where(['id'=>$v['plant_id']])->one()['typename'];
    	
//     				$plantsum += $v['area'];
    				$plant[$plantname][] = $v['area'];
    				if($v['goodseed_id'] !== 0 and $v['goodseed_id'] !== '')
    					$goodseedarea = $v['area'];
    				else
    					$goodseedarea = 0.0;
    				
    				$goodseed[$plantname][] = $goodseedarea;
    				// 					var_dump($goodseed);
    			}
    		
//     	}
    	$plantsum = 0.0;
    	$goodseedsum = 0.0;
    	foreach ($plant as $value) {
    		foreach($value as $val) {
    			$plantsum += $val;
    		}
    	}
    	foreach ($goodseed as $value) {
    		foreach($value as $val) {
    			$goodseedsum += $val;
    		}
    	}
    	return ['plantSum'=>(float)sprintf("%.4f", $plantsum),'goodseedSum'=>(float)sprintf("%.4f", $goodseedsum)];
    }
}
