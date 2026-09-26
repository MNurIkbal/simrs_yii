<?php

use yii\db\Migration;

/**
 * Class m221129_034611_migrate_skema_view_infogabungtagihandetail_v
 */
class m221129_034611_migrate_skema_view_infogabungtagihandetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infogabungtagihandetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infogabungtagihandetail_v" AS  SELECT \'pendaftaran_utama\'::text AS tipe,
    gabungpelayanandetail_t.pendaftaran_id,
    NULL::integer AS ref_pendaftaran_id,
    pendaftaran_t.no_pendaftaran, 
    pendaftaran_t.tgl_pendaftaran,
    concat(layanan.instalasi_nama, \' - \', layanan.ruangan_nama) AS instalasi_ruangan,
    layanan.penjamin_nama AS penjamin,
    layanan.tgl_transaksi,
    layanan.tindakan_obat,
    layanan.qty,
    layanan.harga,
    COALESCE(layanan.cyto, 0::double precision) AS cyto,
    COALESCE(layanan.diskon, 0::double precision) AS diskon,
    layanan.sub_total,
        CASE
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id = 417 THEN 0::double precision
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id <> 417 THEN layanan.sub_total
            WHEN layanan.sudahbayar_id IS NOT NULL THEN layanan.tarif_dijamin
            ELSE 0::double precision
        END AS tarif_dijamin,
        CASE
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id = 417 THEN layanan.sub_total
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id <> 417 THEN 0::double precision
            WHEN layanan.sudahbayar_id IS NOT NULL THEN layanan.tarif_dibayarkan
            ELSE 0::double precision
        END AS tarif_dibayarkan,
    layanan.sudahbayar_id,
    carabayar_m.groupcarabayar_id
   FROM pendaftaran_t
     JOIN ( SELECT a.pendaftaran_id
           FROM gabungpelayanandetail_t a
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id) gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.pendaftaran_id
     JOIN ( SELECT tindakan.pendaftaran_id,
            tindakan.tgl_tindakan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
            tindakan.qty_tindakan AS qty,
            tindakan.tarif_satuan AS harga,
            tindakan.tarifcyto_tindakan AS cyto,
            tindakan.tarif_diskon AS diskon,
            tindakan.tarif_tindakan AS sub_total,
            tindakan.tarif_dijamin,
            tindakan.tarif_dibayarkan,
            tindakan.tindakansudahbayar_id AS sudahbayar_id
           FROM tindakanpelayanan_t tindakan
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON tindakan.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON tindakan.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON tindakan.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE tindakan.is_deleted = false AND tindakan.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT paket.pendaftaran_id,
            paket.tgl_tindakan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            tipepaket_m.tipepaket_nama AS tindakan_obat,
            paket.qty_tindakan AS qty,
            paket.tarif_satuan AS harga,
            paket.tarifcyto_tindakan AS cyto,
            paket.tarif_diskon AS diskon,
            paket.tarif_tindakan AS sub_total,
            paket.tarif_dijamin,
            paket.tarif_dibayarkan,
            paket.tindakansudahbayar_id AS sudahbayar_id
           FROM tindakanpelayanan_t paket
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON paket.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON paket.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama
                   FROM tipepaket_m a) tipepaket_m ON paket.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE paket.is_deleted = false AND paket.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT obat.pendaftaran_id,
            obat.tglpelayanan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
                CASE
                    WHEN obat.det = 0::double precision OR obat.det IS NULL THEN obat.qty_oa
                    ELSE obat.det
                END AS qty,
            obat.hargasatuan_oa AS harga,
            obat.tarifcyto AS cyto,
            obat.tarif_diskon AS diskon,
            obat.hargajual_oa AS sub_total,
            obat.tarif_dijamin,
            obat.tarif_dibayarkan,
            obat.obatsudahbayar_id AS sudahbayar_id
           FROM obatalkespasien_t obat
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON obat.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON obat.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obat.obatalkes_id = obatalkes_m.obatalkes_id
          WHERE obat.is_deleted = false AND obat.obatsudahbayar_id IS NULL) layanan ON gabungpelayanandetail_t.pendaftaran_id = layanan.pendaftaran_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON layanan.carabayar_id = carabayar_m.carabayar_id
