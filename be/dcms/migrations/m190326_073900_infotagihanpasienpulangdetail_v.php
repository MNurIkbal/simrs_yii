<?php

use yii\db\Migration;

/**
 * Class m190326_073900_infotagihanpasienpulangdetail_v
 */
class m190326_073900_infotagihanpasienpulangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP VIEW infotagihanpasienpulangdetail_v;
        ");
        $this->execute("
            CREATE OR REPLACE VIEW infotagihanpasienpulangdetail_v AS 
             SELECT tagihan.pendaftaran_id,
                tagihan.pasien_id,
                tagihan.tgl_pendaftaran,
                tagihan.no_pendaftaran,
                tagihan.pasienadmisi_id,
                tagihan.tindakan_obat_id,
                tagihan.tindakan_obat_nama,
                tagihan.is_obat,
                tagihan.tarif_satuan,
                tagihan.qty,
                tagihan.sub_total,
                tagihan.ruangan_id,
                tagihan.tgl_pelayanan,
                tagihan.kelaspelayanan_id,
                tagihan.carabayar_pelayanan_id,
                tagihan.carabayar_pelayanan,
                tagihan.penjamin_pelayanan_id,
                tagihan.penjamin_pelayanan,
                tagihan.carabayar_pendaftaran_id,
                tagihan.penjamin_pendaftaran_id,
                tagihan.pasienpulang_id,
                tagihan.kelompoktindakan_id,
                kelompoktindakan_m.kelompoktindakan_nama,
                carabayar_m.carabayar_nama AS carabayar_pendaftaran,
                penjamin_m.penjamin_nama AS penjamin_pendaftaran,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.instalasi_nama AS instalasi_pelayanan,
                tagihan.ruangan_nama AS ruangan_pelayanan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.no_mobile_pasien,
                pasienpulang_t.tglpasienpulang,
                bayaruangmuka_t.jumlah_uangmuka,
                tagihan.pelayanan_id,
                tagihan.cyto_tindakan,
                tagihan.tarifcyto_tindakan
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        pendaftaran_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        pendaftaran_t.pasienpulang_id,
                        daftartindakan_m.kelompoktindakan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pendaftaran_t
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                         JOIN carabayar_m carabayar_m_1 ON tindakanpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        pendaftaran_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        pendaftaran_t.pasienpulang_id,
                        NULL::integer AS kelompoktindakan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pendaftaran_t
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                         JOIN carabayar_m carabayar_m_1 ON tindakanpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        obatalkespasien_t.obatalkes_id,
                        obatalkes_m.obatalkes_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa,
                        obatalkespasien_t.qty_oa,
                        obatalkespasien_t.hargajual_oa,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan,
                        pendaftaran_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        pendaftaran_t.pasienpulang_id,
                        NULL::integer AS var,
                        obatalkespasien_t.obatsudahbayar_id,
                        obatalkespasien_t.obatalkespasien_id,
                        false AS cyto_tindakan,
                        0 AS tarifcyto_tindakan,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pendaftaran_t
                         JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                         JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN carabayar_m carabayar_m_1 ON obatalkespasien_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN penjamin_m penjamin_m_1 ON obatalkespasien_t.penjamin_id = penjamin_m_1.penjamin_id
                         JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) tagihan
                 LEFT JOIN pasienadmisi_t ON tagihan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
                 LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN carabayar_m ON tagihan.carabayar_pendaftaran_id = carabayar_m.carabayar_id
                 LEFT JOIN penjamin_m ON tagihan.penjamin_pendaftaran_id = penjamin_m.penjamin_id
                 LEFT JOIN pasienpulang_t ON tagihan.pasienpulang_id = pasienpulang_t.pasienpulang_id
                 LEFT JOIN kelompoktindakan_m ON tagihan.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                 LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                        sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                       FROM bayaruangmuka_t bayaruangmuka_t_1
                      WHERE bayaruangmuka_t_1.is_deleted = false
                      GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON tagihan.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
              WHERE tagihan.tindakansudahbayar_id IS NULL;
        ");
        $this->execute("
            ALTER TABLE infotagihanpasienpulangdetail_v
              OWNER TO postgres;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_073900_infotagihanpasienpulangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_073900_infotagihanpasienpulangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
