<?php
namespace frontend\helpers;
use app\models\Tables;
use app\models\Farms;
/**
 * Created by PhpStorm.
 * User: liujiaming
 * Date: 2018/4/2
 * Time: 19:08
 */
class ES extends Echarts
{
    //柱形图,options=['title'=>'测试','tooltip'=>[],'legend'=>['销量'],'xAxis'=>["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"],'yAxis'=>[],'series'=>[5, 20, 36, 10, 10, 20]]
    public static function bar(Array $array = NULL)
    {
        $obj = new Echarts();
        $obj->type = 'bar';
        return $obj;
    }
    //温度计图,options=['title'=>'测试','legend'=>['总数','已销售'],'xAxis'=>["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"],'unit'=>'元','yAxis'=>[],'series'=>[[60,60,60,60,60,60],[5, 20, 36, 10, 10, 20]]]
    public static function wdj()
    {
        $obj = new Echarts();
        $obj->type = 'wdj';
        return $obj;
    }
    //饼形图,options=['title'=>'某站点用户访问来源','legend'=>['直接访问','邮件营销','联盟广告','视频广告','搜索引擎'],'series'=>[['value'=>335, 'name'=>'直接访问'], ['value'=>310, 'name'=>'邮件营销'], ['value'=>234, 'name'=>'联盟广告'], ['value'=>135, 'name'=>'视频广告'], ['value'=>1548, 'name'=>'搜索引擎']]]
    public static function pie()
    {
        $obj = new Echarts();
        $obj->type = 'pie';
        return $obj;
    }
    //饼形图(左右两个),options=['title'=>'南丁格尔玫瑰图','subtext'=>'虚构','legend'=>['rose1','rose2','rose3','rose4','rose5','rose6','rose7','rose8'],'series'=>['name'=>['半径模式','面积模式'],'data'=>[$sdata1,$sdata2]]]
    public static function pie2()
    {
        $obj = new Echarts();
        $obj->type = 'pie2';
        return $obj;
    }
    //柱形图表(组),options=['dataset'=>[['product(项目)','2015','2016','2017'],['aaa(名称)',123,231,234(数据)],['bbb',24,254,234],['ccc',423,54,321]]]
    public static function barGroup()
    {
        $obj = new Echarts();
        $obj->type = 'barGroup';
        return $obj;
    }
    //柱形图表(组)--柱形条显示名称options=['color'=>['#003366', '#006699', '#4cabce', '#e5323e'],'legend'=>['投入品1','投入品2','投入品3','农药1'],'xAxis'=>['小麦','玉米','大豆','杂豆','马铃薯'],'series'=>[[12,53,24,54,43],[65,34,26,34,23],[64,34,24,34,43],[36,54,26,76,14],[54,24,54,23,54]]]
    public static function barLabel()
    {
        $obj = new Echarts();
        $obj->type = 'barLabel';
        return $obj;
    }

//    public static function typename($array)
//    {
//        $result = [];
//        $types = [];
//        $classname = 'app\\models\\'.$array['class'];
//        $data = $classname::find()->where($array['where'])->all();
//        $class2 = explode('_',$array['field']);
//        $classname2 = 'app\\models\\'.ucfirst($class2[0]);
//        foreach ($data as $key => $value) {
//            $types[] = ['id'=>$value[$array['field']]];
//        }
////        var_dump($types);exit;
//        if($types) {
//            $newdata = Farms::unique_arr($types);
//            foreach ($newdata as $value) {
//                $result[$value['id']] = $classname2::find()->where(['id' => $value['id']])->one()['typename'];
//            }
//        }
//        return $result;
//    }
//
//    public static function getTypelist($array)
//    {
//        $types = self::typename($array);
//        sort($types);
//        return $types;
//    }
//
//    public static function getData($array)
//    {
//        $result = [];
//        $types = self::typename($array);
//        $classname = 'app\\models\\'.$array['class'];
//        foreach ($types as $key => $value) {
//            $result[] = sprintf('%.2f',$data = $classname::find()->where($array['where'])->andFilterWhere([$array['field']=>$key])->sum($array['sum']));
//        }
//        return $result;
//    }
}