<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%yield}}".
 *
 * @property integer $id
 * @property integer $planting_id
 * @property integer $farms_id
 * @property double $single
 */
class Yields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%yields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['planting_id', 'farms_id','plant_id','create_at','update_at','management_area'], 'integer'],
            [['single'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'planting_id' => '种植结构ID',
            'farms_id' => '农场ID',
            'single' => '单产',
        	'create_at' => '创建日期',
        	'update_at' => '更新日期',
        	'management_area'=> '管理区',
        	'plant_id' => '作物',
        ];
    }
    
    public static function getAllname()
    {
    	$result = [];
    	$where = Farms::getManagementArea()['id'];
    	$yields = Yields::find ()->where (['management_area'=>$where])->all ();
    	$data = [];
    	foreach($yields as $value) {
    		$data[] = ['id'=>Plantingstructure::find()->where(['id'=>$value['planting_id']])->one()['plant_id']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		foreach($newdata as $value) {
    			//     		var_dump($value);exit;
    			$result[$value['id']] = Plant::find()->where(['id'=>$value['id']])->one()['typename'];
    		}
    	}
    	return $result;
    }
    
    public static function getNameOne($id)
    {
    	$data = self::getAllname();
    	return $data[$id];
    }
    public static function farmSearch($str = NULL)
    {
    	if (preg_match ("/^[A-Za-z]/", $str)) {
    		$tj = ['like','pinyin',$str];
    	} else {
    		$tj = ['like','farmname',$str];
    	}
    
    	return $tj;
    }
    
    public static function farmerSearch($str = NULL)
    {
    	if (preg_match ("/^[A-Za-z]/", $str)) {
    		$tj = ['like','farmerpinyin',$str];
    	} else {
    		$tj = ['like','farmername',$str];
    	}
    	//     	var_dump($tj);exit;
    	return $tj;
    }
    public static function getFarmRows($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$row = $yields->count ();
    	return $row;
    }
    
    public static function getFarmerrows($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$data = [];
    	foreach($yields->all() as $value) {
    		$farm = Farms::find()->where(['id'=>$value['farms_id']])->one();
    		$data[] = ['farmername'=>$farm['farmername'],'cardid'=>$farm['cardid']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		return count($newdata);
    	}
    	else
    		return 0;;
    }
    
    public static function getPlantRows($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$data = [];
    	foreach($yields->all() as $value) {
    		$planting = Plantingstructure::find()->where(['id'=>$value['planting_id']])->one();
    		if($planting)
    			$data[] = ['plant_id'=>Plant::find()->where(['id'=>$planting['plant_id']])->one()['typename']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		return count($newdata);
    	}
    	else
    		return 0;
    }
   
    
    public static function getPlantG($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$sum = 0.0;
    	foreach($yields->all() as $value) {
    		$sum += $value['single'];
    	}
    	return (float)sprintf("%.2f", $sum/10000);
    }
    
    public static function getPlantA($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$sum = 0.0;
    	foreach($yields->all() as $value) {
    		$planting = Plantingstructure::find()->where(['id'=>$value['planting_id']])->one();
    		$sum += $value['single']*$planting['area'];
    	}
    	return (float)sprintf("%.2f", $sum/10000);
    }
    
    public static function getArea($params)
    {
    	$sum = 0.0;
   		$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	foreach ($yields->all() as $value) {
    		$planting = Plantingstructure::find()->where(['id'=>$value['planting_id']])->one();
    		$sum += $planting['area'];
    	}
    	return (float)sprintf("%.2f", $sum/10000);
    }

	public static function getTypename()
	{
		$data = [];
		$result = ['id'=>[],'typename'=>[]];
		$yields = Plantingstructurecheck::find()->where(['management_area'=>Farms::getManagementArea()['id'],'year'=>User::getYear()]);
		foreach ($yields->all() as $value) {
			$data[] = ['id'=>$value['plant_id']];
		}
		if($data) {
			$newdata = Farms::unique_arr($data);
			foreach ($newdata as $value) {
				$result['id'][] = $value['id'];
				$result['typename'][] = Plant::find()->where(['id' => $value['id']])->one()['typename'];
			}
		}
//		var_dump($result);
		return  $result;
	}

	public static function getTypenamelist($params)
    {
    	$sum = 0.0;
    	$result = ['id'=>[],'typename'=>[]];
   		$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
//     	var_dump($query->where);exit;
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	foreach ($yields->all() as $value) {
    		$data[] = ['id'=>$value['plant_id']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		foreach ($newdata as $value) {
    			$result['id'][] = $value['id'];
    			$result['typename'][] = Plant::find()->where(['id' => $value['id']])->one()['typename'];
    		}
    	}
    	return  $result;
    }
    
    public static function getYields($params)
    {
    	$yields = Yields::find ();
    	if($params['yieldsSearch']['management_area'] == 0)
    		$management_area = NULL;
    	else
    		$management_area = $params['yieldsSearch']['management_area'];
    	if(isset($params['yieldsSearch']['farms_id']) or isset($params['yieldsSearch']['farmer_id'])) {
    		$farm = Farms::find();
    		$farm->andFilterWhere(['management_area'=>$management_area]);
    	}
    	if(isset($params['yieldsSearch']['farms_id']) and $params['yieldsSearch']['farms_id'] !== '') {
    		$farms_id = $params['yieldsSearch']['farms_id'];
    		$farm->andFilterWhere(self::farmSearch($farms_id));
    		 
    	}
    	$farmid = [];
    	if(isset($params['yieldsSearch']['farmer_id']) and $params['yieldsSearch']['farmer_id'] !== '') {
    		$farmer_id = $params['yieldsSearch']['farmer_id'];
    		$farm->andFilterWhere(self::farmerSearch($farmer_id));
    	}
    	//     	var_dump($query->where);exit;
    	if(isset($farm)) {
    		foreach ($farm->all() as $value) {
    			$farmid[] = $value['id'];
    		}
    	}
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	if(isset($params['yieldsSearch']['plant_id']) and $params['yieldsSearch']['plant_id'] !== '')
    		$yields->andFilterWhere(['plant_id'=>$params['yieldsSearch']['plant_id']]);
    	$yields->andFilterWhere(['farms_id'=>$farmid]);
    	$plantid = self::getTypenamelist($params);
		$oldp = $yields->where;
		if(is_array($management_area)) {
			foreach ($management_area as $value) {
				$plantArea = [];
				foreach ($plantid['id'] as $val) {
					$yields->where = $oldp;
					$plantsum = 0.0;
					$goodseedsum = 0.0;
					$yields->andFilterWhere(['plant_id'=>$val,'management_area'=>$value]);
					$area = $yields->sum('single');
					$plantArea[] = (float)sprintf("%.2f", $area/10000);
				}
// 				var_dump($plantArea);
				$result[] = [
						'name' => str_ireplace('管理区', '', ManagementArea::find()->where(['id'=>$value])->one()['areaname']),
						'type' => 'bar',
						'stack' => $value,
						'data' => $plantArea
				];
			}
		} else {
    		$plantArea = [];
//     		var_dump($Plantingstructure->where);
    		$yields->andFilterWhere(['management_area'=>$management_area]);
    		$oldp = $yields->where;
    		foreach ($plantid['id'] as $val) {
//     			var_dump($oldp);
    			$yields->where = $oldp;
    			$yields->andFilterWhere(['plant_id'=>$val]);
//     			var_dump($Plantingstructure->where);
    			$area = $yields->sum('single');
//     			var_dump($area);
    			$plantArea[] = (float)sprintf("%.2f", $area/10000);
    		}
//     		var_dump($plantArea);
    		$result[] = [
    				'name' => str_ireplace('管理区', '', ManagementArea::find()->where(['id'=>$management_area])->one()['areaname']),
    				'type' => 'bar',
    				'stack' => $management_area,
    				'data' => $plantArea
    		];
		}
// 		var_dump($result);
    	return  json_encode($result);
    }

	public static function getYieldsCache()
	{
		$result = [];
		$areaNum = 0;
		$area = Farms::getManagementArea()['id'];
		$plantid = Plantingstructurecheck::getPlantname();
		
		foreach ( $area as $key => $value ) {
			$areaNum++;
			// 农场区域
			$plantArea = [];
			foreach ($plantid as $k => $val) {
				$area = Plantingstructurecheck::find()->where(['management_area'=>$value,'plant_id'=>$k,'year'=>User::getYear()])->sum('area');
				$yieldBase = Yieldbase::find()->where(['year'=>User::getYear(),'plant_id'=>$k])->one()['yield'];
				$plantArea[] = (float)bcmul($area,$yieldBase,2);
			}
		}
//		var_dump($plantArea);exit;
		$jsonData = json_encode ($plantArea);

		return $jsonData;
	}

	public static function getYieldsSum()
	{
		$result = [];
		$areaNum = 0;
		$area = Farms::getManagementArea()['id'];
		$plantid = Plantingstructurecheck::getPlantname();
		$sum = 0;
		if(!empty($plantid)) {
			foreach ($area as $key => $value) {
				$areaNum++;
				// 农场区域

				foreach ($plantid['id'] as $val) {
					$area = Plantingstructurecheck::find()->where(['management_area' => $value, 'plant_id' => $val, 'year' => User::getYear()])->sum('area');
					$yieldBase = Yieldbase::find()->where(['year' => User::getYear(), 'plant_id' => $val])->one()['yield'];
					$sum += (float)bcmul($area, $yieldBase, 2);
				}
			}
		}
		return $sum;
	}
    
    public static function getUserTypenamelist($user_id)
    {
    	$sum = 0.0;
		$data = [];
    	$result = ['id'=>[],'typename'=>[]];
    	$yields = Yields::find ();
    	$where = Farms::getUserManagementArea($user_id);
    	
    	$yields->andFilterWhere(['management_area'=>$where]);
    	foreach ($yields->all() as $value) {
    		$data[] = ['id'=>$value['plant_id']];
    	}
    	if($data) {
    		$newdata = Farms::unique_arr($data);
    		foreach ($newdata as $value) {
    			$result['id'][] = $value['id'];
    			$result['typename'][] = Plant::find()->where(['id' => $value['id']])->one()['typename'];
    		}
    	}
    	return  $result;
    }
    
    public static function getUserYields($id)
    {
    	$yields = Yields::find ();
    	$management_area = Farms::getUserManagementArea($id);
    	
    	$yields->andFilterWhere(['management_area'=>$management_area]);
    	
    	$plantid = self::getUserTypenamelist($id);
    	$oldp = $yields->where;
    	if(is_array($management_area)) {
    		foreach ($management_area as $value) {
    			$plantArea = [];
    			foreach ($plantid['id'] as $val) {
    				$yields->where = $oldp;
    				$plantsum = 0.0;
    				$goodseedsum = 0.0;
    				$yields->andFilterWhere(['plant_id'=>$val,'management_area'=>$value]);
    				$area = $yields->sum('single');
    				$plantArea[] = (float)sprintf("%.2f", $area/10000);
    			}
    			// 				var_dump($plantArea);
    			$result[] = [
    					'name' => str_ireplace('管理区', '', ManagementArea::find()->where(['id'=>$value])->one()['areaname']),
    					'type' => 'bar',
    					'stack' => $value,
    					'data' => $plantArea
    			];
    		}
    	} else {
    		$plantArea = [];
    		//     		var_dump($Plantingstructure->where);
    		$yields->andFilterWhere(['management_area'=>$management_area]);
    		$oldp = $yields->where;
    		foreach ($plantid['id'] as $val) {
    			//     			var_dump($oldp);
    			$yields->where = $oldp;
    			$yields->andFilterWhere(['plant_id'=>$val]);
    			//     			var_dump($Plantingstructure->where);
    			$area = $yields->sum('single');
    			//     			var_dump($area);
    			$plantArea[] = (float)sprintf("%.2f", $area/10000);
    		}
    		//     		var_dump($plantArea);
    		$result[] = [
    				'name' => str_ireplace('管理区', '', ManagementArea::find()->where(['id'=>$management_area])->one()['areaname']),
    				'type' => 'bar',
    				'stack' => $management_area,
    				'data' => $plantArea
    		];
    	}
    	// 		var_dump($result);
    	return  json_encode($result);
    }
}
