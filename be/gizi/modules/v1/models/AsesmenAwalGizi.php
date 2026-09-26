<?php
// author : rizal Faidin
namespace app\modules\v1\models;

use Yii;

class AsesmenAwalGizi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asesmenawalgizi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'sumberdata',
                    'bb_biasanya',
                    'bb_saatini',
                    'perubahan_kg',
                    'perubahan_persen',
                    'kategori_bb',
                    'asupanmkn',
                    'kategori_asupanmkn',
                    'gastrointestinal_mual',
                    'gastrointestinal_muntah',
                    'gastrointestinal_diare',
                    'gastrointestinal_anoreksia',
                    'kategori_gastrointestinal',
                    'fungsional',
                    'kategori_fungsional',
                    'diagnosa_medis',
                    'keb_metabolik',
                    'kategori_hubungan',
                    'kategori_fisik',
                    'penilaian_sga',
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('app','Tidak boleh kosong')
            ],
            [
                ['sumberdata_dari'],
                'required',
                'when' => function($model) {
                    return $model->sumberdata == 2;
                }
            ],
            [
                [
                    'asesmenawalgizi_id',
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'fisik_lemak',
                    'fisik_otot',
                    'fisik_udem',
                    'fisik_asites',
                    'diet',
                    'pagt',
                    'saran_terapi',
                    'perubahan_hasil',
                    'sumberdata_dari',
                    'created_date', 'last_modified_date', 'deleted_date'
                ],
                'safe'
            ],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'carabayar_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'metode_pembayaran' => Yii::t('app', 'Metode pembayaran'),
            'carabayar_loket' => Yii::t('app', 'Cara bayar loket'),
            'carabayar_singkatan' => Yii::t('app', 'Singkatan'),
            'carabayar_urutan' => Yii::t('app', 'Urutan'),
            'is_subsidiasuransi' => Yii::t('app', 'Is subsidi asuransi'),
            'is_subsidipemerintah' => Yii::t('app', 'Is subsidi pemerintah'),
            'is_subsidirs' => Yii::t('app', 'Is subsidi rs'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }


    public function getPenjamin()
    {
        return $this->hasMany(Penjamin::className(), ['carabayar_id' => 'carabayar_id']);
    }
}
