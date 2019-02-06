<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2CustomUrls\CustomUrls;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/**
 * @var $this              \yii\web\View
 * @var $urlIndexInputData \DmitriiKoziuk\yii2CustomUrls\data\UrlData
 */

$this->title = Yii::t(CustomUrls::ID, 'Update url: {name}', [
    'name' => $urlIndexInputData->url,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrls::ID, 'Url index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $urlIndexInputData->url, 'url' => ['view', 'id' => $urlIndexInputData->url]];
$this->params['breadcrumbs'][] = Yii::t(BaseModule::ID, 'Update');
?>
<div class="url-index-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'urlIndexInputData' => $urlIndexInputData,
    ]) ?>

</div>
