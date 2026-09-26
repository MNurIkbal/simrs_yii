<?php


/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components;

use Yii;
use yii\di\Instance;
use Integrasi\Components\DocoHelpers;
use yii\helpers\ArrayHelper;

class DocoActiveRecord extends \yii\db\ActiveRecord
{
    protected $xssProtected = [];
    public function beforeValidate()
    {
        if (!empty($this->xssProtected)) {
            $is_xss = false;
            foreach ($this->xssProtected as $value) {
                if(!empty($this->$value)) {
                    $this->$value = strip_tags(\yii\helpers\HtmlPurifier::process($this->$value));
                    // $this->$value = \yii\helpers\HtmlPurifier::process($this->$value);
                    if(empty($this->$value)) {
                        $this->addError($value, 'Data Tidak Valid');
                        $is_xss = true;
                    }
                }
            }

            if($is_xss) return false;
         }

        return true;
    }

    public function fields()
    {
        $fields = parent::fields();
        // hide fields that contain sensitive information
        $sensitiveFields = [
            'created_date', 
            'created_by',
            'modified_count', 
            'last_modified_date', 
            'last_modified_by',
            'is_deleted', 
            'deleted_date', 
            'deleted_by'
        ];
        foreach ($sensitiveFields as $sensitiveField)
            if (in_array($sensitiveField, $fields))
                unset($fields[$sensitiveField]);
        return $fields;
    }

    public function beforeSave($insert=null)
    {
        if (parent::beforeSave($insert=null)) {
            $attrs = $this->getAttributes();
            foreach ($attrs as $attr => $val) {
                if (strpos($attr, 'tanggal') !== false) {
                    $this->$attr = DocoHelpers::convertTo422($val);
                }

                if (strpos($attr, 'tgl') !== false) {
                    $this->$attr = DocoHelpers::convertTo422($val);
                }

                if ($this->$attr === '') {
                    $this->$attr = null;
                }
            }
            $data = self::getDefaultData();
            if ($this->isNewRecord) {
                if ($this->hasAttribute('modified_count')) {
                    $this->modified_count = 0;
                }
                if ($this->hasAttribute('created_date')) {
                    $this->created_date = $this->created_date ? $this->created_date : $data->date;
                }
                // Menambahkan kondisi created by untuk kebutuhan mobile
                if ($this->hasAttribute('created_by')) {
                    $this->created_by =  $this->created_by ? $this->created_by : $data->by;
                }
                self::setActivityLog('insert',$this->attributes,'INSERT');
            } else {
                if ($this->hasAttribute('modified_count')) {
                    $this->modified_count += 1;
                }
                if ($this->hasAttribute('last_modified_date')) {
                    $this->last_modified_date = $data->date;
                }
                if ($this->hasAttribute('last_modified_by')) {
                    $this->last_modified_by = $data->by;
                }
                $auditUpdate = [
                    'before' => $this->getOldAttributes(),
                    'after' => $this->attributes
                ];
                self::setActivityLog('update',$auditUpdate,'UPDATE');
            }

            return true;
        }

        return false;
    }


    /**
    * @param object $model
    * @param array $data
    * @param boolean $validate
    *
    * @return void
    * @throws Exception
    */
    public static function batchInsert($data = [],$validate = true)
    {
        if (count($data)) {
            $rows = [];
            //$default = self::getDefaultData();
            $primary = self::primaryKey();
            $tableName = self::getTableSchema()->name;
            $className = get_called_class();
            foreach ($data as $key => $value) {
                $newModel = new $className;
                //$value['created_by'] = $default->by;
                //$value['created_date'] = $default->date;
                //$value['is_deleted'] =  false;
                //$value['is_active'] =  isset($value['is_active']) ? $value['is_active'] : true;
                $newModel->attributes = $value;
                if ($validate) {
                    if (!$newModel->validate()) {
                        Yii::info('Terjadi Kesalahan ' . __METHOD__ . ' Check Validasi : '. json_encode($newModel->errors));
                        throw new \Exception('Terjadi Kesalahan ' . __METHOD__ . ' Check Validasi : '. json_encode($newModel->errors));
                        break;
                    }
                }
                $attr = $newModel->attributes;

                if (isset($primary[0])) {
                    unset($attr[$primary[0]]);
                }

                $rows[] = $attr;
            }

            //self::setActivityLog('insertBatch',$rows,'INSERT');

            $newModel = new $className;
            $attributes = $newModel->attributes();

            foreach ($attributes as $key => $value) {
                if (isset($primary[0]) && $value == $primary[0]) {
                    unset($attributes[$key]);
                    break;
                }
            }

            return $className::getDb()->createCommand()->batchInsert($tableName,$attributes, $rows)->execute();
        }
    }

