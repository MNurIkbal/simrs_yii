<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "terimamutasibarang_t".
 *
 * @property int $terimamutasibarang_id
 * @property int $mutasibarang_id
 * @property string $tglterima
 * @property string $noterimamutasi
 * @property int $ruanganpenerima_id
 * @property int $ruanganasalmutasi_id
 * @property int $pegawaipenerima_id
 * @property int $pegawaimengetahui_id
 * @property string $keterangan_terima
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $pegawaimenyetujui_id
 *
 * @property MutasibarangT $mutasibarang
 */
class FormMutasiBarang extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $mutasibarang_id;
    public $pesanbarang_id;
    public $pegawaipengirim_id;
    public $pegawaimengetahui_id;
    public $ruangantujuan_id;
    public $tgl_mutasibarang;
    public $nomutasi_barang;
    public $keterangan_mutasi;
    public $totalhargamutasi;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $ruanganasal_id;
    public $status_mutasi;
    public $mutasi_detail;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarang_id','pegawaipengirim_id','pegawaimengetahui_id','ruangantujuan_id','tgl_mutasibarang','nomutasi_barang','keterangan_mutasi','totalhargamutasi','ruanganasal_id','status_mutasi',"mutasi_detail"], 'safe'],
            [['keterangan_mutasi', 'status_mutasi',], 'default', 'value'=> null],
            [['pesanbarang_id','pegawaipengirim_id','pegawaimengetahui_id','ruangantujuan_id','ruanganasal_id'], 'integer'],
            [['pegawaimengetahui_id','ruangantujuan_id', 'tgl_mutasibarang', 'mutasi_detail'], 'required', "message" => "{attribute} Tidak boleh kosong !"],
            [['mutasi_detail'], 'validasiDetail']
        ];
    }

    public function validasiDetail($params, $attributes)
    {
        if (!empty($this->mutasi_detail)) {
            $data = json_decode($this->mutasi_detail,true);
            if (!is_array($data)) return $this->addError("data_valid","Format Data Salah");

            $validData = 0;

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    if ($value["qty_mutasi"] < 0) {
                        $this->addError("data_valid","Qty Kirim harus lebih dari 0");
                    }else{
                        $validData++;
                    }
                }
            }
            if ($validData == 0) {
                $this->addError("data_valid","Tidak ada barang yang bisa diproses.");
            }
        }else{
            $this->addError("data_valid","Tidak ada data yang dapat diproses");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarang_id' => Yii::t("fe", "Mutasi Barang ID"),
            'pesanbarang_id' => Yii::t("fe", "Pesan Barang ID"),
            'pegawaipengirim_id' => Yii::t("fe", "Pegawai Pengirim"),
            'pegawaimengetahui_id' => Yii::t("fe", "Pegawai Mengetahui"),
            'ruangantujuan_id' => Yii::t("fe", "Ruangan Tujuan"),
            'tgl_mutasibarang' => Yii::t("fe", "Tanggal Kirim"),
            'nomutasi_barang' => Yii::t("fe", "No. Mutasi Barang"),
            'keterangan_mutasi' => Yii::t("fe", "Keterangan Mutasi"),
            'totalhargamutasi' => Yii::t("fe", "Total Harga Mutasi"),
            'additional_data' => Yii::t("fe", "additional_data"),
            'created_date' => Yii::t("fe", "created_date"),
            'created_by' => Yii::t("fe", "created_by"),
            'modified_count' => Yii::t("fe", "modified_count"),
            'last_modified_date' => Yii::t("fe", "last_modified_date"),
            'last_modified_by' => Yii::t("fe", "last_modified_by"),
            'is_deleted' => Yii::t("fe", "is_deleted"),
            'is_active' => Yii::t("fe", "is_active"),
            'deleted_date' => Yii::t("fe", "deleted_date"),
            'deleted_by' => Yii::t("fe", "deleted_by"),
            'ruanganasal_id' => Yii::t("fe", "Ruangan Asal"),
            'status_mutasi' => Yii::t("fe", "Status Mutasi"),
            'mutasi_detail' => Yii::t("fe", "Detail Mutasi"),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getMutasibarang()
    // {
    //     return $this->hasOne(MutasibarangT::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    // }
}
