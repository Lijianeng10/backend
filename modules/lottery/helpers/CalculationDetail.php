<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\lottery\helpers;

use app\modules\models\LotteryRecord;
use app\modules\models\LotteryTime;
use app\modules\helpers\Commonfun;
use app\modules\helpers\Constants;
use yii\db\Query;
    

class CalculationDetail {
     /**
     * 生成组合数组
     * @param array $arr
     * @param integer $num
     * @return array
     */
    public static function getCombination_array($arr, $num) {
        $ret = self::getArrangement_array($arr, $num);
        $result = [];
        if (is_array($ret) && count($ret) > 0) {
            foreach ($ret as $key => $val) {
                sort($val);
                if (!in_array($val, $result)) {
                    $result[] = $val;
                }
            }
            return $result;
        } else {
            return $ret;
        }
    }
    
     /**
     * 生成排列的数组
     * @param type $arr
     * @param type $num
     * @return array
     */
    public static function getArrangement_array($arr, $num) {
        $result = [];
        $v = [];
        if ($num < 1) {
            return "数据错误！";
        }
        if ($num == 1) {
            if (is_array($arr) && count($arr) > 0) {
                foreach ($arr as $key => $value) {
                    $v = [];
                    $v[] = $value;
                    $result[] = $v;
                }
                return $result;
            } else {
                return "数据错误！";
            }
        } else {
            if (is_array($arr) && count($arr) > 0) {
                $num = $num - 1;
                foreach ($arr as $key => $value) {
                    $arr1 = $arr;
                    unset($arr1[$key]);
                    $ret = self::getArrangement_array($arr1, $num);
                    if (!is_array($ret)) {
                        return $ret;
                    }
                    foreach ($ret as $k => $v) {
                        $v[] = $value;
                        $result[] = $v;
                    }
                }
                return $result;
            } else {
                return "数据错误！";
            }
        }
    }
    /**
     * 生成交叉组合数组
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function cross_array($arr1, $arr2) {
        $result = [];
        $num = 0;
        if (is_array($arr1) && is_array($arr2) && count($arr1) > 0 && count($arr2) > 0) {
            foreach ($arr1 as $val1) {
                foreach ($arr2 as $val2) {
                    $result[$num][0] = $val1;
                    $result[$num][1] = $val2;
                    $num++;
                }
            }
            return $result;
        } else {
            return "数据错误！";
        }
    }

    /**
     * 生成交叉组合字符串，必须含有三个参数或者以上，第一个参数为拼接用的字符串
     * @param string $str
     * @param array $arr1
     * @param array $arr2
     * @return string
     */
    public static function proCross_string() {
        $args = func_get_args();
        $count = func_num_args();
        if (!is_string($args[0])) {
            return "数据错误！";
        }
        if ($count >= 3) {
            $result = $args[1];
            for ($n = 2; $n < $count; $n++) {
                if (!is_array($args[$n]) || count($args[$n]) < 1) {
                    return "数据错误！";
                }
                $result = self::cross_array($result, $args[$n]);
                foreach ($result as &$val) {
                    $val = implode($args[0], $val);
                }
                if (!is_array($result)) {
                    return "数据错误！";
                }
            }
        } else {
            return "数据错误！";
        }
        return $result;
    }
    
    /**
     * 
     * @param type $nums
     * @return array
     */
    public static function noteNums($nums) {
        return explode('|', $nums);
    }
    
