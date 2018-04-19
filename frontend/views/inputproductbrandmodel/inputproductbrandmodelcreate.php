<?php
namespace frontend\controllers;use app\models\User;
use app\models\Tables;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Inputproductbrandmodel */

$this->title = 'inputproductbrandmodel' ;
$title = Tables::find()->where(['tablename'=>$this->title])->one()['Ctablename'];
$this->title = '添加'.$title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['inputproductbrandmodelindex']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inputproductbrandmodel-create">

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
