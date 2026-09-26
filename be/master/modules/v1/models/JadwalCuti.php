<?php


namespace app\modules\v1\models;

use Yii;
use \DateTime;
use \DateInterval;
use \DatePeriod;
use app\modules\v1\models\JadwalCutiView;

class JadwalCuti extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwalcuti_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pegawai_id', 
                'ruangan_id',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
                'alasan_cuti',
            ], 'required'],
            [[
                'pegawai_id', 
                'spesialis_id',
                'ruangan_id',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
                'alasan_cuti',
                'created_by',
                'created_date',
                'is_deleted',
                'is_active',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalcuti_id' => 'Jadwal Cuti ID',
            'pegawai_id' => 'Dokter',
            'ruangan_id' => 'Ruangan',
            'spesialis_id' => 'Spesialis ID',
            'spesialis_nama' => 'Spesialis',
            'tgl_cuti_awal' => 'Tanggal Cuti Awal',
            'tgl_cuti_akhir' => 'Tanggal Cuti Akhir',
            'alasan_cuti' => 'Alasan Cuti',
        ];
    }

    /**
     * @inheritdoc
     * 
     * param :
     * pegawai_id => int
     * ruangan_id => int
     * tgl_cuti_awal => date
     * tgl_cuti_akhir => date
     */
    public function validateTanggalCuti($param) {
        $result = true;
        $arrCuti = [];
        $tgl_cuti_awal = new DateTime($param['tgl_cuti_awal']);
        $tgl_cuti_akhir = new DateTime($param['tgl_cuti_akhir']);

        $interval = new DateInterval('P1D');
        $rangeTanggal = new DatePeriod($tgl_cuti_awal, $interval ,$tgl_cuti_akhir);
        foreach($rangeTanggal as $value){
            $arrCuti[] = $value->format("Y-m-d");
        }

        $data = JadwalCutiView::find()->select(['jadwalcuti_id'])
        ->where([
            'dokter_id' => $param['pegawai_id'],
            'ruangan_id' => $param['ruangan_id'],
        ])->andWhere(['IN', 'DATE(tgl_cuti_awal)', $arrCuti])
        ->asArray()->one();

        // $data = JadwalCutiView::find()->select(['jadwalcuti_id'])
        // ->where([
        //     'dokter_id' => $param['pegawai_id'],
        //     'ruangan_id' => $param['ruangan_id'],
        // ])->andWhere([
        //     'OR', 
        //     ['IN', 'DATE(tgl_cuti_awal)', $arrCuti], 
        //     ['IN', 'DATE(tgl_cuti_akhir)', $arrCuti], 
        //     ['AND', 
        //         ['<=', 'tgl_cuti_awal', $param['tgl_cuti_awal']], 
        //         ['>=', 'tgl_cuti_akhir', $param['tgl_cuti_akhir']]
        //     ]
        // ])->asArray()->one();

        if (!empty($data)) $result = false;

        return $result;
    }
}
