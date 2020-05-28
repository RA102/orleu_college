<?php

use backend\search\handbook\SpecialitySearch;
use common\models\handbook\Speciality;
use common\models\organization\InstitutionSpecialityInfo;
use frontend\models\rup\Profile;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2; 
use yii\web\JsExpression;
//use yii\web\JqueryAsset;


/* @var $this yii\web\View */
/* @var $model app\models\rup\RupRoots */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rup-roots-form">

    <?php $form = ActiveForm::begin(['action' => ['/rup/rup/create'],
        'options' => [
            'class' => 'comment-form',
            'style'=>[
                'width'=>'70%'
            ]
        ]]); ?>
    <?= $form->field($model, 'rup_id',['options' => ['class' => 'sem']])->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'status',['options' => ['class' => 'sem']])->hiddenInput(['value'=>1])->label(false) ?>
    <?= $form->field($model, 'captionRu',['options' => ['class' => 'sem']])->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'rup_year',['options' => ['class' => 'trid']])->dropDownList([2020=>'2020',2021=>'2021']) ?>

    <?= $form->field($model, 'profile_code',['options' => ['class' => 'sem']])->dropDownList(ArrayHelper::map(Speciality::find()->where(['type' => '1'])->all(), 'code', 'CaptionWithCode'))->label('Профиль')  ?>
    <?= $form->field($model, 'edu_form',['options' => ['class' => 'trid']])->dropDownList([0=>'Очная',1=>'Заочная']) ?>

    <?= $form->field($model, 'spec_code',['options' => ['class' => '']])->dropDownList(null)->label("Специальность") ?>
            
            <!--
    
    $form->field($model, 'spec_code')->widget(Select2::classname(), [
    'options' => ['multiple'=>false, 'placeholder' => 'Search for a city ...'],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
       
        'ajax' => [
            'url' => '/rup/rup/get-specialities',
            'type' => 'GET',
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
       
    ],
]);
-->
    
    
    <?=Html::submitButton('Добавить план', ['class' => 'btn btn-success btn-margin','id'=>'rup_save','style'=>[]]);?>
    
    <?php ActiveForm::end(); ?>
    


</div>

<?php
$js = <<<JS
(function() {
    
    function get_specialities(){
        //console.log("yes");
        $.ajax({
            type: 'GET',
            url: '/rup/rup/get-specialities',
            data: {'parent_code': $('select[name="RupRoots[profile_code]"]').val()}, 
            success: function(data){
                //console.log(data);
                $("#ruproots-spec_code").html( data );
            }
        });        
    };

    // $(document).ready(function() {
    //     get_specialities(); 
    // });


    $('#ruproots-profile_code').on('change',function (e) {
        e.preventDefault();
        get_specialities(); 
        //console.log('profile-change!');

    });




})();
JS;
$this->registerJs($js);
?>

<script>
        // $(document).ready(function() {
        //     $.ajax({
        //         url: '/rup/rup/get-specialities',
        //         data:{'parent_code':'134567'},
        //         success: function() {
        //             // alert(data);
        //             console.log(data);
        //         }
        //     });
        // });
        // $('#w0').submit(function (e) {
        //     e.preventDefault();
        //     alert('alo!');
        //     return false;
        // });

        // $('#ruproots-profile_code').on('change',function (e) {
        //     e.preventDefault();
        //     console.log('profile-change!');
        //     // $.ajax({
        //     //     type: 'GET',
        //     //     url: '/rup/rup/get-specialities',
        //     //     data: 'parent_code=135340', //+ $("input[name='Draw[date]_submit']").val().split("/").join("-") + '&self_id=0', //$('#draw-date').serialize(),
        //     //     success: function(data){
        //     //         $("#ruproots-spec_code").html( data );
        //     //     }
        //     // });
        // });
</script> 
