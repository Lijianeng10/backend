<?php

namespace app\modules\components\redis;

use yii\base\Exception;
use app\modules\components\redis\Connection;

class rediscli extends Connection
{
    private $fields = [];
    private $tablename;

    public function select($fields)
    {
        return $this;
    }

    public function where($is)
    {

        return $this;
    }

    //使用的时候现制定表名
    public function TableName($tablename)
    {
        $this->tablename = $tablename;
        return $this;
    }

    //添加id 后面对应的键值对
    public function add($id,$params = [])
    {
        $len = $this->ExistsById($id);

        if($len ==0)
        {
            $count = 0;
            foreach($params as $k => $v)
            {
                $cmd = $this->Make($id,[$k,$v]);
                $count += $this->HSet($cmd);
            }
            return $count;
        }
        else
        {
            return null;
        }
    }

    public function Del($id,$fields = [])
    {
        $cmd = $this->Make($id,$fields);
        $redis = $this->redis();
        return $redis->executeCommand('hdel',$cmd);
    }

    public function ExistsById($id)
    {
        $redis = $this->redis();
        $cmd = $this->Make($id,[]);
        return $redis->executeCommand('hlen',$cmd);
    }

    public function Edit($params)
    {
        return $this->HSet($params);
    }

    public function HGet($params = [])
    {
        $redis = $this->redis();
        return $redis->executeCommand('hget',$params);
    }

    public function HSet($params = [])
    {
        $redis = $this->redis();
        return $redis->executeCommand('hset',$params);
    }

    private function redis()
    {
        return \yii::$app->redis;
    }

    private function Make($id,$prams = [])
    {
        if($this->tablename != '')
        {
            unset($this->fields);
            $this->fields = [];
            $command = $this->tablename.":".$id;
            $this->fields[0] = $command;
            foreach($prams as $val)
            {
                array_push($this->fields,$val);
            }
            return $this->fields;
        }
        else
        {
            throw new Exception("Redis error: " . "tablename is null");
            return [];
        }

    }
}