<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2CustomUrls\CustomUrls;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/**
 * @var $this yii\web\View
 * @var $model \DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord
 */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrls::ID, 'Url Index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="url-index-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t(BaseModule::ID, 'Update'), ['update', 'id' => $model->url], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t(BaseModule::ID, 'Delete'), ['delete', 'id' => $model->url], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t(BaseModule::ID, 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url',
            'redirect_to_url',
            'module_name',
            'controller_name',
            'action_name',
            'entity_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