    public static function ssqNote_1001($arr) {

        if (!is_array($arr)) {
            return "数据错误！";
        }
        $redBalls = explode(',', $arr[0]);
        $blueBalls = explode(',', $arr[1]);
        $redArrs =  self::getCombination_array($redBalls, 6);
        $blueArrs = self::getCombination_array($blueBalls, 1);
        
        foreach ($redArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        
        foreach ($blueArrs as $key => &$val) {
            $val = implode(",", $val);
        }

        $nums = self::cross_array($redArrs, $blueArrs);
        foreach ($nums as $key => &$val) {
            $val = implode("|", $val);
        }
        return $nums;
    }

    public static function tdNote_100201($arr){
        $orders = [];
        $orders[] = implode(",", $arr);
        return $orders;
    }
    
    public static function tdNote_100211($arr) {
        $hand = explode(',', $arr[0]);
        $ten = explode(',', $arr[1]);
        $bit = explode(',', $arr[2]);
        
        $baiArrs = self::getCombination_array($hand, 1);
        $shiArrs =  self::getCombination_array($ten, 1);
        $geArrs = self::getCombination_array($bit, 1);
        foreach ($baiArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($shiArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($geArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        $orders = self::cross_array($baiArrs, $ten);
        foreach ($orders as $key => &$val) {
            $val = implode(",", $val);
        }

        $orders = self::cross_array($orders, $geArrs);
        foreach ($orders as $key => &$val) {
            $val = implode(",", $val);
        }
        return $orders;
    }
    
    public static function tdNote_100202($arr) {
        $nums = explode(',', $arr[0]);
        $arrs = self::getCombination_array($nums, 2);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $v1 = $val;
            $v1[] = $val[0];
            $v2 = $val;
            $v2[] = $val[1];
            $orders[] = $v1;
            $orders[] = $v2;
        }
        foreach ($orders as $key => &$val) {
            $val = implode(",", $val);
        }
        return $orders;
    }
    
    public static function tdNote_100212($arr) {
        $nums = explode(',', $arr[0]);
        $arrs = self::getCombination_array($nums, 2);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $v1 = $val;
            $v1[] = $val[0];
            $v2 = $val;
            $v2[] = $val[1];
            $orders[] = $v1;
            $orders[] = $v2;
        }
        foreach ($orders as $key => &$val) {
            $val = implode(",", $val);
        }
        return $orders;
    }
    
    public static function tdNote_100203($arr) {
        $arrs = explode(',', $arr[0]);
        $arrs = self::getCombination_array($arrs, 3);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $orders[] = implode(",", $val);
        }
        return $orders;
    }
    
    public static function tdNote_100213($arr) {
        $arrs = explode(',', $arr[0]);
        $arrs = self::getCombination_array($arrs, 3);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $orders[] = implode(",", $val);
        }
        return $orders;
    }
    
    public static function qlcNote_1003($arr) {
        if (!is_array($arr)) {
            return "数据错误！";
        }
        $numsArr = explode(',', $arr[0]);
        $qlcArrs = self::getCombination_array($numsArr, 7);
        
        foreach ($qlcArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        return $qlcArrs;
    }
    
    /**
     * 大乐透直选单式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function dltNote_200101($areas) {
        $redBalls = explode(',', $areas[0]);
        $blueBalls = explode(',', $areas[1]);
        if (!is_array($redBalls) || !is_array($blueBalls) || count($redBalls) < 5 || count($blueBalls) < 2) {
            return false;
        }
        $redArrs = self::getCombination_array($redBalls, 5);
        $blueArrs = self::getCombination_array($blueBalls, 2);
        foreach ($redArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($blueArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        $orders = self::cross_array($redArrs, $blueArrs);
        foreach ($orders as $key => &$val) {
            $val = implode("|", $val);
        }
        return $orders;
    }

    /**
     * 大乐透直选复式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function dltNote_200102($areas) {
        $redBalls = explode(',', $areas[0]);
        $blueBalls = explode(',', $areas[1]);
        if (!is_array($redBalls) || !is_array($blueBalls) || count($redBalls) < 5 || count($blueBalls) < 2) {
            return false;
        }
        $redArrs = self::getCombination_array($redBalls, 5);
        $blueArrs = self::getCombination_array($blueBalls, 2);
        foreach ($redArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($blueArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        $orders = self::cross_array($redArrs, $blueArrs);
        foreach ($orders as $key => &$val) {
            $val = implode("|", $val);
        }
        return $orders;
    }

    /**
     * 排列三直选单式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function plsNote_200201($areas) {
        $orders = [];
        if (is_array($areas) && count($areas) == 3) {
            $orders[] = implode(",", $areas);
            return $orders;
        }
        return false;
    }

    /**
     * 排列三直选复式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function plsNote_200211($areas) {
        $baiBalls = explode(',', $areas[0]);
        $shiBalls = explode(',', $areas[1]);
        $geBalls = explode(',', $areas[2]);
        if (!is_array($baiBalls) || !is_array($shiBalls) || !is_array($geBalls)) {
            return false;
        }
        $baiArrs = self::getCombination_array($baiBalls, 1);
        $shiArrs = self::getCombination_array($shiBalls, 1);
        $geArrs = self::getCombination_array($geBalls, 1);
        foreach ($baiArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($shiArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        foreach ($geArrs as $key => &$val) {
            $val = implode(",", $val);
        }
        $orders = self::proCross_string(",", $baiArrs, $shiArrs, $geArrs);
        return $orders;
    }

    /**
     * 排列三组三单式生成单一投注
     * @param array $nums
     * @return array
     */
    public static function plsNote_200202($nums) {
        $balls = explode(',', $nums[0]);
        if (count($balls) != 3) {
            return false;
        }
        $orders = [$nums[0]];
        return $orders;
    }

    /**
     * 排列三组三复式生成单一投注
     * @param array $nums
     * @return array
     */
    public static function plsNote_200212($nums) {
        $balls = explode(',', $nums[0]);
        if (!is_array($balls) || count($balls) < 2) {
            return false;
        }
        $arrs = self::getCombination_array($balls, 2);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $v1 = $val;
            $v1[] = $val[0];
            $v2 = $val;
            $v2[] = $val[1];
            $orders[] = $v1;
            $orders[] = $v2;
        }
        foreach ($orders as $key => &$val) {
            $val = implode(",", $val);
        }
        return $orders;
    }

