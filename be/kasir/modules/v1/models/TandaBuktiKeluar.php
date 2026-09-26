<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tandabuktikeluar_t".
 *
 * @property int $tandabuktikeluar_id
 * @property int $returbayarpelayanan_id
 * @property int $pembatalanuangmuka_id
 * @property int $shift_id
 * @property int $pengembalianuangmuka_id
 * @property int $ruangan_id
 * @property int $bank_id
 * @property int $pegawai1_id
 * @property int $pegawai2_id
 * @property string $tgl_buktikeluar
 * @property string $no_buktikeluar
 * @property double $jml_pembayaran
 * @property double $jml_pembulatan
 * @property double $biaya_administrasi
 * @property double $biaya_materai
 * @property double $uang_diterima
 * @property double $uang_dikembalikan
 * @property bool $is_tunai
 * @property string $namapenerima
 * @property string $namapemilik_rek
 * @property string $no_rek
 * @property string $keterangan_pembayaran
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class TandaBuktiKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tandabuktikeluar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['returbayarpelayanan_id', 'pembatalanuangmuka_id', 'shift_id', 'pengembalianuangmuka_id', 'ruangan_id', 'bank_id', 'pegawai1_id', 'pegawai2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returbayarpelayanan_id', 'pembatalanuangmuka_id', 'shift_id', 'pengembalianuangmuka_id', 'ruangan_id', 'bank_id', 'pegawai1_id', 'pegawai2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'tgl_buktikeluar'], 'required'],
            [['tgl_buktikeluar', 'created_date', 'deleted_date', "returbayarpelayanan_id"], 'safe'],
            [['jml_pembayaran', 'jml_pembulatan', 'biaya_administrasi', 'biaya_materai', 'uang_diterima', 'uang_dikembalikan'], 'number'],
            [['is_tunai', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_pembayaran', 'additional_data'], 'string'],
            [['no_buktikeluar'], 'string', 'max' => 50],
            [['namapenerima'], 'string', 'max' => 100],
            [['namapemilik_rek', 'no_rek'], 'string', 'max' => 255],
            [['no_buktikeluar'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tandabuktikeluar_id' => 'Tandabuktikeluar ID',
            'returbayarpelayanan_id' => 'Returbayarpelayanan ID',
            'pembatalanuangmuka_id' => 'Pembatalanuangmuka ID',
            'shift_id' => 'Shift ID',
            'pengembalianuangmuka_id' => 'Pengembalianuangmuka ID',
            'ruangan_id' => 'Ruangan ID',
            'bank_id' => 'Bank ID',
            'pegawai1_id' => 'Pegawai1 ID',
            'pegawai2_id' => 'Pegawai2 ID',
            'tgl_buktikeluar' => 'Tgl Buktikeluar',
            'no_buktikeluar' => 'No Buktikeluar',
            'jml_pembayaran' => 'Jml Pembayaran',
            'jml_pembulatan' => 'Jml Pembulatan',
            'biaya_administrasi' => 'Biaya Administrasi',
            'biaya_materai' => 'Biaya Materai',
            'uang_diterima' => 'Uang Diterima',
            'uang_dikembalikan' => 'Uang Dikembalikan',
            'is_tunai' => 'Is Tunai',
            'namapenerima' => 'Namapenerima',
            'namapemilik_rek' => 'Namapemilik Rek',
            'no_rek' => 'No Rek',
            'keterangan_pembayaran' => 'Keterangan Pembayaran',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
