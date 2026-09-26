<?php

use yii\db\Migration;

/**
 * Class m210112_060050_migrate_20210112_infotagihanpasien_v
 */
class m210112_060050_migrate_20210112_infotagihanpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infotagihanpasien_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasien_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan::integer AS tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto::integer AS tarif_cyto,
    tagihan.sub_total::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.penjualanresep_id,
    tagihan.is_valid,
    tagihan.is_cyto,
    tagihan.pasienadmisi_id,
    tagihan.implementasi_id,
    tagihan.jeniskasuspenyakit_id,
    tagihan.discount,
    tagihan.tipepaket_id,
    tagihan.kamarruangan_id,
    tagihan.is_akomodasi
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.discount_tindakan AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.discount_tindakan AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.det
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi
           FROM pendaftaran_t
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id
  WHERE tagihan.tindakansudahbayar_id IS NULL AND tagihan.is_deleted = false
  ORDER BY tagihan.tgl_pelayanan DESC;");

        $this->execute('ALTER TABLE "public"."infotagihanpasien_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_060050_migrate_20210112_infotagihanpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210112_060050_migrate_20210112_infotagihanpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
