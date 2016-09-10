<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * NewsSearch - поиск новости. Формирует запрос для отображения списка новостей 
 * с учетом страниц и колличества новостей на странице
 */
class NewsSearch extends News
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['subj', 'date', 'post'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Формирование запроса к БД
     * @param array $params
     * @param int $itemsInPage - количество новостей на странице
     * @return ActiveDataProvider
     */
    public function search($params, $itemsInPage)
    {
        $query = News::find()->orderBy(['date' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $itemsInPage,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'subj', $this->subj])
            ->andFilterWhere(['like', 'post', $this->post]);

        return $dataProvider;
    }
}
