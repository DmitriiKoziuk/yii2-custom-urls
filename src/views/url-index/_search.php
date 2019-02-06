<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/* @var $this  yii\web\View */
/* @var $model \DmitriiKoziuk\yii2CustomUrls\data\UrlIndexSearchParams */
/* @var $form  yii\widgets\ActiveForm */
?>

<div class="url-index-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'url') ?>

    <?= $form->field($model, 'redirect_to_url') ?>

    <?= $form->field($model, 'controller_name') ?>

    <?= $form->field($model, 'action_name') ?>

    <?= $form->field($model, 'entity_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t(BaseModule::ID, 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t(BaseModule::ID, 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
