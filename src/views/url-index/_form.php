<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/**
 * @var $this              \yii\web\View
 * @var $urlIndexInputData UrlData
 * @var $form              yii\widgets\ActiveForm
 */

$type  = $urlIndexInputData->getScenario() === UrlData::SCENARIO_UPDATE ? 'hidden' : 'text';
$label = $urlIndexInputData->getScenario() === UrlData::SCENARIO_UPDATE ? false : null;
?>

<div class="url-index-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($urlIndexInputData, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($urlIndexInputData, 'redirect_to_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($urlIndexInputData, 'module_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlIndexInputData, 'controller_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlIndexInputData, 'action_name')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <?= $form->field($urlIndexInputData, 'entity_id')
        ->textInput(['type' => $type, 'maxlength' => true])
        ->label($label) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t(BaseModule::ID, 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
