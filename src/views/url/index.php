<?php

use yii\helpers\Html;
use yii\grid\GridView;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;

/**
 * @var $this         yii\web\View
 * @var $searchModel  \DmitriiKoziuk\yii2CustomUrls\forms\UrlSearchForm
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t(CustomUrlsModule::ID, 'Url index');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t(CustomUrlsModule::ID, 'Add url to index'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'url',
            'redirect_to_url',
            'module_name',
            'controller_name',
            'action_name',
            'entity_id',
            'created_at:datetime',
            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
