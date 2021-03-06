<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\helpers;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Reviewprocess;
use yii\grid\Column;
use app\models\User;
/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 *
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\ActionColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class eActionColumn extends Column
{
	public $farms_id = null;
	public $id = NULL;
	public $other = false;
    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;
    /**
     * @var string the template used for composing each cell in the action column.
     * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names*
     * in the context of action column). They will be replaced by the corresponding button rendering callbacks
     * specified in [[buttons]]. For example, the token `{view}` will be replaced by the result of
     * the callback `buttons['view']`. If a callback cannot be found, the token will be replaced with an empty string.
     * @see buttons
     */
    public $template = '{view} {update} {delete}';
    /**
     * @var array button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * function ($url, $model, $key) {
     *     // return the button HTML code
     * }
     * ```
     *
     * where `$url` is the URL that the column creates for the button, `$model` is the model object
     * being rendered for the current row, and `$key` is the key of the model in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the model is
     * editable (here assuming you have a status field that indicates that):
     *
     * ```php
     * [
     *     'update' => function ($url, $model, $key) {
     *         return $model->status == 'editable' ? Html::a('Update', $url) : '';
     *     };
     * ],
     * ```
     */
    public $buttons = [];
    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(isset($_GET['farms_id']))
        	$this->farms_id = $_GET['farms_id'];
        $this->controller = Yii::$app->controller->id;
        $this->initDefaultButtons();
    }

    /**
     * Initializes the default button rendering callbacks
     */
    protected function addButtons($action,$buttonName,$newurl = NULL,$option = NULL)
    {
    	if(\Yii::$app->user->can($action)){
	        if (!isset($this->buttons[$buttonName])) {
	            $this->buttons[$buttonName] = function ($url, $model) {
	            	if(!empty($this->farms_id))
	            		$url.='&farms_id='.$this->farms_id;
	            	if($url)
	            		$url = $newurl;
		            return Html::a($option['option'], $url, [
		                'title' => Yii::t('yii', $option['title']),
		                'data-pjax' => '0',
		        	]);
	            };
	        }
    	}
    }
	protected function initDefaultButtons()
	{
//     	$this->$id;
		$action = $this->controller.'view';
//		var_dump($this->getUserRole($action));
		if($this->getUserRole($action)){
			if (!isset($this->buttons['view'])) {
				$this->buttons['view'] = function ($url, $model) {
					if(!empty($this->farms_id))
						$url.='&farms_id='.$this->farms_id;
					return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
						'title' => Yii::t('yii', '查看'),
						'data-pjax' => '0',
					]);
				};
			}
		}
		
		$action = $this->controller.'update';
		if($this->getUserRole($action)){
			if (!isset($this->buttons['update'])) {
				$this->buttons['update'] = function ($url, $model) {
					if(!empty($this->farms_id))
						$url.='&farms_id='.$this->farms_id;
					$state = 1;
					if($this->controller == 'projectapplication') {
						$state = Reviewprocess::find()->where(['id'=>$model->reviewprocess_id])->one()['state'];
					}
					if(User::getItemname('法规科')) {
						$url = Url::to(['farms/farmsadminupdate','id'=>$model->id]);
					}
					if(User::disabled()) {
						$url = '#';
					}
					if($state !== 7) {
						if(User::disabled()) {
							return '';
						} else {
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
								'title' => Yii::t('yii', '更新'),
								'data-pjax' => '0',
							]);
						}
					}
				};
			}
		}
		$action = $this->controller.'delete';
		if($this->getUserRole($action)){
			if (!isset($this->buttons['delete'])) {
				$this->buttons['delete'] = function ($url, $model) {
					if(!empty($this->farms_id))
						$url.='&farms_id='.$this->farms_id;
					$state = 1;
					if($this->controller == 'projectapplication') {
						$state = Reviewprocess::find()->where(['id'=>$model->reviewprocess_id])->one()['state'];
					}

					if($state !== 7) {
						if(User::disabled()) {
							return '';
						} else {
							return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
								'title' => Yii::t('yii', '删除'),
								'data-confirm' => Yii::t('yii', '确定要删除此项吗?'),
								'data-method' => 'post',
								'data-pjax' => '0',
							]);
						}
					}
				};
			}
		}
	}


	protected function getUserRole($action)
	{
		$plateArray = User::getPlateRole();
//		var_dump($plateArray);exit;
//		$plateArray[] = 'log-view';
		$nowController = Yii::$app->controller->id;
		$data = ['nation','plant','inputproduct','inputproductbrandmodel','pesticides','goodseed','cooperative','disputetype','breedtype','infrastructuretype','projecttype','disastertype','machinetype','insurancecompany'];
		if(in_array($nowController,$data)) {
			return true;
		}
//		if()
//		return in_array($nowController,$plateArray);
		foreach ($plateArray as $key => $plate) {
			if($key == $nowController) {
				foreach ($plate as $p) {
					$tempaction = $key.$p;
					if($action == $tempaction) {
						return true;
					}
				}
			}
		}
		return false;
	}
    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
       	$action = Yii::$app->controller->id.$action;
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
            return Url::toRoute($params);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);

                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }
        }, $this->template);
    }
}
