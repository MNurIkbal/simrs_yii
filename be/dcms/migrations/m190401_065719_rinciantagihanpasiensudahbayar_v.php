<?php

use yii\db\Migration;

/**
 * Class m190401_065719_rinciantagihanpasiensudahbayar_v
 */
class m190401_065719_rinciantagihanpasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW rinciantagihanpasiensudahbayar_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW rinciantagihanpasiensudahbayar_v AS 
             SELECT tagihan.pendaftaran_id,
                tagihan.pelayanan_id,
                tagihan.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                tagihan.umur,
                jk.lookup_name AS jeniskelamin,
                tagihan.tgl_pendaftaran,
                tagihan.no_pendaftaran,
                tagihan.tindakan_obat_id,
                tagihan.tindakan_obat_nama,
                tagihan.is_obat,
                tagihan.tarif_satuan,
                tagihan.qty,
                tagihan.sub_total,
                tagihan.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_pelayanan,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama AS instalasi_pelayanan,
                tagihan.tgl_pelayanan,
                tagihan.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.carabayar_tinpelayanan_id,
                carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
                tagihan.penjamin_tinpelayanan_id,
                penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
                tagihan.kelompoktindakan_id,
                tagihan.kelompoktindakan_nama,
                tagihan.jeniskasuspenyakit_id,
                tagihan.pembayaranpelayanan_id,
                tagihan.biaya_administrasi,
                tagihan.e_collection,
                tagihan.nama_pemrekening,
                tagihan.no_rekening,
                tagihan.carabayar_pelayanan_id,
                tagihan.carabayar_pelayanan,
                tagihan.penjamin_pelayanan_id,
                tagihan.penjamin_pelayanan,
                tagihan.tarif_cyto,
                tagihan.tandabuktibayar_id,
                tagihan.jeniskasuspenyakit_nama,
                tagihan.penjualanresep_id
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                        kelompoktindakan_m.kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id
                       FROM pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                         JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                         JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'kelompok_paket\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id
                       FROM pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                         JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                        obatalkespasien_t.qty_oa AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'kelompok_obat\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id
                       FROM pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                         JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                         JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                    UNION ALL
                     SELECT NULL::integer AS pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        penjualanresep_t.pasien_id,
                        penjualanresep_t.tglresep AS tgl_pendaftaran,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        NULL::character varying AS umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                        obatalkespasien_t.qty_oa AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'kelompok_obat\'::character varying AS kelompoktindakan_nama,
                        NULL::integer AS jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        NULL::character varying AS jeniskasuspenyakit_nama,
                        obatalkespasien_t.penjualanresep_id
                       FROM obatalkespasien_t
                         JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                         JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                         JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id) tagihan
                 LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
                 LEFT JOIN penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
                 LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
                 LEFT JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id;
        ');
        
        $this->execute('
            ALTER TABLE rinciantagihanpasiensudahbayar_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_065719_rinciantagihanpasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_065719_rinciantagihanpasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
