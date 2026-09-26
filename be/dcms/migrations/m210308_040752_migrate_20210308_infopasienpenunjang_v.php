<?php

use yii\db\Migration;

/**
 * Class m210308_040752_migrate_20210308_infopasienpenunjang_v
 */
class m210308_040752_migrate_20210308_infopasienpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienpenunjang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienpenunjang_v\" AS
            SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
            pasien_m.nama_pasien,
            pasien_m.alamat_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan_penunjang,
            ruangasal.ruangan_nama AS ruangan_asal,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienmasukpenunjang_t.status_periksa,
            ruangan_m.instalasi_id,
            pendaftaran_t.created_by,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.is_pasientitipan,
            pasienadmisi_t.kelas_ditagihkan_id,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            pasienadmisi_t.is_stoptitipan,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS nama_status_periksa,
            pasien_m.tanggal_lahir,
            pendaftaran_t.keterangan_pendaftaran,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pegawai_m.pegawai_id,
            kelaspelayanan_m.kelaspelayanan_id,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.instalasi_id AS instalasiasal_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pasien_m.additional_pasien,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
            CASE
            WHEN (antrian_poli.jenisantrian_id = 312) THEN (antrian_poli.no_antrian)::text
            ELSE '-'::text
            END AS no_antrian_poli,
            pasien_m.catatanpenting_pasien,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            penanggungjawab_m.pj_pekerjaan_id,
            pj_kerja.pekerjaan_nama AS pj_pekerjaan_nama,
            penanggungjawab_m.pj_propinsi_id,
            pj_prop.propinsi_nama AS pj_propinsi_nama,
            penanggungjawab_m.pj_kabupaten_id,
            pj_kab.kabupaten_nama AS pj_kabupaten_nama,
            penanggungjawab_m.pj_kecamatan_id,
            pj_kec.kecamatan_nama AS pj_kecamatan_nama,
            penanggungjawab_m.pj_kelurahan_id,
            pj_kel.kelurahan_nama AS pj_kelurahan_nama,
            pasien_m.bahasa_sehari,
            fgetnamalookup((pasien_m.bahasa_sehari)::integer) AS bahasa_sehari_nama,
            penanggungjawab_m.pj_namadepan,
            fgetnamalookup((penanggungjawab_m.pj_namadepan)::integer) AS pj_namadepan_nama,
            pendaftaran_t.limit_tagihan
            FROM (((((((((((((((((((((((((pasienmasukpenunjang_t
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ruangan_m ruangasal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id)))
            JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            LEFT JOIN antrian_t antrian_poli ON (((pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id) AND (antrian_poli.jenisantrian_id = 312))))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN pekerjaan_m pj_kerja ON ((penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id)))
            LEFT JOIN propinsi_m pj_prop ON ((penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id)))
            LEFT JOIN kabupaten_m pj_kab ON ((penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id)))
            LEFT JOIN kecamatan_m pj_kec ON ((penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id)))
            LEFT JOIN kelurahan_m pj_kel ON ((penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id)))
            LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
            LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
            LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
            WHERE ((pendaftaran_t.instalasi_id <> 21) AND (pasienmasukpenunjang_t.is_active = true) AND (pasienmasukpenunjang_t.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.infopasienpenunjang_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_040752_migrate_20210308_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_040752_migrate_20210308_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
