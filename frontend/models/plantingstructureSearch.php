<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Plantingstructure;
use app\models\Theyear;
use app\models\Farms;
use app\models\Goodseed;
use yii\helpers\ArrayHelper;
/**
 * plantingstructureSearch represents the model behind the search form about `app\models\Plantingstructure`.
 */
class plantingstructureSearch extends Plantingstructure
{
	public $farmer_id;
//	public $insurancetype;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'plant_id', 'goodseed_id', 'lease_id', 'farms_id','farmer_id','management_area','planter','state','isinsurance'], 'integer'],
            [['area'], 'number'],
            [['zongdi','year'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
//     	var_dump($params);exit;
        $query = Plantingstructure::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
      
//        
        
        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'plant_id' => $this->plant_id,
            'area' => $this->area,
            'goodseed_id' => $this->goodseed_id,
        	'lease_id' => $this->lease_id,
            'farms_id' => $this->farms_id,
        	'management_area'=>$this->management_area,
			'year' => $this->year,
        ]);

        $query->andFilterWhere(['like', 'zongdi', $this->zongdi])
        ->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]]);
        return $dataProvider;
        
    }
    public function areaSearch($str = NULL)
    {
    	$this->area = $str;
    	if(!empty($this->area)) {
    		preg_match_all('/(.*)([0-9]+?)/iU', $this->area, $where);
    		//print_r($where);
    
    		// 		string(2) ">="
    		// 		string(3) "300"
    		if($where[1][0] == '>' or $where[1][0] == '>=')
    			$tj = ['between', 'area', (float)$where[2][0],(float)99999.0];
    		if($where[1][0] == '<' or $where[1][0] == '<=')
    			$tj = ['between', 'area', (float)0.0,(float)$where[2][0]];
    		if($where[1][0] == '')
    			$tj = ['like', 'area', $this->area];
    	} else
    		$tj = ['like', 'area', $this->area];
    	//var_dump($tj);
    	return $tj;
    }
    public function pinyinSearch($str = NULL)
    {
    	if (preg_match ("/^[A-Za-z]/", $str)) {
    		$tj = ['like','pinyin',$str];
    	} else {
    		$tj = ['like','farmname',$str];
    	}
    	 
    	return $tj;
    }
    
    public function farmerpinyinSearch($str = NULL)
    {
    	if (preg_match ("/^[A-Za-z]/", $str)) {
    		$tj = ['like','farmerpinyin',$str];
    	} else {
    		$tj = ['like','farmername',$str];
    	}
    	//     	var_dump($tj);exit;
    	return $tj;
    }
    public function searchIndex($params)
    {
//     	echo date('Y-m-d',$params['begindate']);
//		echo '<br>';
//		echo date('Y-m-d',$params['enddate']);
//     	 var_dump($params);exit;
    	$query = Plantingstructure::find();
    
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    	]);
    	if(isset($params['plantingstructureSearch']['management_area'])) {
	    	if($params['plantingstructureSearch']['management_area'] == 0)
	    		$this->management_area = NULL;
	    	else
	    		$this->management_area = $params['plantingstructureSearch']['management_area'];
    	} else {
    		$management_area = Farms::getManagementArea()['id'];
    	
			if(count($management_area) > 1)
				$this->management_area = NULL;
			else 
				$this->management_area = $management_area;
    	}
    	$farmid = [];
    	if((isset($params['plantingstructureSearch']['farms_id']) and $params['plantingstructureSearch']['farms_id'] !== '') or (isset($params['plantingstructureSearch']['farmer_id']) and $params['plantingstructureSearch']['farmer_id'] !== '')) {
	    	$farm = Farms::find();
	    	$farm->andFilterWhere(['management_area'=>$this->management_area]);
    	}
    	if(isset($params['plantingstructureSearch']['farms_id']) and $params['plantingstructureSearch']['farms_id'] !== '') {
    		$this->farms_id = $params['plantingstructureSearch']['farms_id'];
    		$farm->andFilterWhere($this->pinyinSearch($this->farms_id));
    		    		
    	}

    	if(isset($params['plantingstructureSearch']['farmer_id']) and $params['plantingstructureSearch']['farmer_id'] !== '') {
    		$this->farmer_id = $params['plantingstructureSearch']['farmer_id'];
    		$farm->andFilterWhere($this->farmerpinyinSearch($this->farmer_id));
    	}
    	if(isset($farm)) {
	    	foreach ($farm->all() as $value) {
	    		$farmid[] = $value['id'];
	    	}
    	}
//     	var_dump($farmid);exit;
    	if(isset($params['plantingstructureSearch']['plant_id']))
    		$this->plant_id = $params['plantingstructureSearch']['plant_id'];

//		if(isset($params['plantingstructureSearch']['insurancetype']))
//			$this->plant_id = $params['plantingstructureSearch']['insurancetype'];

		if(isset($params['plantingstructureSearch']['state']))
			$this->state = $params['plantingstructureSearch']['state'];
		if(isset($params['plantingstructureSearch']['goodseed_id']))
			$this->goodseed_id = $params['plantingstructureSearch']['goodseed_id'];

		if(isset($params['plantingstructureSearch']['year']))
			$this->year = $params['plantingstructureSearch']['year'];

		if(isset($params['plantingstructureSearch']['planter']))
			$this->planter = $params['plantingstructureSearch']['planter'];

    	if(isset($params['plantingstructureSearch']['goodseed_id'])) {
    		$this->goodseed_id = $params['plantingstructureSearch']['goodseed_id'];
    		$goodseeds = ArrayHelper::map(Goodseed::find()->where(['plant_id'=>$this->plant_id])->all(), 'id', 'id');
    		if(!in_array($this->goodseed_id, $goodseeds)) {
    			$this->goodseed_id = null;
    		}
    	}
    	if(isset($params['plantingstructureSearch']['area']))
    		$query->andFilterWhere($this->areaSearch($params['plantingstructureSearch']['area']));
    	//         $this->setAttributes($params);

    	$query->andFilterWhere([
//     			'id' => $this->id,
    			'plant_id' => $this->plant_id,
    			'goodseed_id' => $this->goodseed_id,
    			'lease_id' => $this->lease_id,
    			'farms_id' => $farmid,
				'planter' => $this->planter,
    			'management_area' => $this->management_area,
				'year' => $this->year,
				'state' => $this->state,
    	]);
    	if(isset($params['begindate'])) {
			$query->andFilterWhere(['between', 'create_at', $params['begindate'], $params['enddate']]);
		}
//     	var_dump($dataProvider->query->where);exit;
    	return $dataProvider;
    }
}
