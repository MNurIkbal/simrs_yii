<?php

use yii\db\Migration;

/**
 * Class m220604_161220_migrate_skema_fisioterapi_MHG2387_kunjunganpasienfisioterapiranap_v
 */
class m220604_161220_migrate_skema_fisioterapi_MHG2387_kunjunganpasienfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.kunjunganpasienfisioterapiranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kunjunganpasienfisioterapiranap_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            pasienadmisi_t.status_ranap AS status_periksa_id,
            dokterdpjp.pegawai_id AS dokterperujuk_id,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa_nama,
            pasienadmisi_t.tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
            pasien_m.tanggal_lahir,
            pasien_m.no_rekam_medik,
            programterapidetail_t.terapi_nama,
            dokterdpjp.nama_pegawai AS dokterperujuk_nama,
            pendaftaran_t.status_bayar AS statusbayar_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            programterapi_t.programterapi_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.alamat_pasien,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            kamarruangan_m.kamarruangan_id AS kamar_id,
            kamartempattidur_m.no_tempattidur,
            kamartempattidur_m.kamartempattidur_id,
            dokterdpjpranap.nama_pegawai AS dokterdpjp_nama,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pasienmasukpenunjang_t.status_periksa,
            programterapi_t.frekuensi,
            programterapi_t.realisasi,
            programterapi_t.sisa,
            COALESCE(pembayaran.bayar, (0)::bigint) AS bayar,
            CASE
            WHEN ((COALESCE(soapfisioterapi_t.jumlah, (0)::bigint) = COALESCE(pembayaran.bayar, (0)::bigint)) AND (pembayaran.bayar > 0)) THEN 'LUNAS'::text
            WHEN ((programterapidetail_t.is_paketfisio = true) AND (pembayaran.bayar = 1)) THEN 'LUNAS'::text
            ELSE 'BELUM LUNAS'::text
            END AS statusbayar_nama,
            programterapidetail_t.is_paketfisio,
            programterapi_t.status_fisio AS status_fisio_id,
            look_status_fisio.lookup_name AS status_fisio_nama,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            look_status_program_fisio.lookup_name AS status_program_fisio_nama
            FROM ((((((((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.kelaspelayanan_id,
            a.penjamin_id,
            a.kamarruangan_id,
            a.pegawai_id,
            a.status_ranap,
            a.tgl_pendaftaran,
            a.kamartempattidur_id,
            a.ruangan_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.alamat_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.programterapi_id,
            a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.dokterperujuk_id,
            a.frekuensi,
            a.status_fisio,
            a.status_program_fisio,
            a.realisasi,
            a.sisa
            FROM programterapi_t a) programterapi_t ON ((pendaftaran_t.pendaftaran_id = programterapi_t.pendaftaran_id)))
            JOIN ( SELECT pt.pasienmasukpenunjang_id,
            pt.tglmasukpenunjang,
            pt.pendaftaran_id,
            pt.programterapi_id,
            pt.pasienadmisi_id,
            pt.status_periksa
            FROM pasienmasukpenunjang_t pt) pasienmasukpenunjang_t ON ((programterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            a.is_paketfisio,
            string_agg((dm.daftartindakan_nama)::text, ','::text) AS terapi_nama
            FROM (((programterapidetail_t a
            LEFT JOIN daftartindakan_m dm ON ((dm.daftartindakan_id = a.daftartindakan_id)))
            LEFT JOIN pegawai_m terapis ON ((terapis.pegawai_id = a.terapis_id)))
            LEFT JOIN pegawai_m dokterperujuk ON ((dokterperujuk.pegawai_id = a.dokterperujuk_id)))
            WHERE (a.is_deleted = false)
            GROUP BY a.programterapi_id, a.is_paketfisio) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokterdpjp ON ((pasienadmisi_t.pegawai_id = dokterdpjp.pegawai_id)))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
            FROM pegawai_m b) dokterdpjpranap ON ((pasienadmisi_t.pegawai_id = dokterdpjpranap.pegawai_id)))
            LEFT JOIN ( SELECT count(a.soapfisioterapi_id) AS jumlah,
            a.pasien_id,
            a.programterapi_id,
            a.pendaftaran_id
            FROM soapfisioterapi_t a
            GROUP BY a.pasien_id, a.pendaftaran_id, a.programterapi_id) soapfisioterapi_t ON (((programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id) AND (programterapi_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id))))
            LEFT JOIN ( SELECT pasienmasukpenunjang_t_1.programterapi_id,
            sum(
            CASE
            WHEN (tindakanpelayanan_t.tindakan = tindakanpelayanan_t.jml_bayar) THEN 1
            ELSE 0
            END) AS bayar
            FROM (pasienmasukpenunjang_t pasienmasukpenunjang_t_1
            JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            count(tindakanpelayanan_t_1.tindakanpelayanan_id) AS tindakan,
            sum(
            CASE
            WHEN (tindakanpelayanan_t_1.tindakansudahbayar_id IS NOT NULL) THEN 1
            ELSE 0
            END) AS jml_bayar
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            WHERE (tindakanpelayanan_t_1.instalasi_id = 3)
            GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON ((pasienmasukpenunjang_t_1.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
            GROUP BY pasienmasukpenunjang_t_1.programterapi_id) pembayaran ON ((programterapi_t.programterapi_id = pembayaran.programterapi_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_fisio ON (((programterapi_t.status_fisio)::integer = look_status_fisio.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_program_fisio ON (((programterapi_t.status_program_fisio)::integer = look_status_program_fisio.lookup_id)))
            WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[2, 3]))
            ;");
        $this->execute('
            ALTER TABLE public.kunjunganpasienfisioterapiranap_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_161220_migrate_skema_fisioterapi_MHG2387_kunjunganpasienfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_161220_migrate_skema_fisioterapi_MHG2387_kunjunganpasienfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
