<?php

namespace app\modules\tools\helpers;

use app\modules\tools\helpers\Des;
use app\modules\common\services\KafkaService;
use app\modules\common\models\ZmfOrder;
use app\modules\common\models\AutoOutOrder;

class Zmf {

//    private $venderId = '18020601'; //销售商代码
//    private $url = 'http://120.77.204.131:8098/'; //智魔方地址
//    private $key = '56B06065B5237AF34DBBCBF8'; //密钥
    private $iv = '12345678'; //IV 向量
    private $version = '1500'; //版本号

    public function getHead($command) {
        $venderId = \Yii::$app->params['zmf_venderId'];
        $messageId = $venderId . $command . date('YmdHis') . rand(0, 100);
        $head = ['head' => ['version' => $this->version, 'command' => $command, 'venderId' => $venderId, 'messageId' => $messageId]];
        return $head;
    }

    /**
     * 说明: 1001 查询期信息接口
     * @author  kevi
     * @date 2018年1月5日 下午4:41:04
     * @param
     * @return
     */
//    public function to1019($data) {
//        $dataXml = $this->formatxml($data);
//        $key = \Yii::$app->params['zmf_key'];
//        $desModel = new Des($key, $this->iv);
//        $desData = $desModel->encrypt($dataXml); //des3加密
//        ob_clean();
////        print_r($desData);die;
//        //post消息体封装
//        $venderId = \Yii::$app->params['zmf_venderId'];
//        $head = ['head' => ['version' => 1500, 'command' => 1019, 'venderId' => $venderId, 'messageId' => $data['messageId']]];
//        $head['head']['md'] = md5($desData);
//        $head['body'] = $desData;
//        $head = $this->formatxml($head, 'message');
//        //提交请求
//        $postRet = $this->xmlpost($head);
//        $ret = $this->xmlToArray($postRet);
//        $ticketId = '';
//
//        if ($ret['head']['result'] == 0) {
//            $xmlret = $desModel->decrypt($ret['body']);
//            $ret = $this->xmlToArray($xmlret);
//            $ticketId = $ret['records']['record']['ticketId'];
//            $status = 4;
//        } else {
//            $status = 5;
//        }
//
//        //写入日志
//       $zmfOrder = ZmfOrder::find()->where(['messageId' => $data['messageId']])->andWhere('status !=1')->one();
//        if (!empty($zmfOrder)) {
//            $zmfOrder->ret_async_data = json_encode($ret);
//            $zmfOrder->status = 2;
//            $zmfOrder->modify_time = date('Y-m-d H:i:s');
//           if (!$zmfOrder->save()) {
//                $errorMsg = $zmfOrder->errors;
//               \Yii::jsonError(400, $errorMsg);
//           }
//           $autoOutOrder = AutoOutOrder::findOne(['out_order_code' => $zmfOrder->order_code, 'status' => 2]);
//           if (empty($autoOutOrder)) {
//               return \Yii::jsonError(400, '订单不存在');
//           }
//           $autoOutOrder->ticket_code = $ticketId;
//           $autoOutOrder->status = $status;
//           $autoOutOrder->modify_time = date('Y-m-d H:i:s');
//           if (!$autoOutOrder->save()) {
//               $errorMsg = $autoOutOrder->errors;
//               return \Yii::jsonError(400, $errorMsg);
//           }
//           KafkaService::addQue('ConfirmOutTicket', ['orderCode' => $autoOutOrder->order_code], true);
////            OrderDeal::confirmOutTicket($autoOutOrder->order_code);
//       }
//        return $ret;
//    }

    /**
     * 说明: 1101 竞彩出票接口
     * @author  kevi
     * @date 2018年1月5日 下午4:41:04
     * @param   $paramsXml  异步回调xml参数
     * @return
     */
    public function to1002($data)
    {
        $dataXml = $this->formatxml($data);
        $key = \Yii::$app->params['zmf_key'];
        $desModel = new Des($key, $this->iv);
        $desData = $desModel->encrypt($dataXml); //des3加密
        ob_clean();
//        print_r($desData);die;
        //post消息体封装
        $venderId = \Yii::$app->params['zmf_venderId'];
        $head = ['head' => ['version' => 1500, 'command' => 1002, 'venderId' => '18020601', 'messageId' => $data['messageId']]];
        $head['head']['md'] = md5($desData);
        $head['body'] = $desData;
        $head = $this->formatxml($head, 'message');
        //提交请求
        $postRet = $this->xmlpost($head);
        $ret = $this->xmlToArray($postRet);
        $ticketId = '';

        if ($ret['head']['result'] == 0) {
            $xmlret = $desModel->decrypt($ret['body']);
            $ret = $this->xmlToArray($xmlret);
//            $ticketId = $ret['records']['record']['ticketId'];
        }
        return $ret;
    }


