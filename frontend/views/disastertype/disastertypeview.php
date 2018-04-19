<?php
namespace frontend\controllers;use app\models\User;
use app\models\Tables;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Disastertype */

$this->title = 'ID:'.$model->id;
$title = Tables::find()->where(['tablename'=>'disastertype'])->one()['Ctablename'];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['disastertypeindex']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disastertype-view">

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                                            </h3>
                </div>
                <div class="box-body">

    <p>
    	 <?= Html::a('添加', ['disastertypecreate', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('更新', ['disastertypeupdate', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['disastertypedelete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '您确定要删除这项吗？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'typename',
        ],
    ]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
