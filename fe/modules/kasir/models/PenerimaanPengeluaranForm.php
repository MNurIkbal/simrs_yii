<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\models;

use Yii;
use app\components\DocoBaseModel;
use DateTime;

class PenerimaanPengeluaranForm extends DocoBaseModel
{
    protected $xssProtected = [
        'tipe',
        'jumlah',
        'deskripsi',
        'referensi'
    ];

    public $jenis_transaksi;
    public $pemasukan;
    public $pengeluaran;
    public $tanggal_transaksi;
    public $tipe;
    public $dari_kepada;
    public $metode;
    public $tunai;
    public $nontunai;
    public $jumlah;
    public $kategori;
    public $deskripsi;
    public $referensi;
    public $jenisnontunai_id;
    public $id_pendaftaran;

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

    public function rules()
    {
        return [
            [[
                'jenis_transaksi',
                'tanggal_transaksi',
                'tipe',
                'dari_kepada',
                'metode',
                'jumlah',
                'deskripsi',
                'kategori'
            ], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['jenisnontunai_id'], 'integer'],
            [['metode'], 'checkJenis'],
            [[
                'jenis_transaksi',
                'pemasukan',
                'pengeluaran',
                'tanggal_transaksi',
                'tipe',
                'dari_kepada',
                'id_pendaftaran',
                'metode',
                'tunai',
                'nontunai',
                'jumlah',
                'kategori',
                'deskripsi',
                'referensi'], 'safe'],
            [['jumlah'], 'integer'],
            ['tanggal_transaksi', 'validateTanggalTransaksi'],
        ];
    }

    public function checkJenis($attribute, $params)
    {
        if ($this->metode == 28) {
            if (empty($this->jenisnontunai_id)) {
                $this->addError('jenisnontunai_id', 'Jenis Non Tunai Tidak boleh kosong');
            }
        }
    }

    public function validateTanggalTransaksi($attribute, $params)
    {
        $inputDate = new DateTime($this->{$attribute});
        $currentDate = new DateTime();
        $inputDate->setTime(0, 0, 0);
        $currentDate->setTime(0, 0, 0);

        if ($inputDate > $currentDate) {
            $this->addError($attribute, 'Tanggal transaksi tidak boleh melebihi tanggal hari ini.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenis_transaksi' => 'Jenis Transaksi',
            'tanggal_transaksi' => 'Tanggal Transaksi',
            'tipe' => 'Tipe',
            'dari_kepada' => 'Dari/Kepada',
            'id_pendaftaran' => 'No Pendaftaran',
            'metode' => 'Metode Pembayaran',
            'jumlah' => 'Jumlah(Rp)',
            'kategori' => 'Kategori Transaksi',
            'deskripsi' => 'Deskripsi',
            'referensi' => 'Referensi',
            'jenisnontunai_id' => 'Jenis Non Tunai',
        ];
    }
}