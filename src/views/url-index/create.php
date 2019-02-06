<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2CustomUrls\CustomUrls;

/**
 * @var $this              \yii\web\View
 * @var $urlIndexInputData \DmitriiKoziuk\yii2CustomUrls\data\UrlData
 */

$this->title = Yii::t(CustomUrls::ID, 'Add url to index');
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrls::ID, 'Url index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'urlIndexInputData' => $urlIndexInputData,
    ]) ?>

</div>
