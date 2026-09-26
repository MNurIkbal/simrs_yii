<?php

use yii\db\Migration;

/**
 * Class m210125_093848_migrate_3309_20210125_view_laporanrekapmorbiditas_v
 */
class m210125_093848_migrate_3309_20210125_view_laporanrekapmorbiditas_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekapmorbiditas_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekapmorbiditas_v\" AS
           SELECT 'Normal'::text AS tipe,
           koreksidiagnosa_t.pasien_id,
           koreksidiagnosa_t.pendaftaran_id,
           koreksidiagnosa_t.tgl_koreksidiagnosa AS tglmorbiditas,
           NULL::text AS kasusdiagnosa,
           diagnosa_m.diagnosa_id,
           diagnosa_m.diagnosa_kode,
           diagnosa_m.diagnosa_nama,
           diagnosa_m.diagnosa_namalainnya,
           diagnosa_m.diagnosa_nourut,
           pendaftaran_t.golonganumur_id,
           pasien_m.jeniskelamin,
           golonganumur_m.golonganumur_nama,
           pendaftaran_t.no_pendaftaran,
           pasien_m.no_rekam_medik,
           pasien_m.nama_pasien,
           kelompokdiagnosa_m.kelompokdiagnosa_nama,
           diagnosa_m.diagnosa_katakunci,
           klasifikasidiagnosa_m.klasifikasidiagnosa_id,
           klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
           dtd_m.dtd_kode,
           dtd_m.dtd_nama,
           CASE
           WHEN (koreksidiagnosa_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
           WHEN (pendaftaran_t.instalasi_id = 2) THEN pendaftaran_t.ruangan_id
           ELSE pasienadmisi_t.ruangan_id
           END AS ruangan_id,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
           ELSE ruangan_m.ruangan_nama
           END AS ruangan_nama,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
           WHEN (pendaftaran_t.instalasi_id = 2) THEN pendaftaran_t.pegawai_id
           ELSE peg_admisi.pegawai_id
           END AS pegawai_id,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
           WHEN (pendaftaran_t.instalasi_id = 2) THEN pegawai_m.nama_pegawai
           ELSE peg_admisi.nama_pegawai
           END AS nama_pegawai,
           koreksidiagnosa_t.kelompokdiagnosa_id,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
           ELSE pendaftaran_t.instalasi_id
           END AS instalasi_id,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
           ELSE instalasi_m.instalasi_nama
           END AS instalasi_nama,
           CASE
           WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.tgl_pendaftaran
           WHEN (pendaftaran_t.instalasi_id = 2) THEN pendaftaran_t.tgl_pendaftaran
           ELSE pasienadmisi_t.tgl_pendaftaran
           END AS tgl_pendaftaran,
           pendaftaran_t.carabayar_id,
           carabayar_m.carabayar_nama,
           pendaftaran_t.penjamin_id,
           penjamin_m.penjamin_nama
           FROM ((((((((((((((((koreksidiagnosa_t
           LEFT JOIN pendaftaran_t ON ((koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
           LEFT JOIN pasienadmisi_t ON ((koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
           JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
           JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
           JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
           LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
           LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
           JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
           JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
           JOIN kelompokdiagnosa_m ON ((koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id)))
           JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
           JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
           LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
           LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
           LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
           LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
           WHERE ((koreksidiagnosa_t.is_deleted = false) AND (pendaftaran_t.instalasi_id <> 3) AND (koreksidiagnosa_t.kelompokdiagnosa_id = ANY (ARRAY[1, 2, 3])))
           UNION ALL
           SELECT 'RD ke RI'::text AS tipe,
           koreksidiagnosa_t.pasien_id,
           pasienadmisi_t.pendaftaran_id,
           koreksidiagnosa_t.tgl_koreksidiagnosa AS tglmorbiditas,
           NULL::text AS kasusdiagnosa,
           diagnosa_m.diagnosa_id,
           diagnosa_m.diagnosa_kode,
           diagnosa_m.diagnosa_nama,
           diagnosa_m.diagnosa_namalainnya,
           diagnosa_m.diagnosa_nourut,
           pendaftaran_t.golonganumur_id,
           pasien_m.jeniskelamin,
           golonganumur_m.golonganumur_nama,
           pendaftaran_t.no_pendaftaran,
           pasien_m.no_rekam_medik,
           pasien_m.nama_pasien,
           kelompokdiagnosa_m.kelompokdiagnosa_nama,
           diagnosa_m.diagnosa_katakunci,
           klasifikasidiagnosa_m.klasifikasidiagnosa_id,
           klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
           dtd_m.dtd_kode,
           dtd_m.dtd_nama,
           ruang_admisi.ruangan_id,
           ruang_admisi.ruangan_nama,
           peg_admisi.pegawai_id,
           peg_admisi.nama_pegawai,
           koreksidiagnosa_t.kelompokdiagnosa_id,
           ruang_admisi.instalasi_id,
           instalasi_admisi.instalasi_nama,
           pasienadmisi_t.tgl_pendaftaran,
           carabayar_m.carabayar_id,
           carabayar_m.carabayar_nama,
           pendaftaran_t.penjamin_id,
           penjamin_m.penjamin_nama
           FROM ((((((((((((((((koreksidiagnosa_t
           JOIN pendaftaran_t ON ((koreksidiagnosa_t.pasien_id = pendaftaran_t.pasien_id)))
           JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
           JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
           JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
           JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
           LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
           LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
           JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
           JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
           JOIN kelompokdiagnosa_m ON ((koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id)))
           JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
           JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
           LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
           LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
           LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
           LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
           WHERE ((koreksidiagnosa_t.is_deleted = false) AND (ruang_admisi.instalasi_id = 3) AND (instalasi_admisi.instalasi_id = 3) AND (koreksidiagnosa_t.pasienadmisi_id IS NOT NULL) AND (koreksidiagnosa_t.kelompokdiagnosa_id = ANY (ARRAY[1, 2, 3])))
            ;");
            $this->execute('
                ALTER TABLE public.laporanrekapmorbiditas_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210125_093848_migrate_3309_20210125_view_laporanrekapmorbiditas_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210125_093848_migrate_3309_20210125_view_laporanrekapmorbiditas_v cannot be reverted.\n";

        return false;
    }
    */
}
