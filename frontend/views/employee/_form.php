<?php

use common\helpers\PersonHelper;
use common\models\Nationality;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\StudentGeneralForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'middlename')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'sex')->dropDownList(PersonHelper::getSexList()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'birth_date')->widget(DatePicker::class, [
                'language' => 'ru',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'birth_place')->widget(AutoComplete::class, [
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'source' => Url::to(['employee/ajax-address']),
                    'minLength' => '5',
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'nationality_id')->widget(Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(Nationality::find()->all(), 'id', 'name'),
                    'options' => ['placeholder' => ''],
                    'theme' => 'default',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'iin')
                ->widget(\yii\widgets\MaskedInput::class, ['mask' => '999999999999'])
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'language')->dropDownList(\common\helpers\LanguageHelper::getLanguageList()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'indentity')->textInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
