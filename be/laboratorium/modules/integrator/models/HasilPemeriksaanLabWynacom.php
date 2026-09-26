<?php

namespace app\modules\integrator\models;

use Yii;
use app\modules\v1\models\HasilLabWynacomView;

class HasilPemeriksaanLabWynacom extends \Doco\components\DocoActiveRecord {

    public static function tableName()
    {
        return 'hasilpemeriksaanlab_wynacom_t';
    }

    public function rules()
    {
        return [
            [['lis_reg_no', 'his_reg_no', 'lis_test_id', 'test_name', 'his_test_id'], 'required', 'message' => '{attribute} Tidak boleh kosong!','on' => 'non_authorization'],
            [
                ['lis_reg_no', 'his_reg_no', 'lis_test_id', 'test_name', 'result', 'his_test_id','authorization_date','authorization_user'], 'required', 'message' => '{attribute} Tidak boleh kosong!'
            ],
            [
                ["result_comment","reference_value","reference_note","test_flag_sign","test_units_name","instrument_name","greaterthan_value","lessthan_value","age_year","age_month","age_days","sequence","transfer_flag","test_group","test_method","authorization_date"], 'default', 'value' => null
            ],
            [
                [
                    "lis_reg_no","lis_test_id","his_reg_no","test_name","result","result_comment","reference_value","reference_note","test_flag_sign","test_units_name","instrument_name","authorization_date","authorization_user","greaterthan_value","lessthan_value","age_year","age_month","age_days","his_test_id","sequence","transfer_flag","test_group","test_method",'is_deleted', 'is_active'
                ], 'safe'
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['non_authorization'] = ['lis_reg_no', 'his_reg_no', 'lis_test_id', 'test_name', 'his_test_id'];
        return $scenarios;
    }

    /**
     * This function will return latest record by array lis test id
     * 
     * @param String $id
     * @return Class/Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function latestRecordById($id)
    {
        $subQuery = HasilLabWynacomView::find()->alias('latest')->select(['MAX(tgl_pemeriksaan)'])->where(["latest.lis_test_id" => new \yii\db\Expression('laporanhasillab_v.lis_test_id'), "latest.pasienmasukpenunjang_id" => new \yii\db\Expression('laporanhasillab_v.pasienmasukpenunjang_id')]);

        return HasilLabWynacomView::find()
            ->select([
                'dokter_penunjang',
                'no_masukpenunjang',
                'dokter_pengirim',
                'no_rekam_medik',
                'nama_pasien',
                'tgl_transaksi',
                'dateofbirth',
                'umur',
                'tgl_hasil',
                'jeniskelamin_nama',
                'tgl_cetak',
                'alamat_pasien',
                'lokasi_nama',
                'test_nama_lis',
                'hasil',
                'nilai_rujukan',
                'satuan',
                'test_method',
                'test_flag_sign',
                'authorization_user'
            ])
            ->andWhere([
                'pasienmasukpenunjang_id' => $id
            ])
            ->andWhere(['=', 'tgl_pemeriksaan', $subQuery])
            ->orderBy([
                'tgl_pemeriksaan' => SORT_DESC
            ])
            ->asArray()
            ->all();
    }

    /**
     * This function will return latest record by array lis test id
     * 
     * @param String $id
     * @return Class/Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function latestRecordByOrderNumber($orderNumber)
    {
        $subQuery = HasilLabWynacomView::find()->alias('latest')->select(['MAX(tgl_pemeriksaan)'])->where(["latest.lis_test_id" => new \yii\db\Expression('laporanhasillab_v.lis_test_id'), "latest.pasienmasukpenunjang_id" => new \yii\db\Expression('laporanhasillab_v.pasienmasukpenunjang_id')]);

        return HasilLabWynacomView::find()
            ->select([
                'dokter_penunjang',
                'no_masukpenunjang',
                'dokter_pengirim',
                'no_rekam_medik',
                'nama_pasien',
                'tgl_transaksi',
                'dateofbirth',
                'umur',
                'tgl_hasil',
                'jeniskelamin_nama',
                'tgl_cetak',
                'alamat_pasien',
                'lokasi_nama',
                'test_nama_lis',
                'hasil',
                'nilai_rujukan',
                'satuan',
                'test_method',
                'authorization_user'
            ])
            ->andWhere([
                'no_masukpenunjang' => $orderNumber
            ])
            ->andWhere(['=', 'tgl_pemeriksaan', $subQuery])
            ->orderBy([
                'tgl_pemeriksaan' => SORT_DESC
            ])
            ->asArray()
            ->all();
    }
}