<?php

use yii\db\Migration;

/**
 * Class m210421_004805_migrate_202104120_3713_sy_pasienmasukpenunjang_v
 */
class m210421_004805_migrate_202104120_3713_sy_pasienmasukpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sy_pasienmasukpenunjang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pasienmasukpenunjang_v\" AS
             SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.jeniskasuspenyakit_id,
    pasienmasukpenunjang_t.pasienadmisi_id,
    pasienmasukpenunjang_t.pegawai_id AS nmdr_id,
    peg_penunjang.nama_pegawai AS nmdr,
    peg_penunjang.dokter_id AS kddr,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.pasien_id,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS noregasal,
    pendaftaran_t.instalasi_id AS nmbagian_id,
    ins_pendaftaran.instalasi_nama AS nmbagian,
    ins_pendaftaran.instalasi_singkatan AS kdbagian,
    pendaftaran_t.dokterpengirim_id,
    peg_pendaftaran.nama_pegawai,
    peg_pendaftaran.dokter_id AS rjkndrdari,
    pendaftaran_t.styrujukaninstalasi_id,
    ins_pendaftaran_rujukan.instalasi_singkatan AS rjkndaribagian,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangasal_penunjang.ruangan_nama AS ruanganasal_nama,
    insasal_penunjang.instalasi_singkatan AS rjkndari,
    pasienmasukpenunjang_t.no_masukpenunjang AS noreg,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.kunjungan AS kunjungan_id,
        CASE
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 180) THEN 'B'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 181) THEN 'L'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 310) THEN 'B'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 311) THEN 'L'::text
            ELSE '-'::text
        END AS kunjungan_nama,
    pasienmasukpenunjang_t.instalasiasal_id,
    pasienmasukpenunjang_t.catatan AS cttnrp,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelaspelayanan_m.additional_data AS kelasasal
   FROM (((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangasal_penunjang ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal_penunjang.ruangan_id)))
     JOIN instalasi_m insasal_penunjang ON ((ruangasal_penunjang.instalasi_id = insasal_penunjang.instalasi_id)))
     JOIN instalasi_m ins_pendaftaran ON ((pendaftaran_t.instalasi_id = ins_pendaftaran.instalasi_id)))
     LEFT JOIN instalasi_m ins_pendaftaran_rujukan ON ((pendaftaran_t.styrujukaninstalasi_id = ins_pendaftaran_rujukan.instalasi_id)))
     LEFT JOIN pegawai_m peg_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = peg_penunjang.pegawai_id)))
     LEFT JOIN pegawai_m peg_pendaftaran ON ((pendaftaran_t.dokterpengirim_id = peg_pendaftaran.pegawai_id)))
     LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            ;");
            $this->execute('
                ALTER TABLE public.sy_pasienmasukpenunjang_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210421_004805_migrate_202104120_3713_sy_pasienmasukpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210421_004805_migrate_202104120_3713_sy_pasienmasukpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
