<?php

use yii\db\Migration;

/**
 * Class m190401_071251_infopasiensudahbayar_v
 */
class m190401_071251_infopasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infopasiensudahbayar_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infopasiensudahbayar_v AS 
             SELECT pendaftaran.pendaftaran_id,
                pendaftaran.pembayaranpelayanan_id,
                pendaftaran.tgl_pembayaran,
                pendaftaran.no_pembayaran,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_id
                        ELSE ruang_ri.ruangan_id
                    END AS ruangan_id,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_nama
                        ELSE ruang_ri.ruangan_nama
                    END AS ruangan_nama,
                pendaftaran.no_pendaftaran,
                pendaftaran.tgl_pendaftaran,
                pendaftaran.no_rekam_medik,
                pendaftaran.nama_pasien,
                pendaftaran.tanggal_lahir,
                pendaftaran.umur,
                pendaftaran.jeniskelamin,
                pendaftaran.penjamin_id,
                pendaftaran.penjamin_nama,
                pendaftaran.carabayar_id,
                pendaftaran.carabayar_nama,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
                        ELSE pasienadmisi_t.carabayar_id
                    END AS carabayar_id1,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_nama
                        ELSE carabayar_ri.carabayar_nama
                    END AS carabayar_nama1,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
                        ELSE pasienadmisi_t.penjamin_id
                    END AS penjamin_id1,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_nama
                        ELSE penjamin_ri.penjamin_nama
                    END AS penjamin_nama1,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_id
                        ELSE ruang_ri.instalasi_id
                    END AS instalasi_id1,
                    CASE
                        WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_nama
                        ELSE ins_ri.instalasi_nama
                    END AS instalasi_nama,
                pendaftaran.status_bayar,
                pendaftaran.closingkasir_id,
                pendaftaran.total_tagihan,
                pendaftaran.total_uang_muka,
                pendaftaran.subsidi_asuransi,
                pendaftaran.total_sudah_dibayarkan,
                pendaftaran.total_sisa_tagihan,
                pendaftaran.biaya_administrasi,
                pendaftaran.pembulatan,
                pendaftaran.jeniskasuspenyakit_nama,
                pendaftaran.pegawai_rd_rj,
                pendaftaran.kelaspelayanan_nama
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasienadmisi_id,
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
                        pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
                        pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                        pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
                        pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
                        pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.pembulatan,
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
                         LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
                         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                      WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false) pendaftaran
                 LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
                 LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
                 LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
                 LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id;
        ');
        
        $this->execute('
            ALTER TABLE infopasiensudahbayar_v
              OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_071251_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_071251_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
