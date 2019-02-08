<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/**
 * @var $this          \yii\web\View
 * @var $urlUpdateForm \DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm
 */

$this->title = Yii::t(CustomUrlsModule::TRANSLATE, 'Update url: {name}', [
    'name' => $urlUpdateForm->url,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrlsModule::TRANSLATE, 'Url index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $urlUpdateForm->url, 'url' => ['view', 'id' => $urlUpdateForm->url]];
$this->params['breadcrumbs'][] = Yii::t(BaseModule::TRANSLATE, 'Update');
?>
<div class="url-index-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'urlInputForm' => $urlUpdateForm,
    ]) ?>

</div>
