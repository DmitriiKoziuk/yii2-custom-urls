<?php
namespace DmitriiKoziuk\yii2CustomUrls\services;

use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlSearchForm;
use DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord;

class UrlSearchService
{
    /**
     * @var UrlSearchForm $_searchParams
     */
    private $_searchParams;

    public function __construct(UrlSearchForm $searchParams)
    {
        $this->_searchParams = $searchParams;
    }

    public function getActiveDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->_buildQuery(),
        ]);
    }

    private function _buildQuery(): ActiveQuery
    {
        $query = UrlIndexRecord::find();

        if (! $this->_searchParams->validate()) {
            // if you do not want to return any records when validation fails
            $query->where('0=1');
        }

        $query->andFilterWhere([
            'created_at' => $this->_searchParams->created_at,
            'updated_at' => $this->_searchParams->updated_at,
        ]);

        $query->andFilterWhere(['like', 'url', $this->_searchParams->url])
            ->andFilterWhere(['like', 'redirect_to_url', $this->_searchParams->redirect_to_url])
            ->andFilterWhere(['like', 'module_name', $this->_searchParams->module_name])
            ->andFilterWhere(['like', 'controller_name', $this->_searchParams->controller_name])
            ->andFilterWhere(['like', 'action_name', $this->_searchParams->action_name])
            ->andFilterWhere(['like', 'entity_id', $this->_searchParams->entity_id]);

        return $query;
    }
}