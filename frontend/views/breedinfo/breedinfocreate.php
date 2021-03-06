<?php
namespace frontend\controllers;use app\models\User;
use app\models\Tables;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Breedinfo */

$this->title = 'breedinfo' ;
$title = Tables::find()->where(['tablename'=>$this->title])->one()['Ctablename'];
$this->title = '添加'.$title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['breedinfoindex']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="breedinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('breedinfo_form', [
        'model' => $model,
    ]) ?>

</div>
