<?php

use yii\db\Migration;

/**
 * Class m220411_024136_migrate_ODH530_view_infotindakanpenatajasa_v
 */
class m220411_024136_migrate_ODH530_view_infotindakanpenatajasa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infotindakanpenatajasa_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotindakanpenatajasa_v" AS  
            SELECT \'tindakan\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tgl_tindakan,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama, 
                tindakanpelayanan_t.ruangan_id,
                ruangan_m.ruangan_nama,
                tindakanpelayanan_t.dokterpenanggungjawab_id,
                pegawai_m.nama_pegawai AS nama_dokter,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.qty_tindakan,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.is_penatajasa,
                    CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_bayar,
                tindakanpelayanan_t.is_deleted,
                tindakanpelayanan_t.keterangantindakan,
                NULL::integer AS obatalkespasien_id,
                NULL::integer AS obatalkes_id,
                NULL::character varying AS obatalkes_nama,
                NULL::double precision AS qty_oa,
                NULL::double precision AS hargajual_oa,
                NULL::integer AS satuanobat_id,
                NULL::character varying AS satuanobat_nama,
                tindakanpelayanan_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.no_masukpenunjang,
                tindakanpelayanan_t.tarifpenyulit_tindakan,
                kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
                daftartindakan_m.is_akomodasi,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                (replace(((((tindakanpelayanan_t.additional_data)::json ->> \'detail_akomodasi\'::text))::json ->> \'persentase\'::text), \'%\'::text, \'\'::text))::integer AS qty_akomodasi,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.kamarruangan_id,
                tindakanpelayanan_t.kamartempattidur_id,
                tindakanpelayanan_t.perawat1_id AS perawat_id,
                perawat.nama_pegawai AS perawat_nama,
                (tindakanpelayanan_t.tgl_tindakan)::date AS tgl_pelayanan
               FROM ((((((((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                 LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
                        count(*) AS telahbayar
                       FROM tindakansudahbayar_t
                      WHERE (tindakansudahbayar_t.is_deleted = false)
                      GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
                 LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
                 LEFT JOIN kamarruangan_m ON ((kamarruangan_m.kamarruangan_id = tindakanpelayanan_t.kamarruangan_id)))
                 LEFT JOIN kamartempattidur_m ON ((kamartempattidur_m.kamartempattidur_id = tindakanpelayanan_t.kamartempattidur_id)))
                 LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                        pegawai_m_1.nama_pegawai
                       FROM pegawai_m pegawai_m_1) perawat ON ((tindakanpelayanan_t.perawat1_id = perawat.pegawai_id)))
              WHERE (tindakanpelayanan_t.is_deleted = false)
            UNION ALL
             SELECT \'paket\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tgl_tindakan,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                tindakanpelayanan_t.ruangan_id,
                ruangan_m.ruangan_nama,
                tindakanpelayanan_t.dokterpenanggungjawab_id,
                pegawai_m.nama_pegawai AS nama_dokter,
                tipepaket_m.tipepaket_id AS daftartindakan_id,
                tipepaket_m.tipepaket_nama AS daftartindakan_nama,
                tindakanpelayanan_t.qty_tindakan,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.is_penatajasa,
                    CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_bayar,
                tindakanpelayanan_t.is_deleted,
                tindakanpelayanan_t.keterangantindakan,
                obatalkespasien_t.obatalkespasien_id,
                obatalkespasien_t.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                obatalkespasien_t.qty_oa,
                obatalkespasien_t.hargajual_oa,
                obatalkespasien_t.satuankecil_id AS satuanobat_id,
                satuanunit_m.satuanunit_nama AS satuanobat_nama,
                tindakanpelayanan_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.no_masukpenunjang,
                tindakanpelayanan_t.tarifpenyulit_tindakan,
                \'PAKET\'::character varying AS kelompok,
                false AS is_akomodasi,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                (replace(((((tindakanpelayanan_t.additional_data)::json ->> \'detail_akomodasi\'::text))::json ->> \'persentase\'::text), \'%\'::text, \'\'::text))::integer AS qty_akomodasi,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.kamarruangan_id,
                tindakanpelayanan_t.kamartempattidur_id,
                tindakanpelayanan_t.perawat1_id AS perawat_id,
                perawat.nama_pegawai AS perawat_nama,
                (tindakanpelayanan_t.tgl_tindakan)::date AS tgl_pelayanan
               FROM ((((((((((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
                        count(*) AS telahbayar
                       FROM tindakansudahbayar_t
                      WHERE (tindakansudahbayar_t.is_deleted = false)
                      GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
                 LEFT JOIN obatalkespasien_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id) AND (obatalkespasien_t.is_deleted = false))))
                 LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
                 LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
                 LEFT JOIN kamarruangan_m ON ((kamarruangan_m.kamarruangan_id = tindakanpelayanan_t.kamarruangan_id)))
                 LEFT JOIN kamartempattidur_m ON ((kamartempattidur_m.kamartempattidur_id = tindakanpelayanan_t.kamartempattidur_id)))
                 LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                        pegawai_m_1.nama_pegawai
                       FROM pegawai_m pegawai_m_1) perawat ON ((tindakanpelayanan_t.perawat1_id = perawat.pegawai_id)))
              WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                NULL::integer AS tindakanpelayanan_id,
                obatalkespasien_t.pendaftaran_id,
                obatalkespasien_t.tglpelayanan AS tgl_tindakan,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                obatalkespasien_t.ruangan_id,
                ruangan_m.ruangan_nama,
                obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
                pegawai_m.nama_pegawai AS nama_dokter,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                obatalkespasien_t.qty_oa AS qty_tindakan,
                obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                0 AS tarifcyto_tindakan,
                obatalkespasien_t.hargajual_oa AS tarif_tindakan,
                obatalkespasien_t.is_penatajasa,
                    CASE COALESCE(obatsudahbayar.telahbayar, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_bayar,
                obatalkespasien_t.is_deleted,
                NULL::text AS keterangantindakan,
                obatalkespasien_t.obatalkespasien_id,
                obatalkespasien_t.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                obatalkespasien_t.qty_oa,
                obatalkespasien_t.hargajual_oa,
                obatalkespasien_t.satuankecil_id AS satuanobat_id,
                satuanunit_m.satuanunit_nama AS satuanobat_nama,
                obatalkespasien_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.no_pendaftaran,
                NULL::character varying AS no_masukpenunjang,
                NULL::double precision AS tarifpenyulit_tindakan,
                \'OBAT\'::character varying AS kelompok,
                false AS is_akomodasi,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::character varying AS no_tempattidur,
                NULL::integer AS qty_akomodasi,
                obatalkespasien_t.penjamin_id,
                NULL::integer AS kamarruangan_id,
                NULL::integer AS kamartempattidur_id,
                obatalkespasien_t.perawat1_id AS perawat_id,
                perawat.nama_pegawai AS perawat_nama,
                (obatalkespasien_t.tglpelayanan)::date AS tgl_pelayanan
               FROM (((((((((((obatalkespasien_t
                 JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
                        count(*) AS telahbayar
                       FROM obatsudahbayar_t
                      WHERE (obatsudahbayar_t.is_deleted = false)
                      GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON ((obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id)))
                 LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
                 LEFT JOIN tindakanpelayanan_t ON (((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.is_deleted = false))))
                 LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN kelaspelayanan_m ON ((obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                        pegawai_m_1.nama_pegawai
                       FROM pegawai_m pegawai_m_1) perawat ON ((obatalkespasien_t.perawat1_id = perawat.pegawai_id)))
              WHERE (obatalkespasien_t.is_deleted IS FALSE);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220411_024136_migrate_ODH530_view_infotindakanpenatajasa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220411_024136_migrate_ODH530_view_infotindakanpenatajasa_v cannot be reverted.\n";

        return false;
    }
    */
}
