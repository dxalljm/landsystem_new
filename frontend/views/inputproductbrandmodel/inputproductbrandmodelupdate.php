<?php
namespace frontend\controllers;use app\models\User;
use app\models\Tables;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Inputproductbrandmodel */

$this->title = 'inputproductbrandmodel' ;
$this->title = Tables::find()->where(['tablename'=>$this->title])->one()['Ctablename'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['inputproductbrandmodelindex']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['inputproductbrandmodelview', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="inputproductbrandmodel-update">

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                                            </h3>
                </div>
                <div class="box-body">

    <?= $this->render('inputproductbrandmodel_form', [
        'model' => $model,
    ]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
