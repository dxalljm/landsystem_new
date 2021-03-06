<?php

namespace frontend\controllers;

use app\models\Breedinfo;
use app\models\Collection;
use app\models\Farmerinfo;
use app\models\Fireprevention;
use app\models\Fixed;
use app\models\Indexecharts;
use app\models\Insuranceplan;
use app\models\Loan;
use app\models\Lockedinfo;
use app\models\Machine;
use app\models\Machineapply;
use app\models\Machineoffarm;
use app\models\Plantingstructurecheck;
use app\models\Plantingstructureyearfarmsid;
use app\models\Plantingstructureyearfarmsidplan;
use app\models\Reviewprocess;
use app\models\Sales;
use app\models\Ttpozongdi;
use console\models\PlantPrice;
use frontend\helpers\fileUtil;
use frontend\models\employeeSearch;
use frontend\models\TempauditingSearch;
use Yii;
use app\models\Yields;
use frontend\models\yieldsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Plantingstructure;
use app\models\Farms;
use app\models\Theyear;
use frontend\models\plantingstructureSearch;
use app\models\Lease;
use app\models\Parcel;
use app\models\Tempprintbill;
use frontend\helpers\MoneyFormat;
use app\models\Insurance;
use app\models\User;
use \PHPExcel_IOFactory;
/**
 * YieldsController implements the CRUD actions for Yields model.
 */