UNION ALL
 SELECT \'ref_pendaftaran\'::text AS tipe,
    gabungpelayanandetail_t.pendaftaran_id,
    gabungpelayanandetail_t.ref_pendaftaran_id,
    ref_pendaftaran.no_pendaftaran,
    ref_pendaftaran.tgl_pendaftaran,
    concat(layanan.instalasi_nama, \' - \', layanan.ruangan_nama) AS instalasi_ruangan,
    layanan.penjamin_nama AS penjamin,
    layanan.tgl_transaksi,
    layanan.tindakan_obat,
    layanan.qty,
    layanan.harga,
    COALESCE(layanan.cyto, 0::double precision) AS cyto,
    COALESCE(layanan.diskon, 0::double precision) AS diskon,
    layanan.sub_total,
        CASE
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id = 417 THEN 0::double precision
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id <> 417 THEN layanan.sub_total
            WHEN layanan.sudahbayar_id IS NOT NULL THEN layanan.tarif_dijamin
            ELSE 0::double precision
        END AS tarif_dijamin,
        CASE
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id = 417 THEN layanan.sub_total
            WHEN layanan.sudahbayar_id IS NULL AND carabayar_m.groupcarabayar_id <> 417 THEN 0::double precision
            WHEN layanan.sudahbayar_id IS NOT NULL THEN layanan.tarif_dibayarkan
            ELSE 0::double precision
        END AS tarif_dibayarkan,
    layanan.sudahbayar_id,
    carabayar_m.groupcarabayar_id
   FROM gabungpelayanandetail_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran
           FROM pendaftaran_t a) ref_pendaftaran ON gabungpelayanandetail_t.ref_pendaftaran_id = ref_pendaftaran.pendaftaran_id
     JOIN ( SELECT tindakan.pendaftaran_id,
            tindakan.tgl_tindakan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
            tindakan.qty_tindakan AS qty,
            tindakan.tarif_satuan AS harga,
            tindakan.tarifcyto_tindakan AS cyto,
            tindakan.tarif_diskon AS diskon,
            tindakan.tarif_tindakan AS sub_total,
            tindakan.tarif_dijamin,
            tindakan.tarif_dibayarkan,
            tindakan.tindakansudahbayar_id AS sudahbayar_id
           FROM tindakanpelayanan_t tindakan
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON tindakan.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON tindakan.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON tindakan.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE tindakan.is_deleted = false AND tindakan.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT paket.pendaftaran_id,
            paket.tgl_tindakan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            tipepaket_m.tipepaket_nama AS tindakan_obat,
            paket.qty_tindakan AS qty,
            paket.tarif_satuan AS harga,
            paket.tarifcyto_tindakan AS cyto,
            paket.tarif_diskon AS diskon,
            paket.tarif_tindakan AS sub_total,
            paket.tarif_dijamin,
            paket.tarif_dibayarkan,
            paket.tindakansudahbayar_id AS sudahbayar_id
           FROM tindakanpelayanan_t paket
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON paket.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON paket.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama
                   FROM tipepaket_m a) tipepaket_m ON paket.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE paket.is_deleted = false AND paket.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT obat.pendaftaran_id,
            obat.tglpelayanan AS tgl_transaksi,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            penjamin_m.carabayar_id,
            penjamin_m.penjamin_nama,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
                CASE
                    WHEN obat.det = 0::double precision OR obat.det IS NULL THEN obat.qty_oa
                    ELSE obat.det
                END AS qty,
            obat.hargasatuan_oa AS harga,
            obat.tarifcyto AS cyto,
            obat.tarif_diskon AS diskon,
            obat.hargajual_oa AS sub_total,
            obat.tarif_dijamin,
            obat.tarif_dibayarkan,
            obat.obatsudahbayar_id AS sudahbayar_id
           FROM obatalkespasien_t obat
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON obat.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON obat.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obat.obatalkes_id = obatalkes_m.obatalkes_id
          WHERE obat.is_deleted = false AND obat.obatsudahbayar_id IS NULL) layanan ON gabungpelayanandetail_t.ref_pendaftaran_id = layanan.pendaftaran_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON layanan.carabayar_id = carabayar_m.carabayar_id
  WHERE gabungpelayanandetail_t.is_deleted IS FALSE;
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_034611_migrate_skema_view_infogabungtagihandetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034611_migrate_skema_view_infogabungtagihandetail_v cannot be reverted.\n";

        return false;
    }
    */
}
