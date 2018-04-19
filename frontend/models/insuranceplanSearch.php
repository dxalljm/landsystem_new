<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Insuranceplan;
use app\models\Farms;
use app\models\User;
/**
 * InsuranceplanSearch represents the model behind the search form about `app\models\Insuranceplan`.
 */
class InsuranceplanSearch extends Insuranceplan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'insurance_id', 'management_area', 'farms_id', 'create_at', 'update_at', 'policyholdertime', 'state', 'farmstate', 'lease_id'], 'integer'],
            [['year', 'policyholder', 'cardid', 'telephone', 'farmername', 'farmerpinyin', 'policyholderpinyin', 'insured'], 'safe'],
            [['insuredarea', 'contractarea'], 'number'],
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
        $query = Insuranceplan::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'insurance_id' => $this->insurance_id,
            'management_area' => $this->management_area,
            'farms_id' => $this->farms_id,
            'insuredarea' => $this->insuredarea,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
            'policyholdertime' => $this->policyholdertime,
            'state' => $this->state,
            'contractarea' => $this->contractarea,
            'farmstate' => $this->farmstate,
            'lease_id' => $this->lease_id,
        ]);

        $query->andFilterWhere(['like', 'year', $this->year])
            ->andFilterWhere(['like', 'policyholder', $this->policyholder])
            ->andFilterWhere(['like', 'cardid', $this->cardid])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'farmername', $this->farmername])
            ->andFilterWhere(['like', 'farmerpinyin', $this->farmerpinyin])
            ->andFilterWhere(['like', 'policyholderpinyin', $this->policyholderpinyin])
            ->andFilterWhere(['like', 'insured', $this->insured]);

        return $dataProvider;
    }

    public function searchIndex($params)
    {
//    	var_dump($params);exit;
        $query = Insuranceplan::find();
// 		var_dump($query->all());
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(isset($params['insuranceplanSearch']['management_area'])) {
            if ($params['insuranceplanSearch']['management_area'] == 0)
                $this->management_area = Null;
            else
                $this->management_area = $params['insuranceplanSearch']['management_area'];
        } else {
            $management_area = Farms::getManagementArea()['id'];
            if(count($management_area) > 1)
                $this->management_area = NULL;
            else
                $this->management_area = $management_area;
        }
//         if(isset($params['insuranceplanSearch']['state'])) {
//         	$this->state = $params['insuranceplanSearch']['state'];
//         }
        if(isset($params['insuranceplanSearch']['fwdtstate'])) {
            $this->fwdtstate = $params['insuranceplanSearch']['fwdtstate'];
        }
        if(isset($params['insuranceplanSearch']['isbxsame'])) {
            $this->state = $params['insuranceplanSearch']['isbxsame'];
        }
        $farmid = [];

        if(isset($params['insuranceplanSearch']['farms_id']) and $params['insuranceplanSearch']['farms_id'] !== '') {
            $farm = Farms::find()->where(['state'=>[1,2,3,4,5]])->andFilterWhere($this->pinyinSearch($params['insuranceplanSearch']['farms_id']))->select('id');
        }
        if(isset($farm)) {
            foreach ($farm->all() as $value) {
                $farmid[] = $value['id'];
            }
        }
        if(isset($params['insuranceplanSearch']['state']))
            $this->state = $params['insuranceplanSearch']['state'];

        if(isset($params['insuranceplanSearch']['insured'])) {
            $this->insured = $params['insuranceplanSearch']['insured'];
            $query->andFilterWhere(['like','insured',$this->insured.'-']);
        }

        if(isset($params['insuranceplanSearch']['farmstate']))
            $this->farmstate = $params['insuranceplanSearch']['farmstate'];

        $query->andFilterWhere([
            'id' => $this->id,
            'farms_id' => $farmid,
            'management_area' => $this->management_area,
            'state' => $this->state,
            'farmstate' => $this->farmstate,
        ]);

        if(isset($params['insuranceplanSearch']['insuredarea']))
            $query->andFilterWhere($this->areaSearch('insuredarea',$params['insuranceplanSearch']['insuredarea']));
        if(isset($params['insuranceplanSearch']['insuredwheat']))
            $query->andFilterWhere($this->areaSearch('insuredwheat',$params['insuranceplanSearch']['insuredwheat']));
        if(isset($params['insuranceplanSearch']['insuredsoybean']))
            $query->andFilterWhere($this->areaSearch('insuredsoybean',$params['insuranceplanSearch']['insuredsoybean']));
        if(isset($params['insuranceplanSearch']['insuredother']))
            $query->andFilterWhere($this->areaSearch('insuredother',$params['insuranceplanSearch']['insuredother']));
        if(isset($params['insuranceplanSearch']['company_id'])) {
            $this->company_id = $params['insuranceplanSearch']['company_id'];
            $query->andFilterWhere([
                'company_id' => $this->company_id,
            ]);
        }
        if(isset($params['insuranceplanSearch']['farmername']))
            $query->andFilterWhere($this->farmerpinyinSearch($params['insuranceplanSearch']['farmername']));
        if(isset($params['insuranceplanSearch']['policyholder']))
            $query->andFilterWhere($this->policyholderSearch($params['insuranceplanSearch']['policyholder']));
        if(isset($params['insuranceplanSearch']['contractarea']))
            $query->andFilterWhere($this->areaSearch('contractarea',$params['insuranceplanSearch']['contractarea']));

        if(isset($params['insuranceplanSearch']['select']) and $params['insuranceplanSearch']['select'] !== '') {

            $this->select = $params['insuranceplanSearch']['select'];
            switch ($params['insuranceplanSearch']['select']) {
                case 'issame':
//                    $farm = Farms::find()->where(['id'=>$this->farms_id])
                    $query->andFilterWhere(['state'=>1,'iscontractarea'=>0])->andWhere('insuredarea<contractarea');
                    break;
                case 'finished' :
                    $query->andFilterWhere(['state' => 1]);
                    break;
                case 'cancal':
                    $query->andFilterWhere(['state' => -1]);
                    break;
                case 'dsh':
                    $query->andFilterWhere(['fwdtstate'=>0]);
                    break;
                default:
                    $query->andFilterWhere([$params['insuranceplanSearch']['select']=>0]);
            }
        }
//        var_dump(User::getYear());exit;
        $query->andFilterWhere(['year'=>User::getYear()]);
//        if(isset($params['begindate']) and isset($params['enddate']))
//        	$query->andFilterWhere(['between','update_at',$params['begindate'],$params['enddate']]);
//        else {
//        	$query->andFilterWhere(['between','update_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]]);
//        }
// 		var_dump($dataProvider->query->where);exit;
        return $dataProvider;
    }
    public function searchIndex2($params)
    {
//     	    	var_dump($params);exit;
        $query = Insuranceplan::find();
        // 		var_dump($query->all());
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(isset($params['insuranceplanSearch']['management_area'])) {
            if ($params['insuranceplanSearch']['management_area'] == 0)
                $this->management_area = Null;
            else
                $this->management_area = $params['insuranceplanSearch']['management_area'];
        }
        if(isset($params['insuranceplanSearch']['state'])) {
            $this->state = $params['insuranceplanSearch']['state'];
        }
        if(isset($params['insuranceplanSearch']['fwdtstate'])) {
            $this->fwdtstate = $params['insuranceplanSearch']['fwdtstate'];
        }
        if(isset($params['insuranceplanSearch']['isbxsame'])) {
            $this->isbxsame = $params['insuranceplanSearch']['isbxsame'];
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'management_area' => $this->management_area,
            'state' => $this->state,
            'fwdtstate' => $this->fwdtstate,
        ]);
