<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/06/04
 * Time: 17:02:54
 */
namespace app\modules\common\helpers;
use Yii;
use app\modules\common\helpers\Constants;
use app\modules\lottery\helpers\Constant;
//use app\modules\components\PHPExcel\PHPExcel;

require \Yii::$app->basePath . "/modules/components/excel/PHPExcel.php";

class Excel{
    /**
     * 打印代理商数字彩订单
     * @param $row
     * @param $count
     */
    public static function printSzcOrder($row,$count){
        $fileName = date("YmdHis");
        $orderStatus = Constant::ORDER_STATUS;
        $dealStatus = Constants::DEAL_STATUS;
        $obj = new \PHPExcel();
        $objSheet = $obj->getActiveSheet();
        $objSheet->setTitle("订单报表");
        $objSheet->setCellValue("A1","订单号")
            ->setCellValue("B1","投注时间")
            ->setCellValue("C1","截止时间")
            ->setCellValue("D1","彩种")
            ->setCellValue("E1","倍数")
            ->setCellValue("F1","期号")
            ->setCellValue("G1","投注金额")
            ->setCellValue("H1","订单状态")
            ->setCellValue("I1","中奖金额")
            ->setCellValue("J1","处理状态")
            ->setCellValue("K1","实兑金额")
            ->setCellValue("L1","会员编号")
            ->setCellValue("M1","会员手机")
            ->setCellValue("N1","子代理名称");
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        $objSheet->getColumnDimension('E')->setAutoSize(true);
        $objSheet->getColumnDimension('F')->setAutoSize(true);
        $objSheet->getColumnDimension('G')->setAutoSize(true);
        $objSheet->getColumnDimension('H')->setAutoSize(true);
        $objSheet->getColumnDimension('I')->setAutoSize(true);
        $objSheet->getColumnDimension('J')->setAutoSize(true);
        $objSheet->getColumnDimension('K')->setAutoSize(true);
        $objSheet->getColumnDimension('L')->setAutoSize(true);
        $objSheet->getColumnDimension('M')->setAutoSize(true);
        $objSheet->getColumnDimension('N')->setAutoSize(true);
        for ($i = 2; $i <=$count+1; $i++) {
            $objSheet->setCellValue("A".$i,$row[$i-2]["lottery_order_code"]);
            $objSheet->setCellValue("B".$i,$row[$i-2]["create_time"]);
            $objSheet->setCellValue("C".$i,$row[$i-2]["end_time"]);
            $objSheet->setCellValue("D".$i,$row[$i-2]["lottery_name"]);
            $objSheet->setCellValue("E".$i,$row[$i-2]["bet_double"]);
            $objSheet->setCellValue("F".$i,$row[$i-2]["periods"]);
            $objSheet->setCellValue("G".$i,$row[$i-2]["bet_money"]);
            $objSheet->setCellValue("H".$i,$orderStatus[$row[$i-2]["status"]]);
            $objSheet->setCellValue("I".$i,$row[$i-2]["win_amount"]);
            $objSheet->setCellValue("J".$i,$dealStatus[$row[$i-2]["deal_status"]]);
            $objSheet->setCellValue("K".$i,$row[$i-2]["award_amount"]);
            $objSheet->setCellValue("L".$i,$row[$i-2]["cust_no"]);
            $objSheet->setCellValue("M".$i,$row[$i-2]["user_tel"]);
            $objSheet->setCellValue("N".$i,$row[$i-2]["user_remark"]);
        }
        ob_end_clean();;//关键
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=UTF-8");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename='.$fileName.'.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        //保存报表
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');
        return true;
    }
    /**
     * 打印代理商竞彩订单
     * @param $row
     * @param $count
     */
    public static function printJcOrder($row,$count){
        $fileName = date("YmdHis");
        $orderStatus = Constants::ORDER_STATUS;
        $dealStatus = Constants::DEAL_STATUS;
        $obj = new \PHPExcel();
        $objSheet = $obj->getActiveSheet();
        $objSheet->setTitle("订单报表");
        $objSheet->setCellValue("A1","订单号")
            ->setCellValue("B1","投注时间")
            ->setCellValue("C1","截止时间")
            ->setCellValue("D1","过关方式")
            ->setCellValue("E1","彩种玩法")
            ->setCellValue("F1","倍数")
            ->setCellValue("G1","投注金额")
            ->setCellValue("H1","订单状态")
            ->setCellValue("I1","中奖金额")
            ->setCellValue("J1","处理状态")
            ->setCellValue("K1","实兑金额")
            ->setCellValue("L1","会员编号")
            ->setCellValue("M1","会员手机")
            ->setCellValue("N1","子代理名称");
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        $objSheet->getColumnDimension('E')->setAutoSize(true);
        $objSheet->getColumnDimension('F')->setAutoSize(true);
        $objSheet->getColumnDimension('G')->setAutoSize(true);
        $objSheet->getColumnDimension('H')->setAutoSize(true);
        $objSheet->getColumnDimension('I')->setAutoSize(true);
        $objSheet->getColumnDimension('J')->setAutoSize(true);
        $objSheet->getColumnDimension('K')->setAutoSize(true);
        $objSheet->getColumnDimension('L')->setAutoSize(true);
        $objSheet->getColumnDimension('M')->setAutoSize(true);
        $objSheet->getColumnDimension('N')->setAutoSize(true);
        for ($i = 2; $i <=$count+1; $i++) {
            $objSheet->setCellValue("A".$i,$row[$i-2]["lottery_order_code"]);
            $objSheet->setCellValue("B".$i,$row[$i-2]["create_time"]);
            $objSheet->setCellValue("C".$i,$row[$i-2]["end_time"]);
            $objSheet->setCellValue("D".$i,$row[$i-2]["play_name"]);
            $objSheet->setCellValue("E".$i,$row[$i-2]["lottery_name"]);
            $objSheet->setCellValue("F".$i,$row[$i-2]["bet_double"]);
            $objSheet->setCellValue("G".$i,$row[$i-2]["bet_money"]);
            $objSheet->setCellValue("H".$i,$orderStatus[$row[$i-2]["status"]]);
            $objSheet->setCellValue("I".$i,$row[$i-2]["win_amount"]);
            $objSheet->setCellValue("J".$i,$dealStatus[$row[$i-2]["deal_status"]]);
            $objSheet->setCellValue("K".$i,$row[$i-2]["award_amount"]);
            $objSheet->setCellValue("L".$i,$row[$i-2]["cust_no"]);
            $objSheet->setCellValue("M".$i,$row[$i-2]["user_tel"]);
            $objSheet->setCellValue("N".$i,$row[$i-2]["user_remark"]);
        }
        ob_end_clean();  //清空缓存
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename='.$fileName.'.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        //保存报表
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');
        return true;
    }
    /**
     * 打印推广人员分润明细记录
     * @param $row
     * @param $count
     */
    public static function printSpreadDetail($row,$count){
        $fileName = date("YmdHis")."分润明细记录";
        $obj = new \PHPExcel();
        $objSheet = $obj->getActiveSheet();
        $objSheet->setTitle("分润明细报表");
        $objSheet->setCellValue("A1","用户编号")
            ->setCellValue("B1","用户名称")
            ->setCellValue("C1","购彩量")
            ->setCellValue("D1","提成");
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        for ($i = 2; $i <=$count+1; $i++) {
            $objSheet->setCellValue("A".$i,$row[$i-2]["from_cust_no"]);
            $objSheet->setCellValue("B".$i,$row[$i-2]["user_name"]);
            $objSheet->setCellValue("C".$i,$row[$i-2]["total_amount"]);
            $objSheet->setCellValue("D".$i,$row[$i-2]["amount"]);
        }
        ob_end_clean();  //清空缓存
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename='.$fileName.'.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        //保存报表
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');
        return true;
    }

