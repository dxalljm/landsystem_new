<?php

use yii\helpers\Html;
use yii\widgets\ActiveFormrdiv;

/* @var $this yii\web\View */
/* @var $model app\models\Lockstate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lockstate-form">

    <?php $form = ActiveFormrdiv::begin(); ?>
<table class="table table-bordered table-hover">
		<tr>
<td width=15% align='right'>系统状态</td>
<td align='left'><?= $form->field($model, 'systemstate')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>系统锁定时间</td>
<td align='left'><?= $form->field($model, 'systemstatedate')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>板块锁定状态</td>
<td align='left'><?= $form->field($model, 'platestate')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>贷款配置项</td>
<td align='left'><?= $form->field($model, 'loanconfig')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>贷款配置冻结日期</td>
<td align='left'><?= $form->field($model, 'loanconfigdate')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>过户配置项</td>
<td align='left'><?= $form->field($model, 'transferconfig')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>过户冻结日期</td>
<td align='left'><?= $form->field($model, 'transferconfigdate')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
</table>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveFormrdiv::end(); ?>

</div>
