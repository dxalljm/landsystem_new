<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projecttypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="projecttype-search">

    <?php $form = ActiveForm::begin([
        'action' => ['projecttypeindex'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'father_id') ?>

    <?= $form->field($model, 'typename') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