    /**
     * 打印渠道商户竞彩订单
     * @param $row
     * @param $count
     */
    public static function printChannelJcOrder($row,$count){
        $fileName = date("YmdHis");
        $orderStatus = Constants::ORDER_STATUS;
        $dealStatus = Constants::DEAL_STATUS;
        $obj = new \PHPExcel();
        $objSheet = $obj->getActiveSheet();
        $objSheet->setTitle("订单报表");
        $objSheet->setCellValue("A1","接单编号")
            ->setCellValue("B1","商户订单号")
            ->setCellValue("C1","订单编号")
            ->setCellValue("D1","投注时间")
            ->setCellValue("E1","过关方式")
            ->setCellValue("F1","彩种玩法")
            ->setCellValue("G1","倍数")
            ->setCellValue("H1","投注金额")
            ->setCellValue("I1","订单状态")
            ->setCellValue("J1","中奖金额")
            ->setCellValue("K1","实兑金额")
            ->setCellValue("L1","处理状态");
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        $objSheet->getColumnDimension('E')->setAutoSize(true);
        $objSheet->getColumnDimension('F')->setAutoSize(true);
        $objSheet->getColumnDimension('G')->setAutoSize(true);
        $objSheet->getColumnDimension('H')->setAutoSize(true);
        $objSheet->getColumnDimension('I')->setAutoSize(true);
        $objSheet->getColumnDimension('J')->setAutoSize(true);
        $objSheet->getColumnDimension('K')->setAutoSize(true);
        $objSheet->getColumnDimension('L')->setAutoSize(true);
        for ($i = 2; $i <=$count+1; $i++) {
            $objSheet->setCellValue("A".$i,$row[$i-2]["api_order_code"]);
            $objSheet->setCellValue("B".$i,$row[$i-2]["third_order_code"]);
            $objSheet->setCellValue("C".$i,$row[$i-2]["lottery_order_code"]);
            $objSheet->setCellValue("D".$i,$row[$i-2]["create_time"]);
            $objSheet->setCellValue("E".$i,$row[$i-2]["play_name"]);
            $objSheet->setCellValue("F".$i,$row[$i-2]["lottery_name"]);
            $objSheet->setCellValue("G".$i,$row[$i-2]["bet_double"]);
            $objSheet->setCellValue("H".$i,$row[$i-2]["bet_money"]);
            $objSheet->setCellValue("I".$i,$orderStatus[$row[$i-2]["status"]]);
            $objSheet->setCellValue("J".$i,$row[$i-2]["win_amount"]);
            $objSheet->setCellValue("K".$i,$row[$i-2]["award_amount"]);
            $objSheet->setCellValue("L".$i,$dealStatus[$row[$i-2]["deal_status"]]);
        }
        ob_end_clean();  //清空缓存
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename='.$fileName.'.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        //保存报表
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');
        return true;
    }
    /**
     * 打印推广人员每天每个用户记录
     * @param $row
     * @param $count
     */
    public static function printSpreadDayDetail($row,$count){
        $fileName = date("YmdHis")."分润明细记录";
        $obj = new \PHPExcel();
        $objSheet = $obj->getActiveSheet();
        $objSheet->setTitle("分润明细报表");
        $objSheet->setCellValue("A1","购彩日期")
            ->setCellValue("B1","用户编号")
            ->setCellValue("C1","手机号")
            ->setCellValue("D1","用户名称")
            ->setCellValue("E1","购彩量")
            ->setCellValue("F1","订单数");
//            ->setCellValue("E1","提成");
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        $objSheet->getColumnDimension('E')->setAutoSize(true);
        $objSheet->getColumnDimension('F')->setAutoSize(true);
        for ($i = 2; $i <=$count+1; $i++) {
            $objSheet->setCellValue("A".$i,$row[$i-2]["create_date"]);
            $objSheet->setCellValue("B".$i,$row[$i-2]["from_cust_no"]);
            $objSheet->setCellValue("C".$i,$row[$i-2]["user_tel"]);
            $objSheet->setCellValue("D".$i,$row[$i-2]["user_name"]);
            $objSheet->setCellValue("E".$i,$row[$i-2]["total_amount"]);
            $objSheet->setCellValue("F".$i,$row[$i-2]["OrderCount"]);
//            $objSheet->setCellValue("E".$i,$row[$i-2]["amount"]);
        }
        ob_end_clean();  //清空缓存
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename='.$fileName.'.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        //保存报表
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');
        return true;
    }

}