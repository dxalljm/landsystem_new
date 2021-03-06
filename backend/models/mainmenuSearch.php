<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\mainmenu;

/**
 * mainmenuSearch represents the model behind the search form about `app\models\mainmenu`.
 */
class mainmenuSearch extends mainmenu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','sort','typename'], 'integer'],
            [['menuname', 'menuurl'], 'safe'],
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
        $query = mainmenu::find();

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
        	'sort' => $this->sort,
        	'typename' => $this->typename,
        ]);

        $query->andFilterWhere(['like', 'menuname', $this->menuname])
            ->andFilterWhere(['like', 'menuurl', $this->menuurl]);

        return $dataProvider;
    }
}
