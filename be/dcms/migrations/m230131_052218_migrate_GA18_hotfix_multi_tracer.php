<?php

use yii\db\Migration;

/**
 * Class m230131_052218_migrate_GA18_hotfix_multi_tracer
 */
class m230131_052218_migrate_GA18_hotfix_multi_tracer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infodaftarpasien_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infodaftarpasien_v
        AS SELECT data_pasien.pendaftaran_id,
            data_pasien.pasienadmisi_id,
            data_pasien.tgl_pendaftaran,
            data_pasien.no_pendaftaran,
            data_pasien.status_pasien,
            data_pasien.pasien_id,
            data_pasien.no_rekam_medik,
            data_pasien.nama_pasien,
            data_pasien.ruangan_id,
            data_pasien.ruangan_nama,
            data_pasien.instalasi_id,
            data_pasien.instalasi_nama,
            data_pasien.carabayar_id,
            data_pasien.carabayar_nama,
            data_pasien.penjamin_id,
            data_pasien.penjamin_nama,
            data_pasien.pegawai_id,
            data_pasien.nama_pegawai,
            data_pasien.status_konfirmasirm_id,
            data_pasien.status_konfirmasirm,
            data_pasien.nosep,
            data_pasien.status_pasien_nama,
            data_pasien.status_rekam_medik_id,
            data_pasien.status_rekam_medik_nama,
            data_pasien.dokrekammedis_id,
            data_pasien.jenis_reservasi_id,
            data_pasien.jenis_reservasi_nama,
            data_pasien.pendaftaranol_id
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.status_pasien,
                    pasien_m.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    instalasi_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai,
                    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
                    fgetnamalookup(pendaftaran_t.status_konfirmasi::integer) AS status_konfirmasirm,
                    bpjs_t.nosep,
                    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien_nama,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN 1306
                            ELSE permintaandokrekammedik_t.status_rekam_medik
                        END AS status_rekam_medik_id,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN ( SELECT lookup_m.lookup_name
                               FROM lookup_m
                              WHERE lookup_m.lookup_id = 1306)
                            ELSE look_statusrekammedik.lookup_name
                        END AS status_rekam_medik_nama,
                    dokrekammedis_m.dokrekammedis_id,
                    NULL::integer AS jenis_reservasi_id,
                    'Pendaftaran Langsung'::text AS jenis_reservasi_nama,
                    null::integer as pendaftaranol_id
                   FROM pendaftaran_t
                     JOIN ( SELECT pasien.pasien_id,
                            pasien.no_rekam_medik,
                            pasien.nama_pasien
                           FROM pasien_m pasien) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT ruangan.ruangan_id,
                            ruangan.ruangan_nama
                           FROM ruangan_m ruangan) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT instalasi.instalasi_id,
                            instalasi.instalasi_nama
                           FROM instalasi_m instalasi) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT carabayar.carabayar_id,
                            carabayar.carabayar_nama
                           FROM carabayar_m carabayar) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT penjamin.penjamin_id,
                            penjamin.penjamin_nama
                           FROM penjamin_m penjamin) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT pegawai.pegawai_id,
                            pegawai.nama_pegawai
                           FROM pegawai_m pegawai) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT bpjs.bpjs_id,
                            bpjs.nosep
                           FROM bpjs_t bpjs) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
                     LEFT JOIN ( SELECT permintaandokrekammedik.status_rekam_medik,
                            permintaandokrekammedik.pendaftaran_id
                           FROM permintaandokrekammedik_t permintaandokrekammedik) permintaandokrekammedik_t ON pendaftaran_t.pendaftaran_id = permintaandokrekammedik_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pasien_id,
                            a.dokrekammedis_id
                           FROM dokrekammedis_m a) dokrekammedis_m ON pasien_m.pasien_id = dokrekammedis_m.pasien_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_statusrekammedik ON permintaandokrekammedik_t.status_rekam_medik = look_statusrekammedik.lookup_id
                  WHERE pendaftaran_t.instalasi_id <> 3
                UNION ALL
                 SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.status_pasien,
                    pasien_m.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    instalasi_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai,
                    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
                    fgetnamalookup(pendaftaran_t.status_konfirmasi::integer) AS status_konfirmasirm,
                    bpjs_t.nosep,
                    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien_nama,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN 1306
                            ELSE permintaandokrekammedik_t.status_rekam_medik
                        END AS status_rekam_medik_id,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN ( SELECT lookup_m.lookup_name
                               FROM lookup_m
                              WHERE lookup_m.lookup_id = 1306)
                            ELSE look_statusrekammedik.lookup_name
                        END AS status_rekam_medik_nama,
                    dokrekammedis_m.dokrekammedis_id,
                    NULL::integer AS jenis_reservasi_id,
                    'Pendaftaran Langsung'::text AS jenis_reservasi_nama,
                    null::integer as pendaftaranol_id
                   FROM pendaftaran_t
                     JOIN ( SELECT pasienadmisi.pasienadmisi_id,
                            pasienadmisi.tgl_admisi,
                            pasienadmisi.ruangan_id,
                            pasienadmisi.carabayar_id,
                            pasienadmisi.penjamin_id,
                            pasienadmisi.pegawai_id,
                            pasienadmisi.bpjs_id
                           FROM pasienadmisi_t pasienadmisi) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT pasien.pasien_id,
                            pasien.no_rekam_medik,
                            pasien.nama_pasien
                           FROM pasien_m pasien) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT ruangan.ruangan_id,
                            ruangan.ruangan_nama,
                            ruangan.instalasi_id
                           FROM ruangan_m ruangan) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT instalasi.instalasi_id,
                            instalasi.instalasi_nama
                           FROM instalasi_m instalasi) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT carabayar.carabayar_id,
                            carabayar.carabayar_nama
                           FROM carabayar_m carabayar) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT penjamin.penjamin_id,
                            penjamin.penjamin_nama
                           FROM penjamin_m penjamin) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT pegawai.pegawai_id,
                            pegawai.nama_pegawai
                           FROM pegawai_m pegawai) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT permintaandokrekammedik.status_rekam_medik,
                            permintaandokrekammedik.pendaftaran_id
                           FROM permintaandokrekammedik_t permintaandokrekammedik) permintaandokrekammedik_t ON pendaftaran_t.pendaftaran_id = permintaandokrekammedik_t.pendaftaran_id
                     LEFT JOIN ( SELECT bpjs.bpjs_id,
                            bpjs.nosep
                           FROM bpjs_t bpjs) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
                     LEFT JOIN ( SELECT a.pasien_id,
                            a.dokrekammedis_id
                           FROM dokrekammedis_m a) dokrekammedis_m ON pasien_m.pasien_id = dokrekammedis_m.pasien_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_statusrekammedik ON permintaandokrekammedik_t.status_rekam_medik = look_statusrekammedik.lookup_id
                UNION ALL
                 SELECT pendaftaranol_t.pendaftaran_id,
                    NULL::integer AS pasienadmisi_id,
                    pendaftaranol_t.tgl_pendaftaranol AS tgl_pendaftaran,
                    pendaftaranol_t.no_pendaftaranol AS no_pendaftaran,
                    pendaftaranol_t.status_pasien::character varying AS status_pasien,
                    pasien_m.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    carabayar_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai,
                        CASE
                            WHEN pendaftaranol_t.is_cetaktracer = true THEN ( SELECT lookup_m.lookup_id::character(1) AS lookup_id
                               FROM lookup_m
                              WHERE lookup_m.lookup_type::text = 'status_konfirmasirm'::text AND lookup_m.lookup_value::text = 'Proses'::text)
                            ELSE ( SELECT lookup_m.lookup_id::character(1) AS lookup_id
                               FROM lookup_m
                              WHERE lookup_m.lookup_type::text = 'status_konfirmasirm'::text AND lookup_m.lookup_value::text = 'Belum Proses'::text)
                        END AS status_konfirmasirm_id,
                        CASE
                            WHEN pendaftaranol_t.is_cetaktracer = true THEN ( SELECT lookup_m.lookup_name
                               FROM lookup_m
                              WHERE lookup_m.lookup_id = 665)
                            ELSE ( SELECT lookup_m.lookup_name
                               FROM lookup_m
                              WHERE lookup_m.lookup_id = 664)
                        END AS status_konfirmasirm,
                    pendaftaranol_t.no_bpjs,
                    fgetnamalookup(pendaftaranol_t.status_pasien) AS status_pasien_nama,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN 1306
                            ELSE permintaandokrekammedik_t.status_rekam_medik
                        END AS status_rekam_medik_id,
                        CASE COALESCE(dokrekammedis_m.dokrekammedis_id, 1306)
                            WHEN 1306 THEN ( SELECT lookup_m.lookup_name
                               FROM lookup_m
                              WHERE lookup_m.lookup_id = 1306)
                            ELSE look_statusrekammedik.lookup_name
                        END AS status_rekam_medik_nama,
                    dokrekammedis_m.dokrekammedis_id,
                    pendaftaranol_t.jenis_reservasi AS jenis_reservasi_id,
                    look_jenis_reservasi.lookup_name AS jenis_reservasi_nama,
                    pendaftaranol_t.pendaftaranol_id
                   FROM pendaftaranol_t
                     JOIN ( SELECT pasien.pasien_id,
                            pasien.no_rekam_medik,
                            pasien.nama_pasien
                           FROM pasien_m pasien) pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT ruangan.ruangan_id,
                            ruangan.ruangan_nama,
                            ruangan.instalasi_id
                           FROM ruangan_m ruangan) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT instalasi.instalasi_id,
                            instalasi.instalasi_nama
                           FROM instalasi_m instalasi) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT carabayar.carabayar_id,
                            carabayar.carabayar_nama
                           FROM carabayar_m carabayar) carabayar_m ON pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT penjamin.penjamin_id,
                            penjamin.penjamin_nama
                           FROM penjamin_m penjamin) penjamin_m ON pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT pegawai.pegawai_id,
                            pegawai.nama_pegawai
                           FROM pegawai_m pegawai) pegawai_m ON pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT permintaandokrekammedik.status_rekam_medik,
                            permintaandokrekammedik.pendaftaran_id
                           FROM permintaandokrekammedik_t permintaandokrekammedik) permintaandokrekammedik_t ON pendaftaranol_t.pendaftaran_id = permintaandokrekammedik_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pasien_id,
                            a.dokrekammedis_id
                           FROM dokrekammedis_m a) dokrekammedis_m ON pasien_m.pasien_id = dokrekammedis_m.pasien_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_statusrekammedik ON permintaandokrekammedik_t.status_rekam_medik = look_statusrekammedik.lookup_id
                     JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_jenis_reservasi ON pendaftaranol_t.jenis_reservasi::integer = look_jenis_reservasi.lookup_id) data_pasien;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230131_052218_migrate_GA18_hotfix_multi_tracer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230131_052218_migrate_GA18_hotfix_multi_tracer cannot be reverted.\n";

        return false;
    }
    */
}
