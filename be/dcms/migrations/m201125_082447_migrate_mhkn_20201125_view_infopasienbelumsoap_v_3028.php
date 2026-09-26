<?php

use yii\db\Migration;

/**
 * Class m201125_082447_migrate_mhkn_20201125_view_infopasienbelumsoap_v_3028
 */
class m201125_082447_migrate_mhkn_20201125_view_infopasienbelumsoap_v_3028 extends Migration
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
    soap_rj.instalasi,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    soap_rj.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama AS instalasi,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan
           FROM (((pendaftaran_t
             LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL))) soap_rj ON ((pegawai_m.pegawai_id = soap_rj.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
UNION ALL
 SELECT 'RD-RI'::text AS jenis,
    soap_ri.tgl_pendaftaran,
    soap_ri.instalasi_id,
    soap_ri.ruangan_id,
    soap_ri.ruangan,
    soap_ri.instalasi,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    soap_ri.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pegawai_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
                    ELSE ri.instalasi_id
                END AS instalasi_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_nama
                    ELSE ins_ri.instalasi_nama
                END AS instalasi,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
                    ELSE ri.ruangan_id
                END AS ruangan_id,
                CASE
                    WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.ruangan_nama
                    ELSE ri.ruangan_nama
                END AS ruangan
           FROM ((((((pendaftaran_t
             JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (cppt_t.is_deleted = false))))
             LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
             LEFT JOIN ruangan_m ri ON ((pasienadmisi_t.ruangan_id = ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_rd ON ((rd.instalasi_id = ins_rd.instalasi_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ri.instalasi_id = ins_ri.instalasi_id)))
          WHERE ((pendaftaran_t.pegawai_id IS NOT NULL) AND (cppt_t.cppt_id IS NULL))) soap_ri ON ((pegawai_m.pegawai_id = soap_ri.pegawai_id)))
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
        echo "m201125_082447_migrate_mhkn_20201125_view_infopasienbelumsoap_v_3028 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_082447_migrate_mhkn_20201125_view_infopasienbelumsoap_v_3028 cannot be reverted.\n";

        return false;
    }
    */
}
