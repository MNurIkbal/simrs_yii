<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstansId;
use Doco\models\InfoPasienPenunjangView;

class DevToolsController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["pindah-billing"] = ["POST"];
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionPindahBilling()
    {
        $pendaftaran_sumber = Yii::$app->request->post('pendaftaran_sumber','0');
        $pendaftaran_tujuan = Yii::$app->request->post('pendaftaran_tujuan','0');

        $query = "select status as status_pindah,message as status_message from sp_gabung_billing(:pendaftaran_sumber,:pendaftaran_tujuan)";
        return Yii::$app->db->createCommand($query)
            ->bindValue(':pendaftaran_sumber',$pendaftaran_sumber)
            ->bindValue(':pendaftaran_tujuan',$pendaftaran_tujuan)
            ->queryOne();
    }

    public function actionGetPasienPenunjang($keyword)
    {
        $limit = Yii::$app->request->get('limit',5);
        $offset = Yii::$app->request->get('offset',0);

        $instalasi_lab = (new DocoConstansId)->actionGetId('LAB');

        $result = InfoPasienPenunjangView::find()->select([
            "pasienmasukpenunjang_id",
            "CONCAT(no_rekam_medik,'/',no_pendaftaran,'/',no_masukpenunjang) as reference"
        ])
        ->where(['instalasi_id'=>$instalasi_lab])
        ->andWhere((new \yii\db\conditions\OrCondition([
            ['ilike', 'no_rekam_medik',$keyword],
            ['ilike', 'no_masukpenunjang',$keyword],
            ['ilike', 'no_pendaftaran', $keyword],
        ])))->offset($offset)->limit($limit)->asArray()->all();
        return $result;
    }

    public function actionGetRiwayatIntegrasiLis()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('pasienmasukpenunjang_id',0);
        $file = Yii::getAlias('@app/config/env/.env');
        $datasource_config = parse_ini_file($file, true);

        if(isset($datasource_config['db_integration'])){
            $configuration = [
                'components'=>[
                    'db_integration' => [
                        'class' => 'yii\db\Connection',
                        'dsn' => isset($datasource_config['db_integration']['conn_str']) ? $datasource_config['db_integration']['conn_str'] : 'pgsql:host=localhost;port=5432;dbname=db_sirs_integration',
                        'username' => isset($datasource_config['db_integration']['user']) ? $datasource_config['db_integration']['user'] : 'postgres',
                        'password' => isset($datasource_config['db_integration']['password']) ? $datasource_config['db_integration']['password'] : '',
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

            $db = Yii::$app->db_integration;
            $query = $db->createCommand("
                        SELECT id,pendaftaran_id,pasienmasukpenunjang_id,payload,is_sent,is_sending,id_sync_sercon,sync_respon,created_date,state 
                        FROM integrasi_roche_r where pasienmasukpenunjang_id = :pasienmasukpenunjang_id
                    ")
                    ->bindValue(':pasienmasukpenunjang_id',$pasienmasukpenunjang_id)
                    ->queryAll();
            $data = [];
            if(is_array($query) && count($query)>=1){
                foreach ($query as $_item){
                    $data[] = [
                        'id' => $_item['id'],
                        'pendaftaran_id' => $_item['pendaftaran_id'],
                        'pasienmasukpenunjang_id' => $_item['pasienmasukpenunjang_id'],
                        'payload' => json_decode($_item['payload'],true),
                        'is_sent' => $_item['is_sent'],
                        'is_sending' => $_item['is_sending'],
                        'id_sync_sercon' => $_item['id_sync_sercon'],
                        'sync_respon' => json_decode($_item['sync_respon'],true),
                        'created_date' => $_item['created_date'],
                        'state' => $_item['state']
                    ];
                }
            }
            return [
                'message'=>'OK',
                'data'=>$query
            ];
        }else{
            return [
                'data' => null,
                'message'=>'Failed to connect'
            ];
        }
    }
}