<?php

use yii\db\Migration;

/**
 * Class m210202_030330_migrate_20210202_view_infopasienrskoreksi_v
 */
class m210202_030330_migrate_20210202_view_infopasienrskoreksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienrskoreksi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienrskoreksi_v\" AS
            SELECT gabung.jenis_rawat,
            gabung.pendaftaran_id,
            gabung.pasienadmisi_id,
            gabung.tgl_pendaftaran,
            gabung.no_pendaftaran,
            gabung.pasien_id,
            gabung.no_rekam_medik,
            gabung.nama_pasien,
            gabung.jenis_kelamin,
            gabung.carabayar_id,
            gabung.carabayar_nama,
            gabung.penjamin_id,
            gabung.penjamin_nama,
            gabung.jeniskasuspenyakit_id,
            gabung.jeniskasuspenyakit_nama,
            gabung.instalasi_id,
            gabung.instalasi_nama,
            gabung.ruangan_id,
            gabung.ruangan_nama,
            gabung.dokter_dpjp_id,
            gabung.dokter_dpjp,
            gabung.status_verifikasi,
            gabung.status_verif,
            gabung.pasienpulang_id,
            gabung.status_periksa,
            gabung.umur,
            gabung.tanggal_lahir,
            gabung.kelaspelayanan_id,
            gabung.kelaspelayanan_nama,
            gabung.photopasien,
            gabung.tglpasienpulang,
            gabung.status_pengajuanklaim,
            gabung.no_identitas_pasien
            FROM ( SELECT 'RJ-RD'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.pasienpulang_id,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien
            FROM (((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pegawai_m dokter_dpjp ON ((pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN kelaspelayanan_m ON ((kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2]))
            UNION ALL
            SELECT 'RI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienadmisi_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pasienadmisi_t.pasienpulang_id,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien
            FROM ((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m dokter_dpjp ON ((pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pasienadmisi_t.pasienadmisi_id = pengajuanklaimdetail_t.pasienadmisi_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))) gabung
            GROUP BY gabung.jenis_rawat, gabung.pendaftaran_id, gabung.pasienadmisi_id, gabung.tgl_pendaftaran, gabung.no_pendaftaran, gabung.pasien_id, gabung.no_rekam_medik, gabung.nama_pasien, gabung.jenis_kelamin, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_id, gabung.jeniskasuspenyakit_nama, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruangan_id, gabung.ruangan_nama, gabung.dokter_dpjp_id, gabung.dokter_dpjp, gabung.status_verifikasi, gabung.status_verif, gabung.pasienpulang_id, gabung.status_periksa, gabung.umur, gabung.tanggal_lahir, gabung.kelaspelayanan_id, gabung.kelaspelayanan_nama, gabung.photopasien, gabung.tglpasienpulang, gabung.status_pengajuanklaim, gabung.no_identitas_pasien
            ;");
            $this->execute('
                ALTER TABLE public.infopasienrskoreksi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210202_030330_migrate_20210202_view_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210202_030330_migrate_20210202_view_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }
    */
}
