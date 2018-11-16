<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\helpers;



class UploadForm  {
    
    /**
     * 图片上传的存储
     * auther 咕啦 zyl
     * create_time 2017-06-06
     * @param array $file
     * @return boolean|string
     */
    public static function getUpload($file) {
       $typeArr = array('gif', 'jpg', 'jpeg', 'png');
        $result = [];
        if($file['name']){
            $name = $file['name'];
            $type = strtolower(substr($name,strrpos($name,'.')+1));
            if(!in_array($type, $typeArr)) {
                $result = ['code'=>440, 'msg' => '文件格式不正确'];
                return $result;
            }
            $result = ['code'=>600];
            return $result;
        }else{
            $result = ['code' => 441, 'msg' => '上传文件未找到'];
            return $result;
        }
    }
    
    public static function getPath($pathJson){
        $result = [];
        $pathJson = json_decode($pathJson,true);
        if($pathJson->code != 600){
            $result = ['code' => $pathJson->code, 'msg'=>$pathJson->msg];
            return $result;
        }
        $pathRet = $pathJson->result;
        $path = $pathRet->ret_path;
        $result = ['code' => 600, 'msg' => $pathJson->msg, 'data'=>$path];
        return $result;
    }
}

