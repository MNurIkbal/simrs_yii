<?php

use yii\db\Migration;

/**
 * Class m190508_030934_laporanpasiensudahbayar_v_update
 */
class m190508_030934_laporanpasiensudahbayar_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW laporanpasiensudahbayar_v;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW laporanpasiensudahbayar_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    jk.lookup_name AS jeniskelamin,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    status_bayar.lookup_name AS status_bayar,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.closingkasir_id,
    pembayaranpelayanan_t.total_biayapelayanan::integer AS total_tagihan,
    pembayaranpelayanan_t.penggunaan_uangmuka::integer AS total_uang_muka,
    pembayaranpelayanan_t.total_subsidiasuransi::integer AS subsidi_asuransi,
    pembayaranpelayanan_t.total_bayartindakan::integer AS total_sudah_dibayarkan,
    pembayaranpelayanan_t.total_sisatagihan::integer AS total_sisa_tagihan,
    pembayaranpelayanan_t.biaya_administrasi::integer AS biaya_administrasi,
    pembayaranpelayanan_t.pembulatan::integer AS pembulatan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
    kelaspelayanan_m.kelaspelayanan_nama
   FROM pembayaranpelayanan_t
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN lookup_m status_bayar ON pembayaranpelayanan_t.statusbayar::integer = status_bayar.lookup_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false;

        ');

        $this->execute('
 ALTER TABLE laporanpasiensudahbayar_v
  OWNER TO postgres;
        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190508_030934_laporanpasiensudahbayar_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_030934_laporanpasiensudahbayar_v_update cannot be reverted.\n";

        return false;
    }
    */
}