    public function to1101($paramsXml) {
        $paramsArr = $this->xmlToArray($paramsXml);
        //des3解密
        $key = \Yii::$app->params['zmf_key'];
        $desModel = new Des($key, $this->iv);
        $desData = $desModel->decrypt($paramsArr['body']);

        $body = $this->xmlToArray($desData);

        $messageId = $body['messageId'];
        $ticketId = '';
        if ($body['result'] == 0) {
            $ticketId = $body['records']['record']['ticketId'];
            $status = 4;
        } else {
            $status = 5;
        }


        //写日志
        $zmfOrder = ZmfOrder::find()->where(['order_code' => $body['records']['record']['id']])->one();
        if (!empty($zmfOrder)) {
            $zmfOrder->ret_async_data = json_encode($body);
            $zmfOrder->status = 1;
            $zmfOrder->modify_time = date('Y-m-d H:i:s');
            if (!$zmfOrder->save()) {
                $errorMsg = $zmfOrder->errors;
                \Yii::redisSet('error1', $errorMsg);
                $data2 = [
                    'records' => [
                        'record' => [
                            'id' => $body['records']['record']['id'],
                            'result' => 0,
                        ]
                    ]
                ];
                return ['data'=>$data2,'messageId'=>$messageId];
            }
            $autoOutOrder = AutoOutOrder::findOne(['out_order_code' => $zmfOrder->order_code, 'status' => 2]);
            if (empty($autoOutOrder)) {
                $data2 = [
                    'records' => [
                        'record' => [
                            'id' => $body['records']['record']['id'],
                            'result' => 0,
                        ]
                    ]
                ];
                KafkaService::addLog('zmf1101_log',time());
                return ['data'=>$data2,'messageId'=>$messageId];
            }
            $autoOutOrder->ticket_code = $ticketId;
            $autoOutOrder->status = $status;
            $autoOutOrder->modify_time = date('Y-m-d H:i:s');
            if (!$autoOutOrder->save()) {
                $errorMsg = $autoOutOrder->errors;
                \Yii::redisSet('error1', $errorMsg);
                $data2 = [
                    'records' => [
                        'record' => [
                            'id' => $body['records']['record']['id'],
                            'result' => 0,
                        ]
                    ]
                ];
                return ['data'=>$data2,'messageId'=>$messageId];
            }
            KafkaService::addQue('ConfirmOutTicket', ['orderCode' => $autoOutOrder->order_code], true);
        }

        $data2 = [
            'records' => [
                'record' => [
                    'id' => $body['records']['record']['id'],
                    'result' => 0,
                ]
            ]
        ];
        return ['data'=>$data2,'messageId'=>$messageId];
    }


    public function toxml($data2, $id) {
        $dataXml = $this->formatxml($data2);
        $key = \Yii::$app->params['zmf_key'];
        $desModel = new Des($key, $this->iv);
        $desData = $desModel->encrypt($dataXml); //des3加密
        //post消息体封装
        $head = ['head' => ['version' => 1500, 'command' => 1101, 'venderId' => 20170102, 'messageId' => $id]];
        $head['head']['md'] = md5($desData);
        $head['body'] = $desData;
        $ret = $this->formatxml($head, 'message');
        return $ret;
    }

    public function toxml2($data) {
        $dataXml = $this->formatxml($data2);
        $key = \Yii::$app->params['zmf_key'];
        $desModel = new Des($key, $this->iv);
        $desData = $desModel->encrypt($dataXml); //des3加密
        //post消息体封装
        $head = ['head' => ['version' => 1500, 'command' => 1101, 'venderId' => 20170102, 'messageId' => $id]];
        $head['head']['md'] = md5($desData);
        $head['body'] = $desData;
        $ret = $this->formatxml($head, 'message');
        return $ret;
    }

    public function formatxml($arr, $firstDom = "body", $dom = 0, $item = 0) {
        if (!$dom) {
            $dom = new \DOMDocument("1.0");
        }
        if (!$item) {
            $item = $dom->createElement($firstDom);
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            $itemx = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemx);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
            } else {
                $this->formatxml($val, $firstDom, $dom, $itemx);
            }
        }
        $dom->encoding = 'UTF-8';
        return $dom->saveXML();
    }

    public function xmlToArray($xml) {

        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring), true);

        return $val;
    }

    public function xmlpost($xml_data) {
        $url = 'http://120.77.204.131:8098/';
        $header[] = "Content-type: application/xml"; //定义content-type为xml
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    public function zmfencrypt($data){
        $key = \Yii::$app->params['zmf_key'];
        $desModel = new Des($key, '12345678');
        $jmxit = $desModel->encrypt($data);
        return $jmxit;
    }

}
