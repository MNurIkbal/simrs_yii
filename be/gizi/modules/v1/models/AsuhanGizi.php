<?php
// author : rizal Faidin
namespace app\modules\v1\models;

use Yii;

class AsuhanGizi extends \Doco\components\DocoActiveRecord
{
    


    


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asuhangizi_t';
    }

    public $arr = [
        'gizi_makanan' => [
            'alergi_makanan',
            'pantangan_makanan',
            'ketidaksukaan_makanan',
            'pengalaman_diet',
            'pengalaman_diet_desc',
            'catatan',
        ],
        'antropometri'=>[
            'bb_saatini',
            'pb_tb',
            'bb_biasanya',
            'imt',
            'status_gizi',
            'penurunan_bb',
            'kurun_waktu',
            'pengukuran_lainnya',
        ],
        'biokimia'=>[
            'biokimia',
            'prosedur',
        ],
        'fisikklinis_gizi'=>[
            'antropi_otot_lengan',
            'udem',
            'hilang_lemak_subkutan',
            'nafsu_makan',
            'mual',
            'muntah',
            'kembung',
            'konstipasi',
            'diare',
            'gangguan_menelan',
            'gangguan_mengunyah',
            'gangguan_menghisap',
            'kulit',
            'kepala_dan_mata',
            'gigi_geligi',
            'tekanan_darah_mm',
            'tekanan_darah_hg',
            'tekanan_darah_mmhg',
            'tekanan_darah_kondisi',
            'detak_nadi',
            'denyut_jantung',
            'pernapasan',
            'suhu_tubuh',
            'data_lain',
        ],
        'diagnosa_gizi'=>[
            'diagnosa_gizi',
        ],
        'intervensi_gizi'=>[
            'tujuan',
            'materi',
            'sasaran',
            'preskripsi_diet',
            'jenis_diet',
            'rute',
            'edukasi_gizi',
            'media',
            'target_intervensi',
        ],
        'rencana_gizi'=>[
            'rencana_evaluasi',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'tgl_asuhangizi',

                    'peg_gizi_id',
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('app','Tidak boleh kosong')
            ],
            [
                [
                    'gizi_makanan',
                    'antropometri',
                    'biokimia',
                    'fisikklinis_gizi',
                    'diagnosa_gizi',
                    'intervensi_gizi',
                    'rencana_gizi',
                    'created_date', 'last_modified_date', 'deleted_date',
                ],
                'safe'
            ],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
        ];
    }
}
