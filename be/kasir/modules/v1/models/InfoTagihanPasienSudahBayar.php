<?php

namespace app\modules\v1\models;

use Yii;

class InfoTagihanPasienSudahBayar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpasiensudahbayar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id', 'pelayanan_id', 'dokterpenanggungjawab_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'tipepaket_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id', 'pelayanan_id', 'dokterpenanggungjawab_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'tipepaket_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pelayanan', 'tglpasienpulang'], 'safe'],
            [['tindakan_obat_nama'], 'string'],
            [['is_obat'], 'boolean'],
            [['tarif_satuan', 'qty', 'sub_total', 'tarifcyto_tindakan'], 'number'],
            [['no_pendaftaran', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['carabayar_pelayanan', 'penjamin_pelayanan', 'kelompoktindakan_nama', 'carabayar_pendaftaran', 'penjamin_pendaftaran', 'kelaspelayanan_nama', 'instalasi_pelayanan', 'ruangan_pelayanan', 'nama_pasien', 'dokterpenanggungjawab_nama', 'dokterdelegasi_nama', 'perawat1_nama', 'perawat2_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tindakan_obat_id' => 'Tindakan Obat ID',
            'tindakan_obat_nama' => 'Tindakan Obat Nama',
            'is_obat' => 'Is Obat',
            'tarif_satuan' => 'Tarif Satuan',
            'qty' => 'Qty',
            'sub_total' => 'Sub Total',
            'ruangan_id' => 'Ruangan ID',
            'tgl_pelayanan' => 'Tgl Pelayanan',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_pelayanan_id' => 'Carabayar Pelayanan ID',
            'carabayar_pelayanan' => 'Carabayar Pelayanan',
            'penjamin_pelayanan_id' => 'Penjamin Pelayanan ID',
            'penjamin_pelayanan' => 'Penjamin Pelayanan',
            'carabayar_pendaftaran_id' => 'Carabayar Pendaftaran ID',
            'penjamin_pendaftaran_id' => 'Penjamin Pendaftaran ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'carabayar_pendaftaran' => 'Carabayar Pendaftaran',
            'penjamin_pendaftaran' => 'Penjamin Pendaftaran',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'instalasi_pelayanan' => 'Instalasi Pelayanan',
            'ruangan_pelayanan' => 'Ruangan Pelayanan',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'tglpasienpulang' => 'Tglpasienpulang',
            'pelayanan_id' => 'Pelayanan ID',
            'dokterpenanggungjawab_id' => 'Dokterpenanggungjawab ID',
            'dokterpenanggungjawab_nama' => 'Dokterpenanggungjawab Nama',
            'dokterdelegasi_id' => 'Dokterdelegasi ID',
            'dokterdelegasi_nama' => 'Dokterdelegasi Nama',
            'perawat1_id' => 'Perawat1 ID',
            'perawat1_nama' => 'Perawat1 Nama',
            'perawat2_id' => 'Perawat2 ID',
            'perawat2_nama' => 'Perawat2 Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'instalasi_id' => 'Instalasi ID',
            'tipepaket_id' => 'Tipepaket ID',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
        ];
    }
}
