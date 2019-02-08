<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/**
 * @var $this yii\web\View
 * @var $urlData \DmitriiKoziuk\yii2CustomUrls\data\UrlData
 */

$this->title = $urlData->getUrl();
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrlsModule::ID, 'Url Index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="url-index-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Update'), ['update', 'id' => $urlData->getUrl()], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Delete'), ['delete', 'id' => $urlData->getUrl()], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t(BaseModule::TRANSLATE, 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $urlData,
        'attributes' => [
            'url',
            'redirectToUrl',
            'moduleName',
            'controllerName',
            'actionName',
            'entityId',
            'createdAt:datetime',
            'updatedAt:datetime',
        ],
    ]) ?>

</div>
