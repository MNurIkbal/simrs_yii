<?php

use yii\db\Migration;

/**
 * Class m201205_225813_migrate_mhkn_20201206_view_infopasienbelumsoap_v
 */
class m201205_225813_migrate_mhkn_20201206_view_infopasienbelumsoap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienbelumsoap_v;');
        $this->execute("CREATE VIEW \"public\".\"infopasienbelumsoap_v\" AS
             SELECT 'RJ'::text AS jenis,
    soap_rj.tgl_pendaftaran,
    soap_rj.instalasi_id,
    soap_rj.ruangan_id,
    soap_rj.ruangan,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    soap_rj.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan
           FROM ((pendaftaran_t
             LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
          WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1])) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))) soap_rj ON ((pegawai_m.pegawai_id = soap_rj.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
UNION ALL
 SELECT 'RD-RI'::text AS jenis,
    soap_ri.tgl_pendaftaran,
    soap_ri.instalasi_id,
    soap_ri.ruangan_id,
    soap_ri.ruangan,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    soap_ri.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN peg_rd.pegawai_id
                    ELSE peg_ri.pegawai_id
                END AS pegawai_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
                    ELSE ri.instalasi_id
                END AS instalasi_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
                    ELSE ri.ruangan_id
                END AS ruangan_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.ruangan_nama
                    ELSE ri.ruangan_nama
                END AS ruangan
           FROM ((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
             LEFT JOIN pegawai_m peg_rd ON ((pendaftaran_t.pegawai_id = peg_rd.pegawai_id)))
             LEFT JOIN pegawai_m peg_ri ON ((pasienadmisi_t.pegawai_id = peg_ri.pegawai_id)))
             LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
             LEFT JOIN ruangan_m ri ON ((pendaftaran_t.ruangan_id = ri.ruangan_id)))
          WHERE ((cppt_t.cppt_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pendaftaran_t.instalasi_id <> 1))) soap_ri ON ((pegawai_m.pegawai_id = soap_ri.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
UNION ALL
 SELECT 'RDRI'::text AS jenis,
    soap_rj.tgl_pendaftaran,
    soap_rj.instalasi_id,
    soap_rj.ruangan_id,
    soap_rj.ruangan,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    soap_rj.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.pegawai_id,
            ruangan_m.instalasi_id,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan
           FROM ((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
             LEFT JOIN cppt_t ON ((pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id)))
             JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
          WHERE ((ruangan_m.instalasi_id = 3) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL) AND (cppt_t.cppt_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))) soap_rj ON ((pegawai_m.pegawai_id = soap_rj.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
            ;");
            $this->execute('ALTER TABLE public.infopasienbelumsoap_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_225813_migrate_mhkn_20201206_view_infopasienbelumsoap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_225813_migrate_mhkn_20201206_view_infopasienbelumsoap_v cannot be reverted.\n";

        return false;
    }
    */
}
