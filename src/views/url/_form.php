<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;

/**
 * @var $this         \yii\web\View
 * @var $form         yii\widgets\ActiveForm
 * @var $urlInputForm UrlCreateForm|UrlUpdateForm
 */

$type  = ($urlInputForm instanceof UrlUpdateForm) ? 'hidden' : 'text';
$label = ($urlInputForm instanceof UrlUpdateForm) ? false : null;
?>

<div class="url-index-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($urlInputForm, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($urlInputForm, 'redirect_to_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($urlInputForm, 'module_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlInputForm, 'controller_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlInputForm, 'action_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlInputForm, 'entity_id')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t(BaseModule::TRANSLATE, 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
