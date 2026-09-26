<?php

use yii\db\Migration;

/**
 * Class m190423_061416_laporanpendapatanruangan_v_update
 */
class m190423_061416_laporanpendapatanruangan_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE VIEW public.laporanpendapatanruangan_v AS  SELECT gabung.pendaftaran_id,
            gabung.tgl_pendaftaran,
            gabung.no_pendaftaran,
            gabung.no_rekam_medik,
            gabung.nama_pasien,
            gabung.carabayar_nama,
            gabung.penjamin_nama, 
            gabung.nama_pegawai,
            gabung.kelaspelayanan_nama,
            sum(gabung.jasa_rumahsakit_tarif) AS jasa_rumahsakit_tarif,
            sum(gabung.jasa_rumahsakit_cyto) AS jasa_rumahsakit_cyto,
            sum(gabung.jasa_rumahsakit) AS jasa_rumahsakit,
            sum(gabung.jasa_layanan_tarif) AS jasa_layanan_tarif,
            sum(gabung.jasa_layanan_cyto) AS jasa_layanan_cyto,
            sum(gabung.jasa_layanan) AS jasa_layanan,
            (sum(gabung.jasa_rumahsakit) + sum(gabung.jasa_layanan)) AS total,
            gabung.instalasi_id,
            gabung.ruangan_id,
            gabung.ruangan_nama,
            gabung.carabayar_id,
            gabung.penjamin_id
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_nama,
                    pegawai_m.nama_pegawai,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id = 1) THEN tindakankomponen_t.tarif_tindakankomp
                            ELSE (0)::double precision
                        END) AS jasa_rumahsakit_tarif,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id = 1) THEN tindakankomponen_t.tarifcyto_tindakankomp
                            ELSE (0)::double precision
                        END) AS jasa_rumahsakit_cyto,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id = 1) THEN (tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp)
                            ELSE (0)::double precision
                        END) AS jasa_rumahsakit,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id <> 1) THEN tindakankomponen_t.tarif_tindakankomp
                            ELSE (0)::double precision
                        END) AS jasa_layanan_tarif,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id <> 1) THEN tindakankomponen_t.tarifcyto_tindakankomp
                            ELSE (0)::double precision
                        END) AS jasa_layanan_cyto,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id <> 1) THEN (tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp)
                            ELSE (0)::double precision
                        END) AS jasa_layanan,
                    sum(
                        CASE
                            WHEN (tindakankomponen_t.komponentarif_id IS NOT NULL) THEN tindakankomponen_t.tarif_tindakankomp
                            ELSE (0)::double precision
                        END) AS total,
                    pendaftaran_t.instalasi_id,
                    tindakanpelayanan_t.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.carabayar_id,
                    pendaftaran_t.penjamin_id
                   FROM ((((((((tindakankomponen_t
                     JOIN tindakanpelayanan_t ON ((tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
                     JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                     JOIN carabayar_m ON ((tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
                     JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                  WHERE (((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true)) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL))
                  GROUP BY ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.pendaftaran_id, pasien_m.pasien_id, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.instalasi_id, tindakanpelayanan_t.ruangan_id
                UNION ALL
                 SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_nama,
                    pegawai_m.nama_pegawai,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    0 AS jasa_rumahsakit_tarif,
                    0 AS jasa_rumahsakit_cyto,
                    0 AS jasa_rumahsakit,
                    sum(COALESCE(obatalkespasien_t.hargajual_oa, (0)::double precision)) AS jasa_layanan_tarif,
                    0 AS jasa_layanan_cyto,
                    sum(COALESCE(obatalkespasien_t.hargajual_oa, (0)::double precision)) AS jasa_layanan,
                    sum(COALESCE(obatalkespasien_t.hargajual_oa, (0)::double precision)) AS total,
                    pendaftaran_t.instalasi_id,
                    obatalkespasien_t.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.carabayar_id,
                    pendaftaran_t.penjamin_id
                   FROM (((((((obatalkespasien_t
                     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                     JOIN carabayar_m ON ((obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id)))
                     JOIN penjamin_m ON ((obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id)))
                     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                     JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
                  WHERE (((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true)) AND (obatalkespasien_t.obatsudahbayar_id IS NOT NULL))
                  GROUP BY ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.pendaftaran_id, pasien_m.pasien_id, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.instalasi_id, obatalkespasien_t.ruangan_id) gabung
          GROUP BY gabung.pendaftaran_id, gabung.tgl_pendaftaran, gabung.no_pendaftaran, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_nama, gabung.penjamin_nama, gabung.nama_pegawai, gabung.kelaspelayanan_nama, gabung.instalasi_id, gabung.ruangan_id, gabung.ruangan_nama, gabung.carabayar_id, gabung.penjamin_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190423_061416_laporanpendapatanruangan_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_061416_laporanpendapatanruangan_v_update cannot be reverted.\n";

        return false;
    }
    */
}
