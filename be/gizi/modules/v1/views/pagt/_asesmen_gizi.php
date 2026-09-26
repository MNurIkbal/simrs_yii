<ol type="a">
    <li>
        <b>BB: </b> <?=$cppt['bb']?> kg, <b>TB: </b><?=$cppt['tb']?> cm, <b>IMT (Dewasa)</b> <?=$cppt['imt_dewasa']?> kg/m<sup>2</sup>, <b>LILA: </b> <?=$cppt['lila']?> cm, <b>IMT/U<sup>2</sup> (Anak): </b><?=$cppt['imt_anak']?> kg/m<sup>2</sup>, <b>BB/U Anak: </b> <?=$cppt['bb_anak']?> kg, <b>ULNA: </b> <?=$cppt['ulna']?> cm, <b>Status Gizi: </b> <?=ucfirst(str_replace('_', ' ',$cppt['status_gizi']))?>
    </li>
    <li>
        <b>Hasil Lab Abnormal</b><br>
        <b>Profil Lemak</b> <br>
        <b>Trigliserida: </b> <?=$cppt['trigliserida']?> mg/dl, <b>HDL: </b><?=$cppt['hdl']?> mg/dl, <b>LDL: </b><?=$cppt['ldl']?> mg/dl, <b>Kolesterol: </b><?=$cppt['kolesterol']?> mg/dl <br><br>
        <b>Fungsi Ginjal</b>
        <b>Ureum: </b> <?=$cppt['ureum']?> mg/dl, <b>Kreatinin: </b> <?=$cppt['kreatinin']?> mg/dl, <b>Kalium: </b> <?=$cppt['kalium']?> meq/l, <b>Natrium: </b> <?=$cppt['natrium']?> mg/dl, <b>Kalsium: </b> <?=$cppt['kalsium']?> mg/dl, <b>Phospor: </b> <?=$cppt['phospor']?> mg/dl <br><br>
        <b>Fungsi Hati: </b> <br>
        <b>SGOT: </b> <?=$cppt['sgot']?> u/l, <b>SGPT: </b> <?=$cppt['sgpt']?> u/l, <b>Biliurbin: </b> <?=$cppt['bilirubin']?> mg/dl <br><br>
        <b>Lain lain: </b>
        <b>GD Sewaktu: </b> <?=$cppt['gd_sewaktu']?> mg/dl, <b>GD Puasa: </b> <?=$cppt['gd_puasa']?> mg/dl, <b>HBA1C: </b> <?=$cppt['hba1c']?>%, <b>2 Jam PP: </b> <?=$cppt['dua_jam_pp']?> mg/dl, <b>HB: </b> <?=$cppt['hb']?> g/dl, <b>Albumin: </b> <?=$cppt['albumin']?> g/dl, <b>HT: </b> <?=$cppt['ht']?>%<br>
    </li>
    <li>
        <b>Pemeriksaan Fisik: </b> <?=str_replace(',', ', ', $cppt['pemeriksaan_fisik'])?> , <b>Tekanan Darah: <?=$cppt['tekanan_darah']?> mm/Hg</b>
    </li>
    <li>
        <b>Gangguan Pencernaan: </b> <?=str_replace('_', ' ', str_replace(',', ', ', $cppt['gangguan_pencernaan']))?>
    </li>
    <li>
        <b>Food recall 1x24 jam yang lalu:</b> <br>
        <b>Makan Pagi</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['makan_pagi_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['makan_pagi_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['makan_pagi_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['makan_pagi_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['makan_pagi_buah'])?>, <b>Energi: </b> <?=$cppt['makan_pagi_energi']?>, <b>Protein: </b> <?=$cppt['makan_pagi_protein']?>, <b>Lemak: </b> <?=$cppt['makan_pagi_lemak']?>, <b>KH: </b> <?=$cppt['makan_pagi_kh']?> <br><br>

        <b>Selingan Pagi</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['selingan_pagi_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['selingan_pagi_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['selingan_pagi_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['selingan_pagi_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['selingan_pagi_buah'])?>, <b>Energi: </b> <?=$cppt['selingan_pagi_energi']?>, <b>Protein: </b> <?=$cppt['selingan_pagi_protein']?>, <b>Lemak: </b> <?=$cppt['selingan_pagi_lemak']?>, <b>KH: </b> <?=$cppt['selingan_pagi_kh']?> <br><br>

        <b>Makan Siang</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['makan_siang_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['makan_siang_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['makan_siang_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['makan_siang_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['makan_siang_buah'])?>, <b>Energi: </b> <?=$cppt['makan_siang_energi']?>, <b>Protein: </b> <?=$cppt['makan_siang_protein']?>, <b>Lemak: </b> <?=$cppt['makan_siang_lemak']?>, <b>KH: </b> <?=$cppt['makan_siang_kh']?> <br><br>

        <b>Selingan Sore</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['selingan_sore_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['selingan_sore_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['selingan_sore_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['selingan_sore_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['selingan_sore_buah'])?>, <b>Energi: </b> <?=$cppt['selingan_sore_energi']?>, <b>Protein: </b> <?=$cppt['selingan_sore_protein']?>, <b>Lemak: </b> <?=$cppt['selingan_sore_lemak']?>, <b>KH: </b> <?=$cppt['selingan_sore_kh']?> <br><br>

        <b>Makan Malam</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['makan_malam_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['makan_malam_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['makan_malam_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['makan_malam_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['makan_malam_buah'])?>, <b>Energi: </b> <?=$cppt['makan_malam_energi']?>, <b>Protein: </b> <?=$cppt['makan_malam_protein']?>, <b>Lemak: </b> <?=$cppt['makan_malam_lemak']?>, <b>KH: </b> <?=$cppt['makan_malam_kh']?> <br><br>

        <b>Selingan Malam</b><br>
        <b>Makanan Pokok: </b> <?=str_replace('_', '', $cppt['selingan_malam_pokok'])?>, <b>Hewani: </b> <?=str_replace('_', '', $cppt['selingan_malam_hewani'])?>, <b>Nabati: </b> <?=str_replace('_', '', $cppt['selingan_malam_nabati'])?>, <b>Sayur: </b> <?=str_replace('_', '', $cppt['selingan_malam_sayur'])?>, <b>Buah: </b> <?=str_replace('_', '', $cppt['selingan_malam_buah'])?>, <b>Energi: </b> <?=$cppt['selingan_malam_energi']?>, <b>Protein: </b> <?=$cppt['selingan_malam_protein']?>, <b>Lemak: </b> <?=$cppt['selingan_malam_lemak']?>, <b>KH: </b> <?=$cppt['selingan_malam_kh']?> <br>
        <br>
        <b>Total Asupan: </b><br>
        <b>Energi: </b> <?=$cppt['total_energi_diet']?> Kal, <b>Protein: </b> <?=$cppt['total_protein_diet']?> gr, <b>Lemak: </b> <?=$cppt['total_lemak_diet']?> gr, <b>KH: </b> <?=$cppt['total_kh_diet']?> gr,
        <br><br>
        <b>Alergi/Pantangan: </b> <?=$cppt['alergi']?><br>
        <b>Diet yang dijalankan: </b> <?=$cppt['diet_dijalankan']?><br>
        <b>Frekuensi Olah Raga: </b> <?=$cppt['olah_raga_hari']?> kali/hari, <?=$cppt['olah_raga_menit']?> menit<br>
        <b>Kebiasaan Merokok: </b> <?=$cppt['kebiasaan_merokok']?> batang/hari<br>
        <b>Aktifitas Fisik: </b> <?=ucfirst(str_replace('_', ' ', $cppt['aktifitas_fisik']))?> batang/hari<br>
    </li>
</ol>
