<?php

use yii\db\Migration;

/**
 * Class m211006_085326_improvment_monitoring_rm_US342
 */
class m211006_085326_improvment_monitoring_rm_US342 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS monitoringrekammedik_v; 
        ');

        $this->execute('
            CREATE VIEW monitoringrekammedik_v AS  
            SELECT permintaandokrekammedik_t.permintaandokrekammedik_id,
                permintaandokrekammedik_t.tgl_permintaan,
                permintaandokrekammedik_t.tgl_dikembalikan,
                permintaandokrekammedik_t.pendaftaran_id,
                permintaandokrekammedik_t.pasienadmisi_id,
                    CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 0)
                        WHEN 0 THEN 0
                        ELSE permintaandokrekammedik_t.status_rekam_medik
                    END AS status_rekam_medik,
                    CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 0)
                        WHEN 0 THEN \'Doc MR Needed\'::character varying
                        ELSE lookup_m.lookup_name
                    END AS status_rekam_medik_nama,
                pendaftaran_t.no_pendaftaran,
                permintaandokrekammedik_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                pasien_m.tanggal_lahir,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                dokrekammedis_m.subrak_id,
                dokrekammedis_m.lokasirak_id,
                dokrekammedis_m.nodokumenrm,
                NULL::integer AS kamarruangan_id,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::integer AS kamartempattidur_id,
                NULL::character varying AS no_tempattidur
               FROM (((((((permintaandokrekammedik_t
                 JOIN pendaftaran_t ON ((permintaandokrekammedik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((permintaandokrekammedik_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN lookup_m ON ((permintaandokrekammedik_t.status_rekam_medik = lookup_m.lookup_id)))
                 JOIN ruangan_m ON ((COALESCE(pendaftaran_t.ruangan_id, permintaandokrekammedik_t.ruangan_id) = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((permintaandokrekammedik_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
              WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2]))
            UNION ALL
             SELECT permintaandokrekammedik_t.permintaandokrekammedik_id,
                permintaandokrekammedik_t.tgl_permintaan,
                permintaandokrekammedik_t.tgl_dikembalikan,
                permintaandokrekammedik_t.pendaftaran_id,
                permintaandokrekammedik_t.pasienadmisi_id,
                    CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 0)
                        WHEN 0 THEN 0
                        ELSE permintaandokrekammedik_t.status_rekam_medik
                    END AS status_rekam_medik,
                    CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 0)
                        WHEN 0 THEN \'Doc MR Needed\'::character varying
                        ELSE lookup_m.lookup_name
                    END AS status_rekam_medik_nama,
                pendaftaran_t.no_pendaftaran,
                permintaandokrekammedik_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                pasien_m.tanggal_lahir,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                dokrekammedis_m.subrak_id,
                dokrekammedis_m.lokasirak_id,
                dokrekammedis_m.nodokumenrm,
                kamarruangan_m.kamarruangan_id,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.kamartempattidur_id,
                kamartempattidur_m.no_tempattidur
               FROM ((((((((((permintaandokrekammedik_t
                 JOIN pendaftaran_t ON ((permintaandokrekammedik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasienadmisi_t ON ((permintaandokrekammedik_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((permintaandokrekammedik_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN lookup_m ON ((permintaandokrekammedik_t.status_rekam_medik = lookup_m.lookup_id)))
                 JOIN ruangan_m ON ((COALESCE(pasienadmisi_t.ruangan_id, permintaandokrekammedik_t.ruangan_id) = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((permintaandokrekammedik_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
                 LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
              WHERE (pasienadmisi_t.pasienpulang_id IS NULL);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211006_085326_improvment_monitoring_rm_US342 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211006_085326_improvment_monitoring_rm_US342 cannot be reverted.\n";

        return false;
    }
    */
}
