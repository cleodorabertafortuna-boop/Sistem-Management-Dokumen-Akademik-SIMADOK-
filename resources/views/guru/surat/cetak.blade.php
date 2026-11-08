<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Keterangan - {{ $surat->siswa->nama }}</title>
    <style>
        @page {
            margin: 25px 40px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            line-height: 1.5;
        }

        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 4px;
        }

        .kop-logo {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 65px;
        }

        .kop-text {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        .kop-text h1 {
            font-size: 15px;
            margin: 0;
        }

        .kop-text h2 {
            font-size: 14px;
            margin: 0;
        }

        .kop-text p {
            font-size: 10px;
            margin: 2px 0 0;
        }

        .npsn {
            text-align: center;
            font-size: 11px;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        .judul {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul h3 {
            text-decoration: underline;
            margin: 0;
            font-size: 14px;
        }

        .judul p {
            margin: 2px 0 0;
            font-size: 12px;
        }

        table.biodata {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table.biodata td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 12px;
        }

        table.biodata td.label {
            width: 220px;
        }

        table.biodata td.titik {
            width: 15px;
        }

        p.isi {
            text-align: justify;
            margin: 14px 0;
            font-size: 12px;
        }

        .ttd {
            width: 260px;
            margin-left: auto;
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        .ttd .spasi {
            height: 70px;
        }
    </style>
</head>

<body>

    <div class="kop">
        <div class="kop-logo">

            <img src="{{ public_path('images/logo-sekolah.png') }}">
        </div>
        <div class="kop-text">
            <h1>PEMERINTAH PROVINSI JAWA TENGAH</h1>
            <h1>DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
            <h2>SEKOLAH MENENGAH KEJURUAN NEGERI 5 SURAKARTA</h2>
            <p>Jalan L.U Adi Sucipto Nomor 42 Laweyan, Surakarta Kode Pos 57143</p>
            <p>Telepon 0271-713916 Faksimile 0271-727068 Surat Elektronik info@smkn5solo.net</p>
        </div>
    </div>
    <p class="npsn">NPSN : 20328109</p>

    <div class="judul">
        <h3>SURAT KETERANGAN</h3>
        <p>No Surat: {{ $surat->nomor_surat }}</p>
    </div>

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="biodata">
        <tr>
            <td class="label">Nama</td>
            <td class="titik">:</td>
            <td>Sugiyono, S.Pd., M.Si</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="titik">:</td>
            <td>19751211 200501 1 005</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="titik">:</td>
            <td>Kepala Sekolah</td>
        </tr>
    </table>

    <p>Memberikan rekomendasi kepada:</p>

    <table class="biodata">
        <tr>
            <td class="label">a. Nama Siswa</td>
            <td class="titik">:</td>
            <td>{{ $surat->siswa->nama }}</td>
        </tr>
        <tr>
            <td class="label">b. Tempat, Tanggal Lahir</td>
            <td class="titik">:</td>
            <td>{{ $surat->tempat_lahir }}, {{ $surat->tanggal_lahir->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">c. NIS</td>
            <td class="titik">:</td>
            <td>{{ $surat->siswa->nis }}</td>
        </tr>
        <tr>
            <td class="label">d. Jenis Kelamin</td>
            <td class="titik">:</td>
            <td>{{ $surat->jenis_kelamin }}</td>
        </tr>
        <tr>
            <td class="label">e. NISN</td>
            <td class="titik">:</td>
            <td>{{ $surat->nisn }}</td>
        </tr>
        <tr>
            <td class="label">f. Nama Orang Tua (Ayah)</td>
            <td class="titik">:</td>
            <td>{{ $surat->nama_ayah }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;(Ibu)</td>
            <td class="titik">:</td>
            <td>{{ $surat->nama_ibu }}</td>
        </tr>
        <tr>
            <td class="label">g. Alamat</td>
            <td class="titik">:</td>
            <td>{{ $surat->alamat }}</td>
        </tr>
    </table>

    <p class="isi">
        Siswa tersebut di atas adalah benar-benar siswa SMK Negeri 5 Surakarta pada Tahun Ajaran
        {{ $surat->tanggal_surat->year }}/{{ $surat->tanggal_surat->year + 1 }} kelas {{ $surat->siswa->kelas }}.
    </p>

    <p class="isi">
        Menurut data selama menjadi siswa, yang bersangkutan termasuk siswa berprestasi akademik dan
        berkelakuan baik, serta memiliki motivasi yang tinggi untuk belajar.
    </p>

    <p class="isi">
        Demikian Surat Keterangan ini dibuat untuk melengkapi persyaratan administrasi
        <strong>{{ $surat->tujuan_pengajuan }}</strong>.
    </p>

    <div class="ttd">
        <p>Surakarta, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
        <div class="spasi"></div>
        <p>
            <strong>Sugiyono, S.Pd., M.Si</strong><br>
            Pembina Utama Muda / IV-c<br>
            NIP. 19751211 200501 1 005
        </p>
    </div>

</body>

</html>