    /**
     * 排列三组六单式生成单一投注
     * @param array $nums
     * @return array
     */
    public static function plsNote_200203($nums) {
        $balls = explode(',', $nums[0]);
        if (count($balls) != 3) {
            return false;
        }
        $orders = [$nums[0]];
        return $orders;
    }

    /**
     * 排列三组六复式生成单一投注
     * @param array $nums
     * @return array
     */
    public static function plsNote_200213($nums) {
        $balls = explode(',', $nums[0]);
        if (!is_array($balls) || count($balls) < 2) {
            return false;
        }
        $arrs = self::getCombination_array($balls, 3);
        $orders = [];
        foreach ($arrs as $key => $val) {
            $orders[] = implode(",", $val);
        }
        return $orders;
    }

    /**
     * 排列五直选单式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function plfNote_200301($areas) {
        $orders = [];
        if (is_array($areas) && count($areas) == 5) {
            $orders[] = implode(",", $areas);
            return $orders;
        }
        return false;
    }

    /**
     * 排列五直选复式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function plfNote_200302($areas) {
        $wanBalls = explode(',', $areas[0]);
        $qianBalls = explode(',', $areas[1]);
        $baiBalls = explode(',', $areas[2]);
        $shiBalls = explode(',', $areas[3]);
        $geBalls = explode(',', $areas[4]);
        if (!is_array($wanBalls) || !is_array($qianBalls) || !is_array($baiBalls) || !is_array($shiBalls) || !is_array($geBalls)) {
            return false;
        }

        $orders = self::proCross_string(",", $wanBalls, $qianBalls, $baiBalls, $shiBalls, $geBalls);
        return $orders;
    }

    /**
     * 七星彩直选单式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function qxcNote_200401($areas) {
        $orders = [];
        if (is_array($areas) && count($areas) == 7) {
            $orders[] = implode(",", $areas);
            return $orders;
        }
        return false;
    }

    /**
     * 七星彩直选复式生成单一投注
     * @param array $areas
     * @return array
     */
    public static function qxcNote_200402($areas) {
        $bwBalls = explode(',', $areas[0]);
        $swBalls = explode(',', $areas[1]);
        $wanBalls = explode(',', $areas[2]);
        $qianBalls = explode(',', $areas[3]);
        $baiBalls = explode(',', $areas[4]);
        $shiBalls = explode(',', $areas[5]);
        $geBalls = explode(',', $areas[6]);
        if (!is_array($bwBalls) || !is_array($swBalls) || !is_array($wanBalls) || !is_array($qianBalls) || !is_array($baiBalls) || !is_array($shiBalls) || !is_array($geBalls)) {
            return false;
        }
        $orders = self::proCross_string(",", $bwBalls, $swBalls, $wanBalls, $qianBalls, $baiBalls, $shiBalls, $geBalls);
        return $orders;
    }
}
