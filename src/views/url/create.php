<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;

/**
 * @var $this         \yii\web\View
 * @var $urCreateForm \DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm
 */

$this->title = Yii::t(CustomUrlsModule::TRANSLATE, 'Add url to index');
$this->params['breadcrumbs'][] = ['label' => Yii::t(CustomUrlsModule::TRANSLATE, 'Url index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'urlInputForm' => $urCreateForm,
    ]) ?>

</div>
