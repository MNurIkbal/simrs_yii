<?php

use yii\db\Migration;

/**
 * Class m210702_142006_improvment_notifikasi_cppt
 */
class m210702_142006_improvment_notifikasi_cppt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."notifikasicppt_v";
        ');

        $this->execute('
            CREATE VIEW "public"."notifikasicppt_v" AS  SELECT \'RD\'::text AS jenis_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien.nama_pasien,
            cppt.cppt_id,
            cppt.tgl_cppt, 
            COALESCE(pendaftaran_t.pegawai_id, cppt.pegawai_id) AS pegawai_id,
            pendaftaran_t.status_periksa,
            l_status_periksa.lookup_name AS status_periksa_nama,
                CASE COALESCE(cppt.cppt_id, 0)
                    WHEN 0 THEN true
                    ELSE
                    CASE COALESCE(cppt_belum.ct_belum, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END
                END AS is_belum,
            pendaftaran_t.pendaftaran_id,
            ruangan.ruangan_id,
            ruangan.instalasi_id,
            COALESCE(cppt.created_date, pendaftaran_t.created_date) AS created_date
           FROM (((((pendaftaran_t
             JOIN ( SELECT DISTINCT ON (pasien_m.pasien_id) pasien_m.pasien_id,
                    pasien_m.nama_pasien
                   FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
             LEFT JOIN ( SELECT DISTINCT ON (cppt_t.pendaftaran_id) cppt_t.pendaftaran_id,
                    cppt_t.tgl_cppt,
                    cppt_t.cppt_id,
                    cppt_t.pegawai_id,
                    cppt_t.ruangan_id,
                    cppt_t.created_date
                   FROM cppt_t) cppt ON ((pendaftaran_t.pendaftaran_id = cppt.pendaftaran_id)))
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) l_status_periksa ON (((pendaftaran_t.status_periksa)::integer = l_status_periksa.lookup_id)))
             LEFT JOIN ( SELECT count(cppt_t.cppt_id) AS ct_belum,
                    cppt_t.pendaftaran_id
                   FROM cppt_t
                  WHERE ((COALESCE(cppt_t.subject, \'\'::text) = \'\'::text) OR (COALESCE(cppt_t.object, \'\'::text) = \'\'::text) OR (COALESCE(cppt_t.planning, \'\'::text) = \'\'::text) OR (COALESCE((cppt_t.a_diag_utama)::text, \'\'::text) = \'\'::text) OR (cppt_t.subject = \'-\'::text) OR (cppt_t.object = \'-\'::text) OR (cppt_t.planning = \'-\'::text) OR ((cppt_t.a_diag_utama)::text = \'-\'::text))
                  GROUP BY cppt_t.pendaftaran_id) cppt_belum ON ((pendaftaran_t.pendaftaran_id = cppt_belum.pendaftaran_id)))
             LEFT JOIN ( SELECT ruangan_m.ruangan_id,
                    ruangan_m.instalasi_id
                   FROM ruangan_m) ruangan ON ((COALESCE(cppt.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id)))
          WHERE ((pendaftaran_t.instalasi_id = 2) AND ((pendaftaran_t.status_periksa)::integer = 2))
        UNION ALL
         SELECT \'RI\'::text AS jenis_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien.nama_pasien,
            cppt.cppt_id,
            cppt.tgl_cppt,
            COALESCE(pendaftaran_t.pegawai_id, cppt.pegawai_id) AS pegawai_id,
            pendaftaran_t.status_periksa,
            l_status_periksa.lookup_name AS status_periksa_nama,
                CASE COALESCE(cppt.cppt_id, 0)
                    WHEN 0 THEN true
                    ELSE
                    CASE COALESCE(cppt_belum.ct_belum, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END
                END AS is_belum,
            pendaftaran_t.pendaftaran_id,
            ruangan.ruangan_id,
            ruangan.instalasi_id,
            COALESCE(cppt.created_date, pendaftaran_t.created_date) AS created_date
           FROM ((((((pendaftaran_t
             JOIN ( SELECT DISTINCT ON (pasienadmisi_t.pasienadmisi_id) pasienadmisi_t.pasienadmisi_id,
                    pasienadmisi_t.status_ranap,
                    pasienadmisi_t.ruangan_id
                   FROM pasienadmisi_t) pasienadmisi ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id)))
             JOIN ( SELECT DISTINCT ON (pasien_m.pasien_id) pasien_m.pasien_id,
                    pasien_m.nama_pasien
                   FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
             LEFT JOIN ( SELECT DISTINCT ON (cppt_t.pendaftaran_id) cppt_t.pendaftaran_id,
                    cppt_t.tgl_cppt,
                    cppt_t.cppt_id,
                    cppt_t.pegawai_id,
                    cppt_t.ruangan_id,
                    cppt_t.created_date
                   FROM cppt_t) cppt ON ((pendaftaran_t.pendaftaran_id = cppt.pendaftaran_id)))
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) l_status_periksa ON ((pasienadmisi.status_ranap = l_status_periksa.lookup_id)))
             LEFT JOIN ( SELECT count(cppt_t.cppt_id) AS ct_belum,
                    cppt_t.pendaftaran_id
                   FROM cppt_t
                  WHERE ((COALESCE(cppt_t.subject, \'\'::text) = \'\'::text) OR (COALESCE(cppt_t.object, \'\'::text) = \'\'::text) OR (COALESCE(cppt_t.planning, \'\'::text) = \'\'::text) OR (COALESCE((cppt_t.a_diag_utama)::text, \'\'::text) = \'\'::text) OR (cppt_t.subject = \'-\'::text) OR (cppt_t.object = \'-\'::text) OR (cppt_t.planning = \'-\'::text) OR ((cppt_t.a_diag_utama)::text = \'-\'::text))
                  GROUP BY cppt_t.pendaftaran_id) cppt_belum ON ((pendaftaran_t.pendaftaran_id = cppt_belum.pendaftaran_id)))
             LEFT JOIN ( SELECT ruangan_m.ruangan_id,
                    ruangan_m.instalasi_id
                   FROM ruangan_m) ruangan ON ((COALESCE(cppt.ruangan_id, pasienadmisi.ruangan_id) = ruangan.ruangan_id)))
          WHERE ((pendaftaran_t.is_active IS TRUE) AND (pendaftaran_t.is_deleted IS FALSE) AND (pasienadmisi.status_ranap = 441))
        UNION ALL
         SELECT \'RJ\'::text AS jenis_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien.nama_pasien,
            soaprj.cppt_id,
            soaprj.tgl_cppt,
            COALESCE(pendaftaran_t.pegawai_id, soaprj.pegawai_id) AS pegawai_id,
            pendaftaran_t.status_periksa,
            l_status_periksa.lookup_name AS status_periksa_nama,
                CASE COALESCE(soaprj.cppt_id, 0)
                    WHEN 0 THEN true
                    ELSE
                    CASE COALESCE(soaprj_belum.ct_belum, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END
                END AS is_belum,
            pendaftaran_t.pendaftaran_id,
            ruangan.ruangan_id,
            ruangan.instalasi_id,
            COALESCE(soaprj.created_date, pendaftaran_t.created_date) AS created_date
           FROM (((((pendaftaran_t
             JOIN ( SELECT DISTINCT ON (pasien_m.pasien_id) pasien_m.pasien_id,
                    pasien_m.nama_pasien
                   FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
             LEFT JOIN ( SELECT DISTINCT ON (soaprj_t.pendaftaran_id) soaprj_t.pendaftaran_id,
                    soaprj_t.soaprj_id AS cppt_id,
                    soaprj_t.tgl_soaprj AS tgl_cppt,
                    soaprj_t.pegawai_id,
                    soaprj_t.ruangan_id,
                    soaprj_t.created_date
                   FROM soaprj_t) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) l_status_periksa ON (((pendaftaran_t.status_periksa)::integer = l_status_periksa.lookup_id)))
             LEFT JOIN ( SELECT count(soaprj_t.soaprj_id) AS ct_belum,
                    soaprj_t.pendaftaran_id
                   FROM soaprj_t
                  WHERE ((COALESCE(soaprj_t.subject, \'\'::text) = \'\'::text) OR (COALESCE(soaprj_t.object, \'\'::text) = \'\'::text) OR (COALESCE(soaprj_t.planning, \'\'::text) = \'\'::text) OR (COALESCE((soaprj_t.a_diag_utama)::text, \'\'::text) = \'\'::text) OR (soaprj_t.subject = \'-\'::text) OR (soaprj_t.object = \'-\'::text) OR (soaprj_t.planning = \'-\'::text) OR ((soaprj_t.a_diag_utama)::text = \'-\'::text))
                  GROUP BY soaprj_t.pendaftaran_id) soaprj_belum ON ((pendaftaran_t.pendaftaran_id = soaprj_belum.pendaftaran_id)))
             LEFT JOIN ( SELECT ruangan_m.ruangan_id,
                    ruangan_m.instalasi_id
                   FROM ruangan_m) ruangan ON ((COALESCE(soaprj.ruangan_id, pendaftaran_t.ruangan_id) = ruangan.ruangan_id)))
          WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_active IS TRUE) AND (pendaftaran_t.is_deleted IS FALSE) AND ((pendaftaran_t.status_periksa)::integer = 2));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210702_142006_improvment_notifikasi_cppt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210702_142006_improvment_notifikasi_cppt cannot be reverted.\n";

        return false;
    }
    */
}
