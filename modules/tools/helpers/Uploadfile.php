<?php

/*
 * 文件上传工具类
 */

namespace app\modules\tools\helpers;

require \Yii::$app->basePath.'/vendor/qiniu/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 说明 ：文件上传类
 * @author  kevi
 * @date 2017年7月6日 下午1:41:34
 */
class Uploadfile {

    /**
     * 说明: 
     * @author  kevi
     * @date 2017年7月6日 下午1:41:26
     * @param $pic 上传的文件
     * @param $key 文件保存位置与文件名
     * @return 
     */
    public static function qiniu_upload($pic,$key){
        if(!empty($pic)){
            $accessKey = \Yii::$app->params['qiniu_accessKey'];
            $secretKey = \Yii::$app->params['qiniu_secretKey'];
            //要上传的空间
            $bucket = \Yii::$app->params['qiniu_bucket'];
            //鉴权对象
            $auth = new Auth($accessKey,$secretKey);
            $uploadToken = $auth->uploadToken($bucket);
    
            $picture = \Yii::$app->params['qiniu_link_host'] . $key;
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($uploadToken, $key, $pic);
            if ($err !== null) {
                return $err->getResponse()->error;
            }else{
                return  $picture;
            }
        }else{
            return 441;
        }
    }
    
    /**
     * 说明: 上传文件至图片服务器
     * @author  kevi
     * @date 2017年7月6日 下午1:35:51
     * @param $file
     * @return $saveDir
     */
    public static function pic_host_upload($file,$saveDir,$name=''){
        header('content-type:text/html;charset=utf8');
        $ch = curl_init();
        $url = \Yii::$app->params["lottery_img_host"];
        $value = new \CURLFile($file['tmp_name']);
        $data = [
            'name' =>rand(0,20) . '.jpg',
            'save_dir' => $saveDir,
            'img'=>$value,
        ];
        if($name){
            $data['name'] = $name;
        }
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
