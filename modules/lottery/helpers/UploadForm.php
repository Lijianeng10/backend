<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\lottery\helpers;



class UploadForm  {
    
    /**
     * 图片上传的存储
     * auther 咕啦 zyl
     * create_time 2017-06-06
     * @param array $file
     * @return boolean|string
     */
    public static function getUpload($file) {
        $upload = "/lottery_images/";
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] ;
        $path = '';
        $typeArr = array('gif', 'jpg', 'jpeg', 'png');
        if(!file_exists($uploadPath . $upload)){
            mkdir("$upload_path",0700);
        }
        
        if($file['name']){
            $name = $file['name'];
            $type = strtolower(substr($name,strrpos($name,'.')+1)); 
            if(!in_array($type, $typeArr)) {
                return false;
            }
            $path = $upload . date('ymdhis') . '_' . $file['name'];
            $flag = 1;
        }
        if($flag){
            if(! move_uploaded_file($file['tmp_name'],($uploadPath . $path))){
                return false;
            };
        }
        return $path;
    }
}

