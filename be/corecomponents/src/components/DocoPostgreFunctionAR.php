<?php

namespace Doco\components;

use Yii;
use yii\di\Instance;
use Doco\components\DocoHelpers;
use Doco\components\DocoPostgreFunctionAQ;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\db\pgsql\Schema as SchemaPostgre;
use yii\db\ActiveQuery;

class DocoPostgreFunctionAR extends \yii\db\ActiveRecord
{
    protected static $_extParam;
    public $extParam;

    public function init(){
        parent::init();
        self::$_extParam = $this->extParam;
    }

    public static function find()
    {
        return Yii::createObject([
            'class' => DocoPostgreFunctionAQ::className(),
            'paramObj' => self::$_extParam,
            'modelClass' => get_called_class()
        ]);
    }

    public static function tableName($ext_obj = null)
    {
        if($ext_obj){
            $params = $ext_obj;
        }else{
            $params = self::$_extParam;
        }
        $functionParams = '()';
        if(!isset($params)){
            return static::functionName().$functionParams;
        }elseif(ArrayHelper::isAssociative($params)){
            throw new InvalidConfigException('Konfigurasi extParam tidak dapat menggunakan Associative Array');
        }elseif(is_array($params)){
            foreach ($params as $val_param) {
                if($val_param === "0" || $val_param === 0) {
                    continue;
                } else if(empty($val_param) || is_null($val_param)){
                    throw new InvalidConfigException('Konfigurasi Property extParam Salah: Nilai tidak boleh ');
                }
            }
            $functionParams = "("."'" . implode("','", $params) . "'".")";
        }elseif(is_string($params) && !empty($params)){
            $functionParams = "('".$params."')";
        }else{
            throw new InvalidConfigException('Konfigurasi Property extParam Salah');
        }
        return static::functionName().$functionParams;
    }

    public static function getTableSchema()
    {
        return Yii::createObject([
            'class' => 'yii\db\TableSchema',
            'schemaName' => 'public',
            'name' => static::functionName(),
            'fullName' => static::functionName(),
            'primaryKey' => [],
            'sequenceName' => null,
            'foreignKeys' => [],
            'columns' => static::generateColumnSchema()
        ]);
    }

    private static function generateColumnSchema()
    {
        $selfSchema = static::attributSchema();
        if(!ArrayHelper::isAssociative($selfSchema)){
            throw new InvalidConfigException('Konfigurasi Method attributSchema() Salah');
        }

        $objPostgre = new SchemaPostgre();
        $typePostgre = $objPostgre->typeMap;

        $_attr = [];
        foreach ($selfSchema as $dbAttr => $attr_schema) {
            if(!isset($typePostgre[$dbAttr]) || !is_string($dbAttr)){
                throw new InvalidConfigException("Tipe data {$dbAttr} tidak terdaftar");
            }
            $mappedAttr = $typePostgre[$dbAttr];
            if(!is_array($attr_schema)){
                throw new InvalidConfigException("Nilai untuk Key Attribut {$dbAttr} harus berupa Array");
            }
            foreach ($attr_schema as $v_attr_schema) {
                if(isset($_attr[$v_attr_schema])){
                    throw new InvalidConfigException("Duplikat attribut {$v_attr_schema}");
                }
                $_attr[$v_attr_schema] = Yii::createObject([
                    'class' => 'yii\db\pgsql\ColumnSchema',
                    'name' => $v_attr_schema,
                    'type' => $mappedAttr,
                    'phpType' => $mappedAttr,
                    'dbType' => $dbAttr
                ]);
            }
        }
        return $_attr;
    }
    /**
   * lorem
   *
   * @param String var
   * @return JSON
   * @author : Tsani Nashrullah (tsani@docotel.com)
   * A product of PT. Docotel Teknologi
   * Powered by Sirs
   */
    protected static function attributSchema()
    {
        return [
            'int4' => [
            ],
            'int8' => [
            ],
            'varchar' => [
            ],
            'float8'  => [
            ],
            'text' => [
            ]
        ];
    }
}