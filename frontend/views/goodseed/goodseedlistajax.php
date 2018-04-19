<?php
namespace frontend\controllers;
use app\models\Plant;
use app\models\User;
use app\models\Tables;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Goodseed */

?>
<div class="goodseed">

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3>&nbsp;&nbsp;&nbsp;&nbsp;<?= Plant::findOne($plant_id)->typename; ?></h3></div>
                <div class="box-body">
                    <?php
                    echo Html::hiddenInput('plant_id',$plant_id,['id'=>'plant-id']);
                    echo Html::hiddenInput('planter',$planter,['id'=>'Planter']);
                    echo Html::hiddenInput('type',$type,['id'=>'Type']);
                    echo Html::hiddenInput('id',$id,['id'=>'ID']);
                    echo Html::hiddenInput('input',$input,['id'=>'Input']);
                    echo '<table>';
                    echo '<tr>';
                    echo '<td width="20%" align="right">良种:</td>';
                    echo '<td>';
                    echo Html::textInput('Goodseed',$goodseedtypename,['list'=>'selectList','id'=>'goodseedtype']);
                    echo '<datalist id="selectList">';

		            foreach ($goodseedlist as $value) {
                        echo '<option>'.$value.'</option>';
                    }
                    echo '</datalist>';
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
