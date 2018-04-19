<?php

use yii\helpers\Html;
use yii\widgets\ActiveFormrdiv;

/* @var $this yii\web\View */
/* @var $model app\models\Machineapply */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="machineapply-form">

    <?php $form = ActiveFormrdiv::begin(); ?>
<table class="table table-bordered table-hover">
		<tr>
<td width=15% align='right'>农场ID</td>
<td align='left'><?= $form->field($model, 'farms_id')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>法人姓名</td>
<td align='left'><?= $form->field($model, 'farmername')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>年龄</td>
<td align='left'><?= $form->field($model, 'age')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>性别</td>
<td align='left'><?= $form->field($model, 'sex')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>户籍所在地</td>
<td align='left'><?= $form->field($model, 'domicile')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>管理区</td>
<td align='left'><?= $form->field($model, 'management_area')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>法人身份证</td>
<td align='left'><?= $form->field($model, 'cardid')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>联系电话</td>
<td align='left'><?= $form->field($model, 'telephone')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>农机</td>
<td align='left'><?= $form->field($model, 'machineoffarm_id')->textInput()->label(false)->error(false) ?></td>
</tr>
<tr>
<td width=15% align='right'>法人拼音首字母</td>
<td align='left'><?= $form->field($model, 'farmerpinyin')->textInput(['maxlength' => 500])->label(false)->error(false) ?></td>
</tr>
</table>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveFormrdiv::end(); ?>

</div>
