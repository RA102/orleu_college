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
/* @var $model common\models\person\Student */
/* @var $form frontend\models\forms\StudentGeneralForm */
/* @var $activeForm yii\widgets\ActiveForm */
?>

<?php $this->beginBlock('update-content') ?>

    <?php $activeForm = ActiveForm::begin(); ?>

    <?= $activeForm->field($form, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $activeForm->field($form, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $activeForm->field($form, 'middlename')->textInput(['maxlength' => true]) ?>

    <?= $activeForm->field($form, 'sex')->dropDownList(PersonHelper::getSexList()) ?>

    <?= $activeForm->field($form, 'birth_date')->widget(DatePicker::class, [
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $activeForm->field($form, 'birth_place')->widget(AutoComplete::class, [
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'source' => Url::to(['student/ajax-address']),
            'minLength' => '5',
        ],
    ]); ?>


    <?= $activeForm->field($model, 'nationality_id')->widget(Select2::classname(), [
            'data' => \yii\helpers\ArrayHelper::map(Nationality::find()->all(), 'id', 'name'),
            'options' => ['placeholder' => ''],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?= $activeForm->field($form, 'iin')->textInput(['maxlength' => true]) ?>

    <?= $activeForm->field($model, 'language')->dropDownList(\common\helpers\LanguageHelper::getLanguageList()) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

<?php $this->endBlock() ?>

<?= $this->render('_update_layout', ['model' => $model]) ?>