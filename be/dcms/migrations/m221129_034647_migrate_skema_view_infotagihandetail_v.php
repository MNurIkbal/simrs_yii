<?php

use yii\db\Migration;

/**
 * Class m221129_034647_migrate_skema_view_infotagihandetail_v
 */
class m221129_034647_migrate_skema_view_infotagihandetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infotagihandetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihandetail_v" AS  SELECT tagihan.ref_pendaftaran_id,
    tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id, 
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    tagihan.sub_total,
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
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_cyto
   FROM ( SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
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
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_tindakan,
                    a.tindakanpelayanan_id,
                    a.tindakansudahbayar_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
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
            NULL::integer AS kelompoktindakan_id,
            \'kelompok paket\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_tindakan,
                    a.tindakanpelayanan_id,
                    a.tindakansudahbayar_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted,
                    a.tipepaket_id
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama
                   FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            \'kelompok obat\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
                CASE COALESCE(obatalkespasien_t.tarifcyto, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tglpelayanan,
                    a.obatalkespasien_id,
                    a.obatsudahbayar_id,
                    a.obatalkes_id,
                    a.hargasatuan_oa,
                    a.qty_oa,
                    a.tarifcyto,
                    a.hargajual_oa,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted
                   FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
          WHERE obatalkespasien_t.is_deleted IS FALSE
        UNION ALL
         SELECT gabungtagihan.ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
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
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_tindakan,
                    a.tindakanpelayanan_id,
                    a.tindakansudahbayar_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted IS FALSE) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT gabungtagihan.ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
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
            NULL::integer AS kelompoktindakan_id,
            \'kelompok paket\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_tindakan,
                    a.tindakanpelayanan_id,
                    a.tindakansudahbayar_id,
                    a.daftartindakan_id,
                    a.tarif_satuan,
                    a.qty_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.dokterpenanggungjawab_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted,
                    a.tipepaket_id
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama
                   FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted IS FALSE) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT gabungtagihan.ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            \'kelompok obat\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
                CASE COALESCE(obatalkespasien_t.tarifcyto, 0::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tglpelayanan,
                    a.obatalkespasien_id,
                    a.obatsudahbayar_id,
                    a.obatalkes_id,
                    a.hargasatuan_oa,
                    a.qty_oa,
                    a.tarifcyto,
                    a.hargajual_oa,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.pasienmasukpenunjang_id,
                    a.is_deleted
                   FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted IS FALSE) gabungtagihan ON obatalkespasien_t.pendaftaran_id = gabungtagihan.pendaftaran_id
          WHERE obatalkespasien_t.is_deleted IS FALSE) tagihan
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON tagihan.carabayar_pelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_034647_migrate_skema_view_infotagihandetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034647_migrate_skema_view_infotagihandetail_v cannot be reverted.\n";

        return false;
    }
    */
}