class TestController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionTest()
    {
        $sum = 0.0;
        $i=0;
        $id = [];
        $farms = Farms::find()->where(['management_area'=>1,'otherstate'=>[6,7]])->all();
//        var_dump($farms);exit;
        foreach ($farms as $farm) {
            $collection = Collection::find()->select(['id'])->where(['payyear'=>'2016','farms_id'=>$farm['id'],'state'=>1,'dckpay'=>0])->one();
//            var_dump($collection);
            if($collection) {
                $i++;
                $id['farms_id'][] = $farm['id'];
                $id['collection_id'][] = $collection['id'];
                $collectionModel = Collection::findOne($collection['id']);
                $sum += $collectionModel->amounts_receivable;
                $collectionModel->delete();
            }
        }
        var_dump($id);
        var_dump($i);
        var_dump($sum);
    }

    public function actionCollectionowe()
    {
        $collections = Collection::find()->where(['payyear'=>'2016','state'=>1,'dckpay'=>1])->all();
        foreach ($collections as $collection) {
            $model = Collection::findOne($collection['id']);
            if($collection['ypayarea'] !== 0.00) {
                $model->ypayarea = 0.00;
            }
            if($collection['ypaymoney'] !== 0.00) {
                $model->ypaymoney = 0.00;
            }
            if($collection['owe'] !== 0.00) {
                $model->owe = 0.00;
            }
            $model->save();
            echo '------------------------';
        }
    }
    
    public function actionGetrnom()
    {
        $all = [];
        $collections = Collection::find()->where(['payyear'=>'2016','management_area'=>1])->all();
        foreach ($collections as $collection) {
            if($collection['amounts_receivable'] !== $collection['real_income_amount']) {
                $all[] = $collection['farms_id'];
            }
        }
        var_dump($all);
    }

    public function actionBj()
    {
        $all = [];
        $farms = Farms::find()->where(['management_area'=>4])->all();
        foreach ($farms as $farm) {
            $coll = Collection::find()->where(['farms_id'=>$farm['id'],'payyear'=>'2016'])->one();
            if(!$coll) {
                $all[] = $farm;
                var_dump(date('Y-m-d',$farm['create_at']));
               var_dump($farm);
            }
        }
        var_dump(count($all));
    }

    public function actionFbj()
    {
        $all=[];
        $collections = Collection::find()->where(['management_area'=>4,'payyear'=>'2016'])->andWhere('farms_id<>0')->all();
        $farms = Farms::find()->where(['management_area'=>4,'state'=>1])->all();
        var_dump(count($farms));
        var_dump(count($collections));
        foreach ($collections as $collection) {
            $cid[] = $collection['farms_id'];
        }
        foreach ($farms as $farm) {
            $fid[] = $farm['id'];
        }
        $result = array_diff($cid,$fid);
        var_dump($result);
//        var_dump($all);
    }

    public function actionCollectioncl2016()
    {
        $new = [];
        $old = [];
        $newis = false;
        $oldis = false;
        $reviewprocesss = Reviewprocess::find()->where(['actionname'=>'farmstransfer','state'=>[6,7],'management_area'=>4])->all();
//        var_dump($reviewprocesss);
//        $price = PlantPrice::find()->all();
        foreach ($reviewprocesss as $reviewprocess) {
            if($reviewprocess['samefarms_id']) {
                $oldfarm = Farms::findOne($reviewprocess['samefarms_id']);
                $newfarm = Farms::findOne($reviewprocess['newfarms_id']);
            } else {
                $newfarm = Farms::findOne($reviewprocess['newfarms_id']);
                $oldfarm = Farms::findOne($reviewprocess['oldfarms_id']);
            }
            $year = '2016';
            $price = PlantPrice::find()->where(['years'=>$year])->one()['price'];

                        $newcollection = Collection::find()->where(['farms_id' => $newfarm['id'], 'payyear' => $year])->one();
                        $oldcollection = Collection::find()->where(['farms_id' => $oldfarm['id'], 'payyear' => $year])->one();
                        $new[$newfarm['id']] = $newcollection['id'];
                        $old[$oldfarm['id']] = $oldcollection['id'];

                        if($newcollection) {
                            if($oldcollection['dckpay'] == 1 and $oldcollection['state'] == 1) {
                                $newmodel = Collection::findOne($newcollection['id']);
                                $newmodel->update_at = time();
                                $newmodel->payyear = $year;
                                $newmodel->ypayyear = $year;
                                $newmodel->farms_id = $newfarm['id'];
                                $newmodel->amounts_receivable = 0.00;
                                $newmodel->real_income_amount = 0.00;
                                $newmodel->ypayarea = 0.00;
                                $newmodel->ypaymoney = 0.00;
                                $newmodel->measure = 0.00;
                                $newmodel->dckpay = 1;
                                $newmodel->state = 1;
                                $newmodel->management_area = $newfarm['management_area'];
                                $newmodel->owe = 0.00;
                                $newmodel->save();
                            }
                        } else {
                            if($oldcollection['dckpay'] == 1 and $oldcollection['state'] == 1) {
                                $newmodel = new Collection();
                                $newmodel->create_at = time();
                                $newmodel->update_at = $newmodel->create_at;
                                $newmodel->payyear = $year;
                                $newmodel->farms_id = $newfarm['id'];
                                $newmodel->amounts_receivable = 0.00;
                                $newmodel->real_income_amount = 0.0;
                                $newmodel->ypayarea = 0.00;
                                $newmodel->ypaymoney = 0.00;
                                $newmodel->owe = 0.00;
                                $newmodel->ypayyear = $year;
                                $newmodel->measure = 0.00;
                                $newmodel->dckpay = 1;
                                $newmodel->state = 1 ;
                                $newmodel->management_area = $newfarm['management_area'];
                                if ($newmodel->save())
                                    echo $newmodel->id . 'yes';
                            } else {
                                $newmodel = new Collection();
                                $newmodel->create_at = time();
                                $newmodel->update_at = $newmodel->create_at;
                                $newmodel->payyear = $year;
                                $newmodel->farms_id = $newfarm['id'];
                                $newmodel->amounts_receivable = $newmodel->getAR($year, $newfarm['id']);
                                $newmodel->real_income_amount = 0.0;
                                $newmodel->ypayarea = $newfarm['contractarea'];
                                $newmodel->ypaymoney = bcmul($newfarm['contractarea'], $price, 2);
                                $newmodel->owe =$newmodel->amounts_receivable;
                                $newmodel->measure = 0.00;
                                $newmodel->dckpay = 0;
                                $newmodel->state = 0;
                                $newmodel->management_area = $newfarm['management_area'];
                                $newmodel->save();
                            }
                        }
                        if ($oldcollection) {
//                            var_dump($reviewprocess);
                            $oldmodel = Collection::findOne($oldcollection['id']);
                            if ($oldmodel->dckpay !== 1 and $oldmodel->state !== 1) {
                                var_dump($oldmodel->id);
                                $oldmodel->delete();

                            }
                        }
                    }

        var_dump($new);
        var_dump($old);
        echo 'finished';
    }

    public function actionCollectioncl2017()
    {
        $reviewprocesss = Reviewprocess::find()->where(['actionname'=>'farmstransfer','state'=>[6,7],'management_area'=>4])->all();
//        var_dump($reviewprocesss);
//        $price = PlantPrice::find()->all();
        foreach ($reviewprocesss as $reviewprocess) {
            if($reviewprocess['samefarms_id']) {
                $oldfarm = Farms::findOne($reviewprocess['samefarms_id']);
                $newfarm = Farms::findOne($reviewprocess['newfarms_id']);
            } else {
                $newfarm = Farms::findOne($reviewprocess['newfarms_id']);
                $oldfarm = Farms::findOne($reviewprocess['oldfarms_id']);
            }
            $year = '2017';
            $price = PlantPrice::find()->where(['years'=>$year])->one()['price'];
            $newcollection = Collection::find()->where(['farms_id' => $newfarm['id'], 'payyear' => $year])->one();
            $oldcollection = Collection::find()->where(['farms_id' => $oldfarm['id'], 'payyear' => $year])->one();
            if($oldcollection) {

                $oldmodel = Collection::findOne($oldcollection['id']);
                $oldmodel->delete();
            }
            if (!$newcollection) {
                $newmodel = new Collection();
                $newmodel->create_at = time();
                $newmodel->update_at = $newmodel->create_at;
                $newmodel->payyear = $year;
                $newmodel->farms_id = $newfarm['id'];
                $newmodel->amounts_receivable = $newmodel->getAR($year, $newfarm['id']);
                $newmodel->real_income_amount = 0.0;
                $newmodel->ypayarea = $newfarm['contractarea'];
                $newmodel->ypaymoney = bcmul($newfarm['contractarea'], $price, 2);
                $newmodel->owe = 0.00;
                $newmodel->measure = 0.00;
                $newmodel->dckpay = 0;
                $newmodel->state = 0;
                $newmodel->management_area = $newfarm['management_area'];
                $newmodel->save();
            }
        }
        echo 'finished';
    }


    public function actionFarmstocollection($year)
    {
        $farms = Farms::find()->all();
        foreach ($farms as $farm) {
            $coll = Collection::find()->where(['farms_id' => $farm['id'],'payyear'=>$year])->one();
            if($farm['state'] == 0) {
                if ($coll) {
                    if ($coll['dckpay'] !== 1 and $coll['state'] !== 1) {
                        $collModel = Collection::findOne($coll['id']);
                        $collModel->delete();
                    }
                }
            } else {
                if($coll) {
                    
                }
            }
        }
        echo 'finished';
    }

   public function actionFarmbjcoll()
   {
       $result = [];
       $result2 = [];
       $farms = Farms::find()->where(['management_area'=>5])->all();
       foreach ($farms as $farm) {
           $coll = Collection::find()->where(['farms_id'=>$farm['id'],'payyear'=>'2016'])->one();
           if($coll) {
               if ($farm['contractarea'] !== $coll['measure']) {
                   $result[$farm['farmername']][] = [$farm['id'] => $farm['contractarea']];
               }
           }
       }
//       foreach ($farms as $farm) {
//           $coll2 = Collection::find()->where(['farms_id'=>$farm['id'],'payyear'=>'2016'])->one();
//           if(!$coll2) {
//               $result2[] = $farm;
//           }
//       }
       var_dump($result);
       var_dump($result2);
   }

   public function actionLoaninfo()
   {
       $loans = Loan::find()->all();
       foreach ($loans as $loan) {
           $farm = Farms::findOne($loan['farms_id']);
           if($farm->locked) {
               $lockinfo = Lockedinfo::find()->where(['farms_id'=>$farm->id])->orderBy('id desc')->one();
               if(strstr($lockinfo->lockedcontent,'贷款')) {
                   $model = Loan::findOne($loan['id']);
                   $model->lock = 1;
                   $model->save();
               }
           }
       }
       echo 'finished';
   }
   
   public function actionFarmsstatearea()
   {
   		$farms = Farms::find()->where(['state'=>[1,2,3,4,5]])->all();
   		foreach ($farms as $farm) {
//    			if(($farm['measure'] - $farm['contractarea']) !== $farm['notstate']) {
//    				$result[] = $farm;
			$model = Farms::findOne($farm['id']);
			if($farm['measure'] > $farm['contractarea']) {
   				$model->notstate = (float)bcsub($farm['measure'],$farm['contractarea'],2);
			} else {
				$model->notstate = 0;
			}
			if($farm['measure'] < $farm['contractarea']) {
				$model->notclear = (float)bcsub($farm['contractarea'],$farm['measure'],2);
			} else {
				$model->notclear = 0;
			}
   			$model->save(); 
//    			}
   		}
   		echo 'finished';
   }
   
   public function actionFarmsarea()
   {
   		$result = [];
   		$farms = Farms::find()->where(['state'=>1])->andWhere('notclear > 0')->all();
	   	foreach ($farms as $farm) {
	   		$model = Farms::findOne($farm['id']);
	   		if(!empty($model->zongdi)) {
// 	   			$result[] = $farm;
		   		$zongdiArray = explode('、', $model->zongdi);
		   		$newArray = [];
		   		foreach ($zongdiArray as $zongdi) {
		   			$zongdiNumber = Lease::getZongdi($zongdi);
		   			$zongdiArea = Lease::getArea($zongdi);
		   			$parce = Parcel::find()->where(['unifiedserialnumber'=>$zongdiNumber])->one();
		   			if($zongdiArea > $parce['netarea']) {
		   				$newArray[] = $zongdiNumber.'('.$parce['netarea'].')';
		   			}
		   		}
		   		$model->zongdi = implode('、', $newArray);
		   		$model->measure = Lease::getZongdi($model->zongdi);
		   		if($farm['measure'] > $farm['contractarea']) {
// 		   			$result[] = $farm;
		   			$model->notstate = (float)bcsub($farm['measure'],$farm['contractarea'],2);
		   		} else {
		   			$model->notstate = 0;
		   		}
		   		if($farm['measure'] < $farm['contractarea']) {
		   			$model->notclear = (float)bcsub($farm['contractarea'],$farm['measure'],2);
		   		} else {
		   			$model->notstate = 0;
		   		}
// 		   		$model->save();
	   		}
	   	}
	   	var_dump($result);
	   	echo 'finished';
   }
   
   public function actionSetinfo()
   {
   		$array = [1894,1020,1452,1469,1483,507,1497,499,1495,641,584,616,1523,1536,878,1559,1560];
   		foreach ($array as $id) {
   			$loan = Loan::find()->where(['farms_id'=>$id])->one();
   			var_dump($loan);
   			$model = Loan::findOne($loan['id']);
   			$model->lock = 1;
//    			$model->save();
   		}
   		echo 'finished loan';
   }
   
   public function actionLoan()
   {
   		$loan = Loan::find()->all();
   		foreach ($loan as $value) {
   			$farm = Farms::find()->where(['id'=>$value['farms_id']])->one();
   			if($farm['locked'] == 1) {
   				$model = Loan::findOne($value['id']);
   				$model->lock = 1;
   				$model->save();
   			}
   		}
   		echo 'finished';
   }
   
   public function actionTempmod()
   {
   		$temp = Tempprintbill::find()->all();
   		foreach ($temp as $value) {
   			$tmodel = Tempprintbill::findOne($value['id']);
   			$tmodel->amountofmoney = (string)MoneyFormat::toNumber($tmodel->amountofmoney);
   			var_dump($tmodel->amountofmoney);
   			$tmodel->save();
   			var_dump($tmodel->getErrors());
   		}
   		echo 'finished';
   }
   
   public function actionItest()
   {
   		$data = Insurance::find()->where(['year'=>'2017','state'=>1])->all();
   		$re = [];
   		foreach ($data as $value) {
   			$re[] = $value['farms_id'];
   		}
   		var_dump(count(array_unique($re)));
   		exit;
//    		$insuranceAll = Insurance::find()->where(['year'=>2017])->all();
//    		foreach ($insuranceAll as $value) {
//    			$model = Insurance::findOne($value['id']);
//    			$model->insured
//    		}
   }
   
   public function actionTest2()
   {
   		$ids = Farms::find()->andFilterWhere(['like','pinyin','jm'])->all();
   	    foreach ($ids as $id) {
   	    	$farms_id[] = $id['id'];
   	    }
   	    var_dump($farms_id);exit;
   }

    public function actionInsurancetest()
    {
        $insurances = Insurance::find()->where(['state'=>1,'management_area'=>6,'year'=>'2017'])->all();
//        $farms = Farms::find()->where(['state'=>['1,2,3,4'],])
        $sum = 0.0;
        $i=0;
        foreach ($insurances as $insurance) {
            $h = $insurance['insuredsoybean'] + $insurance['insuredwheat'] + $insurance['insuredother'];
            $farm = Farms::find()->where(['id'=>$insurance['farms_id']])->one();
            if(bccomp($farm['contractarea'], $h) <> 0) {
                $result[] = $insurance;
            }
        }
        var_dump($result);
    }
    
    public function actionInsurancecl()
    {
        $insurances = $insurances = Insurance::find()->where(['state'=>1,'year'=>'2017'])->all();
        foreach ($insurances as $insurance) {
            $model = Insurance::findOne($insurance['id']);
            $insurancearea = Insurance::find()->where(['farms_id'=>$insurance['farms_id'],'state'=>1,'year'=>'2017'])->all();
            $sum = 0.0;
            $othersum = 0.0;
            foreach($insurancearea as $value) {
                $sum += $value['insuredsoybean'] + $value['insuredwheat'] +  + $value['insuredother'];
                $othersum +=  + $value['insuredother'];
            }
            var_dump($insurance['id']);
            var_dump($sum);
            var_dump($insurance['contractarea']);
            var_dump('-----------');
            if(bccomp($sum , $insurance['contractarea'],2)==0 and $othersum == 0) {
                $model->iscontractarea = 1;
            } else {
                $model->iscontractarea = 0;
//                $model->other = bcsub($insurance['contractarea'],$sum,2);
//                $model->induredother = bcsub($insurance['contractarea'],$sum,2);
            }
//            $model->contractarea = Farms::find()->where(['id'=>$insurance['farms_id']])->one()['contractarea'];
            $model->save();
        }
        echo 'finished';
    }

    public function actionInsurancecl2()
    {
        $insurances = $insurances = Insurance::find()->where(['state'=>1,'year'=>'2017'])->all();
        foreach ($insurances as $insurance) {
            if(bccomp($insurance['insuredarea'],$insurance['contractarea'],2)== -1 and $insurance['iscontractarea'] == 1) {
                print_r($insurance);
            }
        }
        var_dump(bccomp(460.00,460.01,2));
        echo 'finished';
    }

    public function actionFarmslog()
    {
        $farms = Farms::find()->andFilterWhere(['like','longitude','E24'])->all();
        foreach($farms as $farm) {
            $model = Farms::findOne($farm['id']);
            $model->longitude = $this->insertToStr($model->longitude,1,'1');
            $model->save();
        }
        echo 'finished';
    }
    //$i 为插入位置
    //$substr  插入字符串
    public function insertToStr($str,$i,$substr)
    {
        $startstr = '';
        for($j=0;$j<$i;$j++)
        {
            $startstr .= $str[$j];
        }
        $laststr = '';
        for($j=$i;$j<strlen($str);$j++) {
            $laststr .= $str[$j];
        }

        $str = $startstr . $substr . $laststr;
        return $str;
    }

    public function actionPsetcontractarea()
    {
        $plantings = Plantingstructurecheck::find()->all();
        foreach ($plantings as $planting) {
            $model = Plantingstructurecheck::findOne($planting['id']);
            $model->state = Farms::find()->where(['id'=>$model->farms_id])->one()['state'];
            $model->save();
        }
        echo 'finished';
    }

    public function actionFarmscardid()
    {
        $farms = Farms::find()->all();
        foreach ($farms as $farm) {
            $farmerinfo = Farmerinfo::find()->where(['cardid'=>$farm['cardid']])->one();
            if($farmerinfo) {
                $model = Farmerinfo::findOne($farmerinfo['id']);
            }
        }

    }

    public function actionPlanttofarmsid()
    {
        $farms = Farms::find()->where(['state'=>[1,2,3,4,5]])->all();
        $time = time();
        foreach ($farms as $farm) {
            $plants = Plantingstructureyearfarmsid::find()->where(['farms_id'=>$farm['id'],'year'=>User::getYear()])->one();
            if($plants) {
                $model = Plantingstructureyearfarmsid::findOne($plants['id']);
            } else {
                $model = new Plantingstructureyearfarmsid();
            }
            $model->farms_id = $farm['id'];
            $model->farmname = $farm['farmname'];
            $model->farmername = $farm['farmername'];
            $model->contractarea = $farm['contractarea'];
            $model->contractnumber = $farm['contractnumber'];
            $model->year = User::getYear();
            $model->state = $farm['state'];
            $model->management_area = $farm['management_area'];
            $plantsum = Plantingstructurecheck::find()->where(['farms_id'=>$farm['id'],'year'=>User::getYear()])->sum('area');
            if(bccomp($farm['contractarea'],sprintf('%.2f',$plantsum)) == 0) {
                $model->isfinished = 1;
            } else {
                $model->isfinished = 0;
            }
            $model->create_at = $time;
            $model->save();
        }
        echo 'finished';
    }

    public function actionTextmkdir($dir)
    {
        echo fileUtil::createDir($dir);
    }


    public function actionCollectioncl()
    {
        $reviewprocess = Reviewprocess::find()->andFilterWhere(['like', 'actionname', 'farms'])->andFilterWhere(['between','create_at',strtotime('2017-01-01 00:00:00'),strtotime('2017-12-31 23:59:59')])->all();
        foreach ($reviewprocess as $value) {
            $ttpo = Ttpozongdi::findOne($value['ttpozongdi_id']);
            if ($ttpo->oldnewfarms_id) {
                $collection = Collection::find()->where(['farms_id' => $ttpo->oldfarms_id, 'payyear' => 2016])->one();
                $newcollection = Collection::find()->where(['farms_id' => $ttpo->oldnewfarms_id, 'payyear' => 2016])->one();

                $farm = Farms::findOne($ttpo->oldnewfarms_id);
                if ($collection['state']) {
                    if ($newcollection) {
                        $newcollection->delete();
                    }
                } else {
                    $newModel = new Collection();
                    $newModel->payyear = 2016;
                    $newModel->farms_id = $farm['id'];
                    if(empty($collection['amounts_receivable'])) {
                        $newModel->amounts_receivable = 0.0;
                    } else {
                        $newModel->amounts_receivable = $collection['amounts_receivable'];
                    }
                    if(empty($collection['real_income_amount'])) {
                        $newModel->real_income_amount = 0.0;
                    } else {
                        $newModel->real_income_amount = $collection['real_income_amount'];
                    }
                    if(empty($collection['owe'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['owe'];
                    }
                    if(empty($collection['ypayarea'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['ypayarea'];
                    }
                    if(empty($collection['ypaymoney'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['ypaymoney'];
                    }
                    if(empty($collection['measure'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['measure'];
                    }
                    $newModel->create_at = time();
                    $newModel->update_at = $newModel->create_at;
                    $newModel->dckpay = $collection['dckpay'];
                    $newModel->state = $collection['state'];
                    $newModel->management_area = $farm['management_area'];
                    $newModel->save();
                }
            }
            if ($ttpo->newnewfarms_id) {
                if(empty($ttpo->newfarms_id)) {
                    $collection = Collection::find()->where(['farms_id' => $ttpo->oldfarms_id, 'payyear' => 2016])->one();
                } else {
                    $collection = Collection::find()->where(['farms_id' => $ttpo->newfarms_id, 'payyear' => 2016])->one();
                }

                $newcollection = Collection::find()->where(['farms_id' => $ttpo->newnewfarms_id, 'payyear' => 2016])->one();

                $farm = Farms::findOne($ttpo->newnewfarms_id);
                if ($collection['state']) {
                    if ($newcollection) {
                        $newcollection->delete();
                    }
                } else {
                    $newModel = new Collection();

                    $newModel->farms_id = $farm['id'];
                    $newModel->payyear = 2016;
                    if(empty($collection['amounts_receivable'])) {
                        $newModel->amounts_receivable = 0.0;
                    } else {
                        $newModel->amounts_receivable = $collection['amounts_receivable'];
                    }
                    if(empty($collection['real_income_amount'])) {
                        $newModel->real_income_amount = 0.0;
                    } else {
                        $newModel->real_income_amount = $collection['real_income_amount'];
                    }
                    if(empty($collection['owe'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['owe'];
                    }
                    if(empty($collection['ypayarea'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['ypayarea'];
                    }
                    if(empty($collection['ypaymoney'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['ypaymoney'];
                    }
                    if(empty($collection['measure'])) {
                        $newModel->owe = 0.0;
                    } else {
                        $newModel->owe = $collection['measure'];
                    }
                    $newModel->create_at = time();
                    $newModel->update_at = $newModel->create_at;
                    $newModel->dckpay = $collection['dckpay'];
                    $newModel->state = $collection['state'];
                    $newModel->management_area = $farm['management_area'];
                    $newModel->save();
                }
            }
        }
        echo 'finished';
    }

    public function actionCollcl()
    {
        $temps = Tempprintbill::find()->all();
        foreach ($temps as $temp) {
            if($temp['farms_id'] > 0) {
                $collection = Collection::find()->where(['farms_id'=>$temp['farms_id'],'payyear'=>$temp['year']])->one();
                var_dump($collection);
                $farm = Farms::findOne($temp['farms_id']);
                if(empty($collection)) {
                    var_dump($collection);
                    $model = new Collection();
                    $model->payyear = $temp['year'];
                    $model->farms_id = $temp['farms_id'];
                    $model->amounts_receivable = $temp['amountofmoney'];
                    $model->real_income_amount = $temp['amountofmoney'];
                    $model->ypayarea = 0.0;
                    $model->ypaymoney = 0.0;
                    $owe = $farm['contractarea']*PlantPrice::find()->where(['years'=>$temp['year']])->one()['price'] - $temp['amountofmoney'];
                    $model->owe = sprintf('%.2f',$owe);
                    $model->create_at = $temp['create_at'];
                    $model->update_at = $temp['update_at'];
                    $model->dckpay = 1;
                    $model->state = 1;
                    $model->management_area = $temp['management_area'];
                    $model->save();
                    var_dump($model);
                }
            }
        }
        echo 'finished';
    }

    public function actionCollectionstate()
    {
        $collections = Collection::find()->where(['payyear'=>2016])->all();
        foreach($collections as $collection) {
            $farm = Farms::findOne($collection['farms_id']);
            if($farm['state'] == 2 or $farm['state'] == 3) {
                $model = Collection::findOne($collection['id']);
                $model->delete();
            }
        }
        echo 'finished';
    }

    public function actionCollectionlist()
    {
        $collections = Collection::find()->andFilterWhere(['between','create_at',strtotime('2017-01-01 00:00:00'),strtotime('2017-12-31 23:59:59')])->all();
        foreach($collections as $collection) {
            $review = Reviewprocess::find()->where(['newfarms_id'=>$collection['farms_id']])->andFilterWhere(['like', 'actionname', 'farms'])->one();
            if($review) {
                if($review['samefarms_id']) {

                } else {
                    if($review['newfarms_id']) {
                        $coll = Collection::find()->where(['farms_id'=>$review['newfarms_id'],'payyear'=>2016])->one();
                        $coll->delete();
                    } else {
                        $coll = Collection::find()->where(['farms_id'=>$review['oldfarms_id'],'payyear'=>2016])->one();
                        $coll->delete();
                    }

                }
            }
        }
    }

//    public function actionCollection2017()
//    {
//        $farms = Farms::find()->andFilterWhere(['between','create_at',strtotime('2017-01-01 00:00:00'),strtotime('2017-12-31 23:59:59')])->andFilterWhere(['state'=>[1,2,3]])->all();
//        foreach($farms as $farm) {
//            $collection = Collection::find()->where(['farms_id'=>$farm['id'],'payyear'=>2016])->one();
//            if($collection) {
//                $reviewprocess = Reviewprocess::find()->where(['newfarms_id' => $farm['id']])->one();
//                if ($reviewprocess) {
//                    $oldcoll = Collection::find()->where(['farms_id' => $reviewprocess['oldfarms_id'], 'payyear' => 2016])->one();
//                    if ($oldcoll) {
//                        if ($oldcoll['state']) {
//                            $collection->delete();
//                        }
//                    }
//                }
//            } else {
//                $collection->delete();
//            }
//        }
//    }

    public function actionCollection2017()
    {
        $n=0;
        $collections = Collection::find()->where(['state'=>0,'payyear'=>2016])->all();
        foreach($collections as $collection) {
            $reviewprocess = Reviewprocess::find()->where(['newfarms_id'=>$collection['farms_id']])->one();
            if($reviewprocess) {
                $oldcoll = Collection::find()->where(['payyear'=>2016,'farms_id' => $reviewprocess['oldfarms_id']])->one();
                if($oldcoll) {
                    $temp = Tempprintbill::find()->where(['year'=> 2016,'farms_id'=>$reviewprocess['oldfarms_id']])->one();
                    if($temp) {
                        $farm = Farms::findOne($collection['farms_id']);
                        if($farm['create_at'] > strtotime('2017-01-01 00:00:00')) {
//                            var_dump($collection['id'].'---'.$collection['farms_id']);
//                        var_dump($collection);
                            $model = Collection::findOne($collection['id']);
                            $model->delete();
                        }
                    } else {
                        var_dump($collection['id'].'---'.$collection['farms_id']);
                        $newModel = Collection::findOne($collection['id']);
//                        $newModel->payyear = 2016;
//                        $newModel->farms_id = $farm['id'];
//                        if(empty($collection['amounts_receivable'])) {
//                            $newModel->amounts_receivable = 0.0;
//                        } else {
                            $newModel->amounts_receivable = $oldcoll['amounts_receivable'];
//                        }
//                        if(empty($collection['real_income_amount'])) {
//                            $newModel->real_income_amount = 0.0;
//                        } else {
                            $newModel->real_income_amount = $oldcoll['real_income_amount'];
//                        }
//                        if(empty($collection['owe'])) {
//                            $newModel->owe = 0.0;
//                        } else {
                            $newModel->owe = $oldcoll['owe'];
//                        }
//                        if(empty($collection['ypayarea'])) {
//                            $newModel->owe = 0.0;
//                        } else {
                            $newModel->owe = $oldcoll['ypayarea'];
//                        }
//                        if(empty($collection['ypaymoney'])) {
//                            $newModel->owe = 0.0;
//                        } else {
                            $newModel->owe = $oldcoll['ypaymoney'];
//                        }
//                        if(empty($collection['measure'])) {
//                            $newModel->owe = 0.0;
//                        } else {
                            $newModel->owe = $oldcoll['measure'];
//                        }
//                        $newModel->create_at = time();
//                        $newModel->update_at = $newModel->create_at;
                        $newModel->dckpay = $collection['dckpay'];
                        $newModel->state = $collection['state'];
//                        $newModel->management_area = $farm['management_area'];
                        $newModel->save();
//                    $model->save();
                    }
                }
            } else {
                $temp = Tempprintbill::find()->where(['year'=> 2016,'farms_id'=>$collection['farms_id']])->one();
                if($temp) {

                    $model = Collection::findOne($collection['id']);
                    $model->real_income_amount = $temp['amountofmoney'];
                    $model->ypayarea = 0.0;
                    $model->ypaymoney = 0.0;
                    $model->dckpay = 1;
                    $model->state = 1;
                    $model->owe = 0.0;
                    $model->save();
                } else {

                    $farm = Farms::findOne($collection['farms_id']);
                    $model = Collection::findOne($collection['id']);
                    if($farm['create_at'] > strtotime('2017-01-01 00:00:00')) {
//                        var_dump($collection['id'].'---'.$collection['farms_id']);
//                        var_dump($collection);
                        $model->delete();
                    }
                }
            }
        }
        var_dump($n);
    }

    public function actionCollm()
    {
        $farms = Farms::find()->where(['state'=>[1,2,3]])->all();
        foreach($farms as $farm) {
            $collection = Collection::find()->where(['farms_id'=>$farm['id'],'payyear'=>date('Y')])->one();
            if(empty($collection)) {
                $model = new Collection();
                $model->payyear = date('Y');
                $model->farms_id = $farm['id'];
                $model->amounts_receivable = $farm['contractarea']*PlantPrice::find()->where(['years'=>date('Y')])->one()['price'];
                $model->real_income_amount = 0.0;
                $model->ypayarea = $farm['contractarea'];
                $model->ypaymoney = $model->amounts_receivable;
                $model->owe = 0.0;
                $model->create_at = time();
                $model->update_at = $model->update_at;
                $model->dckpay = 0;
                $model->state = 0;
                $model->management_area = $farm['management_area'];
                $model->save();
            }
        }
        echo 'finished';
    }

    public function actionLoantoreview()
    {
        $loans = Loan::find()->all();
        foreach($loans as $loan) {
            $review = Reviewprocess::findOne($loan['reviewprocess_id']);
            if($review) {
                $review->estatetime = $loan['create_at'];
                $review->save();
            }
        }
        echo 'finished';
    }

    public function actionChecktop()
    {
        $plantcheck = Plantingstructurecheck::find()->all();
        foreach ($plantcheck as $plant) {
            $model = Plantingstructurecheck::findOne($plant['id']);
            $model->issame = 1;
            $model->save();
        }
//        $plants = Plantingstructure::find()->all();
//        foreach ($plants as $plant) {
//            $model = Plantingstructure::findOne($plant['id']);
//            $model->year = 2016;
//            $model->state = Farms::find()->where(['id'=>$plant['farms_id']])->one()['state'];
//            $model->save();
//        }
        echo 'finished';
    }

    public function actionTempkptime()
    {
        $Temps = Tempprintbill::find()->all();
        foreach ($Temps as $temp) {
            $model = Tempprintbill::findOne($temp['id']);
            $model->kptime = strtotime(date('Y-m-d'),$model->create_at);
            $model->save();
        }
        echo 'finished';
    }
    
    public function actionListcollection()
    {
        $collections  = Collection::find()->all();
        $collectionFarms_id = [];
        $farmsid = [];
        foreach ($collections as $collection) {
            $model = Collection::findOne($collection['id']);
//            $farm = Farms::findOne($collection['farms_id']);
            $farmState = Farms::getContractstate($collection['farms_id']);
            switch ($farmState) {
                case 'L':
                    $model->farmstate = 3;
                    break;
                case 'W':
                    $model->farmstate = 2;
                    break;
                default:
                    $model->farmstate = 1;
            }
            $model->save();
        }
        echo 'finished';
    }

    public function actionLoanlist()
    {
        $loans = Loan::find()->all();
        foreach ($loans as $loan) {
            $farm = Farms::findOne($loan['farms_id']);
            if($farm->locked !== $loan['lock'] and $farm->locked == 1) {
                $result[] = $farm->id;
            }
        }
        var_dump($result);
    }

    public function actionAddarea()
    {
        $collections = Collection::find()->all();
//        var_dump($collections);exit;
        foreach ($collections as $collection) {
            $model = Collection::findOne($collection['id']);
            $farm = Farms::findOne($collection['farms_id']);
            $model->contractarea = $farm->contractarea;
            $model->save();
        }
        echo 'finished';
    }

    public function actionFires()
    {
        $fires = Fireprevention::find()->all();
        foreach ($fires as $fire) {
            $model = Fireprevention::findOne($fire['id']);
            $percent = Fireprevention::getPercent($model);
            $model->percent = $percent;
            if($percent >= 60) {
                $model->finished = 1;
            } else {
                $model->finished = 0;
            }
            $model->save();
        }
        echo 'finished';
    }

    public function actionFirestate()
    {
        $fires = Fireprevention::find()->all();
        foreach ($fires as $fire) {
            $model = Fireprevention::findOne($fire['id']);
            $state = Farms::getContractstate($model->farms_id);
            $stateArray = ['10'=>1,'W'=>2,'L'=>3,'M'=>4];
            $model->farmstate = $stateArray[$state];
            $model->save();
        }
        echo 'finished';
    }

    public function actionBreedstate()
    {
        $breeds = Breedinfo::find()->all();
        foreach ($breeds as $breed) {
            $model = Breedinfo::findOne($breed['id']);
            $farm = Farms::findOne($model->farms_id);
            $farm->isbreed = 1;
            $farm->save();
//            $state = Farms::getContractstate($model->farms_id);
//            $stateArray = ['10'=>1,'W'=>2,'L'=>3,'M'=>4];
//            $model->farmstate = $stateArray[$state];
//            $model->save();
        }
        echo 'finished';
    }

    public function actionInsurancestate()
    {
        $breeds = Insurance::find()->all();
        foreach ($breeds as $breed) {
            $model = Insurance::findOne($breed['id']);
//            $farm = Farms::findOne($model->farms_id);
//            $farm->isbreed = 1;
//            $farm->save();
            $state = Farms::getContractstate($model->farms_id);
            $stateArray = ['10'=>1,'W'=>2,'L'=>3,'M'=>4];
            $model->farmstate = $stateArray[$state];
            $model->save();
        }
        echo 'finished';
    }

    public function actionLoanstate()
    {
        $breeds = Loan::find()->all();
        foreach ($breeds as $breed) {
            $model = Loan::findOne($breed['id']);
//            $farm = Farms::findOne($model->farms_id);
//            $farm->isbreed = 1;
//            $farm->save();
            $state = Farms::getContractstate($model->farms_id);
            $stateArray = ['10'=>1,'W'=>2,'L'=>3,'M'=>4];
            $model->farmstate = $stateArray[$state];
            $model->save();
        }
        echo 'finished';
    }

    public function actionRev()
    {
        $t = Ttpozongdi::find()->andFilterWhere(['between','create_at',Theyear::getYeartime()[0],Theyear::getYeartime()[1]])->all();
//        var_dump(count($t));
        foreach ($t as $v) {
            $farm = Farms::findOne($v['oldfarms_id']);
            $farm->nowyearstate = -1;
            $farm->save();
        }
        echo '33';
    }

    public function actionNowto2009()
    {
        set_time_limit ( 0 );

        $loadxls = \PHPExcel_IOFactory::load('xlsuploads/parcel2009.xls');

        $rows = $loadxls->getActiveSheet ()->getHighestRow ();
        $r = 0;
        $data = [];
        $data09 = [];
        for($i = 3; $i <= $rows; $i ++) {
            $data09[] = ['farmname'=>$loadxls->getActiveSheet()->getCell('C' . $i)->getValue(),'farmername'=>$loadxls->getActiveSheet()->getCell('D' . $i)->getValue(),'contractarea'=>$loadxls->getActiveSheet()->getCell('J' . $i)->getValue()];
            $farm = Farms::find()->where(['farmername'=>$loadxls->getActiveSheet()->getCell('D' . $i)->getValue(),'contractarea'=>$loadxls->getActiveSheet()->getCell('J' . $i)->getValue()])->one();
//            if($farm['farmname'] == $loadxls->getActiveSheet()->getCell('C' . $i)->getValue() and $farm['farmername'] == $loadxls->getActiveSheet()->getCell('D' . $i)->getValue() and $farm['contractarea'] == $loadxls->getActiveSheet()->getCell('J' . $i)->getValue()) {
            if($farm) {
                $data[] = $farm;
            }

//                echo $loadxls->getActiveSheet()->getCell('C' . $i)->getValue() . '-' . $loadxls->getActiveSheet()->getCell('D' . $i)->getValue() . '-' . $loadxls->getActiveSheet()->getCell('J' . $i)->getValue() . '<br>';;
        }
        return $this->render('taskindex',[
            'data' => $data,
            'data09' => $data09,
        ]);
    }

    public function actionInsurance()
    {
        $str = '';
        $data = Insurance::find()->all();
        foreach ($data as $value) {
            $strArray = [];
            $model = Insurance::findOne($value['id']);
            if($model->insuredsoybean > 0) {
                $strArray[] = '6-' . $model->insuredsoybean;
            }
            if($model->insuredwheat > 0) {
                $strArray[] ='2-'.$model->insuredwheat;
            }
            if($model->other > 0) {
                $strArray[] = 'other-'.$model->other;
            }
            $model->insured = implode(',',$strArray);
            $model->save();
        }
        echo 'finished';
    }

    public function actionMachine()
    {
        $machines = Machineoffarm::find()->all();
        foreach ($machines as $machine) {
            $model = Machineoffarm::findOne($machine['id']);
            if(empty($model->cardid)) {
                $model->cardid = Farms::findOne($machine['farms_id'])->cardid;
                $model->save();
            }
        }
        echo 'finished';
    }

    public function actionMachinetofixed()
    {
        $machines = Machineoffarm::find()->all();
        foreach ($machines as $machine) {
            $model = new Fixed();
            $model->name = Machine::find()->where(['id'=>$machine['machine_id']])->one()['productname'];
            $model->typeid = 2;
            $model->unit = '台';
            $model->number = 1;
            $model->state = '正常使用';
            $model->cardid = $machine['cardid'];
            $model->save();
        }
        echo 'finsihed';
    }

    public function actionSales()
    {
        $data = Sales::find()->all();
        foreach ($data as $value) {
            $model = Sales::findOne($value['id']);
            $farm = Farms::findOne($value['farms_id']);
            $model->state = $farm->state;
            $model->save();
        }
        echo 'finished';
    }

    public function actionMachineapply()
    {
        $data = Machineapply::find()->all();
        foreach($data as $value) {
            $model = Machineapply::findOne($value['id']);
            $model->year = date('Y',$model->create_at);
            $model->save();
        }
        echo 'finished';
    }

    public function actionLeasetime()
    {
        $data = Insurance::find()->all();
        foreach($data as $value) {
//            if($value->renttime) {
//                if (strpos($value->renttime, '-') === false) {
//                    $model = Lease::findOne($value['id']);
//                    $model->renttime = date('Y-m-d',$model->renttime);
//                    $model->save();
//                }
//            }
            if(date('Y',$value['create_at']) == date('Y')) {
                if($value['year'] !== date('Y',$value['create_at'])) {
                    $model = Insurance::findOne($value['id']);
//                    var_dump($model);
                    $model->year = date('Y');
                    $model->save();
                }
            }
        }
        echo 'finished';
    }

    public function actionCreatefire()
    {
        $farms = Farms::find()->where(['state'=>[1,2,3,4,5]])->all();
        foreach ($farms as $farm) {
            $fire = Fireprevention::find()->where(['farms_id'=>$farm['id'],'year'=>User::getYear()])->one();
            if(empty($fire)) {
                Fireprevention::newFire($farm['id']);
            }
        }
        echo 'finished';
    }
    
    public function actionIndexecharts()
    {
        set_time_limit(0);
        $users = User::find()->all();
        foreach ($users as $user) {
            $cache = new Indexecharts();
            $cache->setCache($user['id']);
        }
        echo '11111';
    }

    public function actionFirefinished()
    {
        $fires = Fireprevention::find()->all();
        foreach ($fires as $fire) {
            $model = Fireprevention::findOne($fire['id']);
            $percent = Fireprevention::getPercent($model);
            if($percent >= 60) {
                $model->finished = 1;
            }
            if($percent > 0 and $percent < 60) {
                $model->finished = 2;
            }
            if($percent == 0 or empty($percent)) {
                $model->finished = 0;
            }
            $model->save();
        }
        echo 'finished';
    }

    public function actionFirepercent()
    {
        $fires = Fireprevention::find()->all();
        foreach ($fires as $fire) {
            $model = Fireprevention::findOne($fire['id']);
            $percent = Fireprevention::getPercent($model);
            $model->percent = $percent;
            $model->save();
        }
        echo 'finished22';
    }

    public function actionPlan()
    {
        $farms = Farms::find()->where(['state'=>[1,2,3,4,5]])->all();
        foreach ($farms as $farm) {
            $plant = Plantingstructure::find()->where(['farms_id'=>$farm['id'],'year'=>User::getYear()])->one();
            $plan = Plantingstructureyearfarmsidplan::find()->where(['farms_id'=>$farm['id'],'year'=>User::getYear()])->one();
            if(empty($plan)) {
                $model = new Plantingstructureyearfarmsidplan();
                $model->farms_id = $farm['id'];
                $model->cardid = $farm['cardid'];
                $model->telephone = $farm['telephone'];
                $model->state = $farm['state'];
                $model->contractarea = $farm['contractarea'];
                $model->contractnumber = $farm['contractnumber'];
                if ($plant) {
                    $model->isfinished = 1;
                } else {
                    $model->isfinished = 0;
                }
                $model->year = User::getYear();
                $model->create_at = $plant['create_at'];
                $model->farmname = $farm['farmname'];
                $model->farmername = $farm['farmername'];
                $model->management_area = $farm['management_area'];
                $model->pinyin = $farm['pinyin'];
                $model->farmerpinyin = $farm['farmerpinyin'];
                $model->save();
            } else {
                $model = Plantingstructureyearfarmsidplan::findOne($plan['id']);
                if ($plant) {
                    $model->isfinished = 1;
                } else {
                    $model->isfinished = 0;
                }
                $model->save();
            }
        }
        echo 'finsihed';
    }
}