//     	var_dump($query);exit;
        //         if(isset($params['insuranceplanSearch']['state']))
        //         	$query->andFilterWhere([
        //                 'state' => $this->state,
        //             ]);
        if(isset($params['insuranceplanSearch']['insuredarea']))
            $query->andFilterWhere($this->areaSearch('insuredarea',$params['insuranceplanSearch']['insuredarea']));
        if(isset($params['insuranceplanSearch']['insuredwheat']))
            $query->andFilterWhere($this->areaSearch('insuredwheat',$params['insuranceplanSearch']['insuredwheat']));
        if(isset($params['insuranceplanSearch']['insuredsoybean']))
            $query->andFilterWhere($this->areaSearch('insuredsoybean',$params['insuranceplanSearch']['insuredsoybean']));
        if(isset($params['insuranceplanSearch']['insuredother']))
            $query->andFilterWhere($this->areaSearch('insuredother',$params['insuranceplanSearch']['insuredother']));

        if(isset($params['insuranceplanSearch']['company_id'])) {
            $this->company_id = $params['insuranceplanSearch']['company_id'];
            $query->andFilterWhere([
                'company_id' => $this->company_id,
            ]);
        }
        if(isset($params['insuranceplanSearch']['farmername']))
            $query->andFilterWhere($this->farmerpinyinSearch($params['insuranceplanSearch']['farmername']));
        if(isset($params['insuranceplanSearch']['policyholder']))
            $query->andFilterWhere($this->policyholderSearch($params['insuranceplanSearch']['policyholder']));
        if(isset($params['insuranceplanSearch']['contractarea']))
            $query->andFilterWhere($this->areaSearch('contractarea',$params['insuranceplanSearch']['contractarea']));

        if(isset($params['insuranceplanSearch']['select']) and $params['insuranceplanSearch']['select'] !== '') {

            $this->select = $params['insuranceplanSearch']['select'];
            switch ($params['insuranceplanSearch']['select']) {
                case 'finished' :
                    $query->andFilterWhere(['state' => 1]);
                    break;
                case 'cancal':
                    $query->andFilterWhere(['state' => -1]);
                    break;
                case 'dsh':
                    $query->andFilterWhere(['fwdtstate'=>0]);
                    break;
                default:
                    $query->andFilterWhere([$params['insuranceplanSearch']['select']=>0]);
            }
        }
        $query->andFilterWhere(['year'=>User::getYear()]);
        //        var_dump($this->farmername);exit;

        return $dataProvider;
    }
}
