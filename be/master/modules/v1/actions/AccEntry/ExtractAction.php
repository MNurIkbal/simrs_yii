<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\AccEntry;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Action;
use app\modules\v1\models\LogOdooCutoff;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\RabbitBgProcess;

class ExtractAction extends Action {

    public $components = '';
    public $paramDate;
    private $sync_type; 
    private $is_file;
    private $is_table;

    protected function initSecondaryDatabaseConfiguration()
    {
        $conf = @parse_ini_file(Yii::getAlias('@app').'/config/env/.env', true);
        $this->sync_type = ArrayHelper::getValue($conf,'dbjurnal.sync_type','0');
        $this->is_file = ArrayHelper::getValue($conf,'dbjurnal.is_file','0');
        $this->is_table = ArrayHelper::getValue($conf,'dbjurnal.is_table','0');
        $configuration = [
            'components'=>[
                'db_extract' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => ArrayHelper::getValue($conf,'dbjurnal.conn_str',"pgsql:host=localhost;port=5432;dbname=db"),
                    'username' => ArrayHelper::getValue($conf,'dbjurnal.user','postgres'),
                    'password' => ArrayHelper::getValue($conf,'dbjurnal.password','postgres'),
                    'charset' => 'utf8',
                    'schemaMap' => [
                        'pgsql' => [
                        'class' => 'yii\db\pgsql\Schema',
                        'defaultSchema' => 'public' 
                        ]
                    ],
                ]
        ]];
        \Yii::configure(\Yii::$app, $configuration);
    }

    public function run() {
        try{
            $this->initSecondaryDatabaseConfiguration();

            $date = $this->paramDate;
            $tableName = (new $this->components)->getTableName();
            $result = $deleted = null;

            if($this->is_table == '1'){
                $deleted = Yii::$app->db_extract->createCommand("
                DELETE FROM $tableName WHERE date(cutoff_date) = :date
                ")->bindValue(':date',$date)->execute();

                $result = Yii::$app->db_extract->createCommand()->batchInsert(
                    (new $this->components)->getTableName(),
                    (new $this->components)->getAttributes(),
                    (new $this->components)->extract($date,$this->sync_type)
                )->execute();

                $log = new LogOdooCutoff;
                $log->model_name = $tableName;
                $log->cutoff_date = $date;
                $log->triggered_by = (php_sapi_name()=="cli") ? "cron" : "api";
                $log->generated_data = $result;
                $log->deleted_data = $deleted;
                $log->save();
            }

            if($this->is_file == '1'){
                (new RabbitBgProcess())->send([
                    'unique_str' => DocoHelpers::generateRandomString(),
                    'components' => $this->components,
                    'sync_type' => $this->sync_type,
                    'using_date' => true,
                    'date' => $this->paramDate,
                ], 'extract_csv','extract_csv');
            }

            return [
                'count' => $result,
                'deleted' => $deleted,
                'message' => 'Sukses',
                'text' => 'Sukses'
            ];

            //
        } catch(\Exception $e){
            Yii::error($e->getMessage(),'acc-entry');

            \Yii::$app->response->statusCode = 500;
            return [
                'count' => 0,
                'deleted' => 0,
                'message' => $e->getMessage(),
                'text' => 'Gagal'
            ];
        }
    }
}