    /**
    * @author yaya
    * @since 
    * @param is_deleted bool default false 
    * @return object
    * updated by rizal 2018-01-09 10:24:20
    */
    public static function find($ignoreIsDeleted=false)
    {
        if ($ignoreIsDeleted == true) return parent::find();

        $table = self::getTableSchema()->name;
        $className = get_called_class();
        $newModel = new $className;
        $attributes = $newModel->attributes();
        if (in_array('is_deleted', $attributes)) {
            return parent::find()->onCondition([$table.'.is_deleted' => false]);
        }
        return parent::find();
    }

    public static function getDefaultData()
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        return (object)array(
            'date' => date('Y-m-d H:i:s', time()),
            'by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1',
        );
    }

    /**
    * @param string type key untuk activity user mengakses model 
    * @param array $detail untuk menampung data yag di akses oleh penguna model
    * @param string $action untuk menampung action apa saja yang meng akses sebuah action
    *
    * @return void
    **/

    private static function setActivityLog($type = 'insert',$detail = array(),$action)
    {
        $jwt = Instance::ensure('jwt', \Doco\components\DocoJwt::className());
        $detailActivity = $jwt->activity;
        $detailAction = $jwt->action;
        $className = get_called_class();
        
        if (!isset($detailActivity[$className][$type])) {
           $detailActivity[$className][$type] = [];
        }

        if (empty($detailAction)) {
            $detailAction = $action;
            $jwt->action = $detailAction;
        }

        $detailActivity[$className][$type][]= $detail;

        $jwt->activity = $detailActivity;
    }

    /**
    * @author 
    * @since 
    * @param id 
    * @return 
    * updated by rizal 2018-01-05 11:07:00 gabungin method updateByPk ke sini.
    */
    public function delete($pk=null)
    {
        try {
            $primarykey = self::primaryKey();
            $table = self::getTableSchema()->name;
            $data = self::getDefaultData();
            $connection = self::getDb();
            if (isset($primarykey[0]) && !is_array($pk)) {
                $primarykey = $primarykey[0];
                $pk = $pk ? : $this->{$primarykey};
                $result = $connection->createCommand()->update(
                    $table, 
                    [
                        'is_deleted' => true,
                        'deleted_date' => $data->date,
                        'deleted_by' => $data->by
                    ],
                    [
                        $primarykey => $pk,
                        'is_deleted' => false
                    ]
                )->execute();
            } else {
                $result = $connection->createCommand()->update(
                    $table, 
                    [
                        'is_deleted' => true,
                        'deleted_date' => $data->date,
                        'deleted_by' => $data->by
                    ],$pk
                )->execute();
            }
            self::setActivityLog(
                'update',[
                    'is_deleted' => true,
                    'deleted_date' => $data->date,
                    'deleted_by' => $data->by
                ],
                'DELETE'
            );
            return $result;
        } catch (\yii\db\Exception $e) {
            Yii::trace('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
            throw new \Exception('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-08 16:48:22 
    * @param $pk1 integer $pk2 integer
    * @return 
    * @desc 
    */
    public static function deleteMapping($pk1 = null, $pk2 = null)
    {
        try {
            $primarykey = self::primaryKey();
            if (isset($primarykey)) {
                $primarykey1 = $primarykey[0];
                $primarykey2 = $primarykey[1];
                $pk1 = $pk1 ? : $primarykey1;
                $pk2 = $pk2 ? : $primarykey2;
                
                $table = self::getTableSchema()->name;
                $data = self::getDefaultData();
                $connection = self::getDb();
                self::setActivityLog(
                    'update',[
                        'is_deleted' => true,
                        'deleted_date' => $data->date,
                        'deleted_by' => $data->by
                    ],
                    'DELETE'
                );
                return $connection->createCommand()->update(
                    $table, 
                    [
                        'is_deleted' => true,
                        'deleted_date' => $data->date,
                        'deleted_by' => $data->by
                    ],
                    [
                        $primarykey1 => $pk1,
                        $primarykey2 => $pk2
                    ]
                )->execute();
            }

            return false;
        } catch (\yii\db\Exception $e) {
            Yii::trace('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
            throw new \Exception('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
        }
    }

    public static function dataMappingByTwoPK( $firstKey = null, $secondKey = null)
    {
        try {
            $className = get_called_class();
            $primarykey = $className::primaryKey();
            
            if (isset($primarykey)) {
                $firstFieldKey = $primarykey[0];
                $secondFieldKey = $primarykey[1];
                
                $table = self::getTableSchema()->name;
                $getDefaultData = self::getDefaultData();
                $connection = self::getDb();
                    
                $newModel = new $className;
                $getData = $newModel->find(true)->where([$firstFieldKey => (int)$firstKey,
                                                        $secondFieldKey => (int)$secondKey,
                                                        'is_deleted' => true])
                                                ->one();
                if( !empty($getData) ){
                    $dataDefault = $getData;
                    $dataDefault->created_date = $getDefaultData->date;
                    $dataDefault->created_by = $getDefaultData->by;
                    $dataDefault->modified_count = 1;
                    $dataDefault->last_modified_date = $getDefaultData->date;
                    $dataDefault->last_modified_by = $getDefaultData->by;
                    $dataDefault->is_deleted = false;
                    $dataDefault->is_active = true;
                    $dataDefault->deleted_date = $getDefaultData->date;
                    $dataDefault->deleted_by = NULL;                   
                    $dataDefault->save();
                    
                    return true; 
                }                    
            }else{

                return false;
            }
        } catch (\yii\db\Exception $e) {
            Yii::trace('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
            throw new \Exception('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
        }
    }

    public static function deleteMappingByTwoPK( $firstKey = null, $secondKey = null)
    {
        try {
            $className = get_called_class();
            $primarykey = $className::primaryKey();
            
            if (isset($primarykey)) {
                $firstFieldKey = $primarykey[0];
                $secondFieldKey = $primarykey[1];
                
                $table = self::getTableSchema()->name;
                $getDefaultData = self::getDefaultData();
                $connection = self::getDb();
                    
                $newModel = new $className;
                $getData = $newModel->find(true)->where([$firstFieldKey => $firstKey,
                                                        $secondFieldKey => $secondKey,
                                                        'is_deleted' => false])
                                                ->one();
                if( !empty($getData) ){
                    $dataDefault = $getData;
                    $dataDefault->created_date = $getDefaultData->date;
                    $dataDefault->created_by = $getDefaultData->by;
                    $dataDefault->modified_count = 1;
                    $dataDefault->last_modified_date = $getDefaultData->date;
                    $dataDefault->last_modified_by = $getDefaultData->by;
                    $dataDefault->is_deleted = true;
                    $dataDefault->is_active = true;
                    $dataDefault->deleted_date = $getDefaultData->date;
                    $dataDefault->deleted_by = $getDefaultData->by;              
                    $dataDefault->save();
                    
                    return true; 
                }else{
                    return false;
                }                    
            }else{

                return false;
            }
        } catch (\yii\db\Exception $e) {
            Yii::trace('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
            throw new \Exception('Terjadi Kesalahan' . __METHOD__ . $e->getMessage());
        }
    }

    /**
     *
     * @author rizfardi@docotel.com
     * Fungsi pengganti save untuk clear cache
     * tapi belum dipake hehehe
     *
     */
    public function saveCache($cache_name = null, $cache_key = null)
    {
        try
        {   
            $cache = Yii::$app->cache;
            if ($cache_name){
                if (parent::save($runValidation = true, $attributeNames = null)){
                    if ($data_cache = $cache->get($cache_name)){
                        if ($cache_key){
                            foreach ($data_cache as $key => $value) {
                                if ($key != $cache_key){
                                    continue;
                                }else{
                                    unset($data_cache[$key]);
                                }
                            }

                            $cache->set($cache_name, $data_cache);
                        }else{
                            $cache->delete($cache_name);
                        }
                    }

                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        } catch (Exception $exception)
        {
            echo $exception->getMessage();
            return false;
        }
    }
}
