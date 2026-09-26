<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 10:55:56
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 13:47:15
 * @Description: 
 */

namespace app\extensions\rajal\models;

use Yii;

class ModelKonsulPoliAdhy extends \app\modules\rajal\models\KonsulpoliForm
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'pasien_id',
                    'tgl_konsulpoli',
                    'asalpoliklinikkonsul_id',
                ], 
                'required'
            ],
            [['ruangan_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'daftartindakan_id', 'pegawai_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_konsulpoli', 'tglberlakukonsul_sd', 'created_date', 'last_modified_date', 'deleted_date', 'jadwaldokter_id', 'jawaban_konsul', 'pegawai_id','jadwal'], 'safe'],
            [['catatan_dokter_konsul', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['status_periksa'], 'string', 'max' => 50],
            [['no_antriankonsul'], 'string', 'max' => 6],

            // required in "update" scenario
            // [['ruangan_id', 'pegawai_id'], 'required', 'on' => self::SCENARIO_UPDATE],
        ];
    }
}
