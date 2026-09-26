<?php

use yii\db\Migration;

/**
 * Class m190527_031155_sync_collection_de_update
 */
class m190527_031155_sync_collection_de_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
    DROP VIEW sync_collection_de;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW sync_collection_de AS 
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN \'Cash\'::text
            ELSE \'Bank\'::text
        END AS paymentmethod_name,
    \'\'::character varying(10) AS bank_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pendaftaran_t.pendaftaran_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(pembayaranpelayanan_t.no_pembayaran, \'-\', pasien_m.nama_pasien) AS notes,
    tandabuktibayar_t.jmlpembayaran AS total_pelayanan,
    pembayaranpelayanan_t.biaya_administrasi,
    pembayaranpelayanan_t.pembulatan,
    0::numeric(15,0) AS kembalian
   FROM pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE NOT (pembayaranpelayanan_t.pembayaranpelayanan_id IN ( SELECT COALESCE(syncakuntansi_r.pembayaranpelayanan_id, 0) AS pembayaranpelayanan_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
UNION ALL
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN \'Cash\'::text
            ELSE \'Bank\'::text
        END AS paymentmethod_name,
    \'\'::character varying(10) AS bank_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    COALESCE(pendaftaran_t.pendaftaran_id, penjualanresep_t.penjualanresep_id) AS pendaftaran_id,
    COALESCE(pasien_m.nama_pasien, pegawai_m.nama_pegawai) AS nama_pasien,
    COALESCE(pasien_m.no_rekam_medik, pegawai_m.nomorindukpegawai) AS no_rekam_medik,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(pembayaranpelayanan_t.no_pembayaran, \'-\', COALESCE(pasien_m.nama_pasien, pegawai_m.nama_pegawai)) AS notes,
    tandabuktibayar_t.jmlpembayaran AS total_pelayanan,
    pembayaranpelayanan_t.biaya_administrasi,
    pembayaranpelayanan_t.pembulatan,
    0::numeric(15,0) AS kembalian
   FROM pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
  WHERE NOT (pembayaranpelayanan_t.pembayaranpelayanan_id IN ( SELECT COALESCE(syncakuntansi_r.pembayaranpelayanan_id, 0) AS pembayaranpelayanan_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE));

        ');

        $this->execute('
ALTER TABLE sync_collection_de
  OWNER TO dev;

        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_031155_sync_collection_de_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190527_031155_sync_collection_de_update cannot be reverted.\n";

        return false;
    }
    */
}
