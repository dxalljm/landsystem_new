<?php

use yii\helpers\Html;
use yii\widgets\ActiveFormrdiv;

/* @var $this yii\web\View */
/* @var $model app\models\Machinescanning */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="machinescanning-form">

    <?php $form = ActiveFormrdiv::begin(); ?>
<table class="table table-bordered table-hover">
		<tr>
<td width=15% align='right'>农场ID</td>
<td align='left'><?= $form->field($model, 'farms_id')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>法人身份证</td>
<td align='left'><?= $form->field($model, 'cardid')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>创建日期</td>
<td align='left'><?= $form->field($model, 'create_at')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>更新日期</td>
<td align='left'><?= $form->field($model, 'update_at')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>扫描件</td>
<td align='left'><?= $form->field($model, 'scanimage')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>页码</td>
<td align='left'><?= $form->field($model, 'pagenumber')->textInput()->label(false)->error(false) ?></td>
</tr>
</table>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveFormrdiv::end(); ?>

</div>
