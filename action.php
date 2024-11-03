<?php
include "koneksi.php";

function validasiInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['simpan'])) {
    if (empty($_POST['nama']) || empty($_POST['status']) || empty($_POST['gejala']) || empty($_POST['profesi']) || empty($_POST['id_obat']) || empty($_POST['id_pengurus']) || empty($_POST['tanggal']) || empty($_POST['jumlah'])) {
        echo "<script>alert('Semua field harus diisi!'); window.location='pasien.php';</script>";
        exit;
    }
    
    $nama = validasiInput($_POST['nama']);
    $status = validasiInput($_POST['status']);
    $gejala = validasiInput($_POST['gejala']);
    $profesi = validasiInput($_POST['profesi']);
    $id_obat = validasiInput($_POST['id_obat']);
    $id_pengurus = validasiInput($_POST['id_pengurus']);
    $tanggal = validasiInput($_POST['tanggal']);
    $jumlah = validasiInput($_POST['jumlah']);
    
    $cekStok = mysqli_query($koneksi, "SELECT stok FROM data_obat WHERE id_obat='$id_obat'");
    $stok = mysqli_fetch_assoc($cekStok)['stok'];
    
    if ($stok < $jumlah) {
        echo "<script>alert('Stok obat tidak mencukupi atau sudah habis. Stok saat ini: $stok'); window.location='pasien.php';</script>";
        exit;
    }
    
    $simpan = mysqli_query($koneksi, "INSERT INTO data_pasien (nama, status, gejala, profesi, id_obat, id_pengurus, tanggal, jumlah) VALUES ('$nama', '$status', '$gejala', '$profesi', '$id_obat', '$id_pengurus', '$tanggal', '$jumlah')");
    
    if ($simpan) {
        $queryStok = "UPDATE data_obat SET stok = stok - '$jumlah' WHERE id_obat='$id_obat'";
        mysqli_query($koneksi, $queryStok);
        echo "<script>alert('Data pasien berhasil disimpan'); document.location='pasien.php';</script>";
    } else {
        echo "<script>alert('Data pasien gagal disimpan: " . mysqli_error($koneksi) . "'); document.location='pasien.php';</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id_pasien = $_GET['hapus'];
    $query = "DELETE FROM data_pasien WHERE id_pasien = '$id_pasien'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Data pasien berhasil dihapus'); window.location.href = 'pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data pasien'); window.location.href = 'pasien.php';</script>";
    }
}

if (isset($_POST['edit'])) {
    $id_pasien = validasiInput($_POST['id_pasien']);
    $query = mysqli_query($koneksi, "SELECT * FROM data_pasien WHERE id_pasien = '$id_pasien'");
    $data_lama = mysqli_fetch_assoc($query);

    $nama = !empty($_POST['nama']) ? validasiInput($_POST['nama']) : $data_lama['nama'];
    $status = !empty($_POST['status']) ? validasiInput($_POST['status']) : $data_lama['status'];
    $gejala = !empty($_POST['gejala']) ? validasiInput($_POST['gejala']) : $data_lama['gejala'];
    $profesi = !empty($_POST['profesi']) ? validasiInput($_POST['profesi']) : $data_lama['profesi'];
    $id_obat = !empty($_POST['id_obat']) ? validasiInput($_POST['id_obat']) : $data_lama['id_obat'];
    $id_pengurus = !empty($_POST['id_pengurus']) ? validasiInput($_POST['id_pengurus']) : $data_lama['id_pengurus'];
    $tanggal = !empty($_POST['tanggal']) ? validasiInput($_POST['tanggal']) : $data_lama['tanggal'];
    $jumlah = !empty($_POST['jumlah']) ? validasiInput($_POST['jumlah']) : $data_lama['jumlah'];

    $cekStok = mysqli_query($koneksi, "SELECT stok FROM data_obat WHERE id_obat='$id_obat'");
    $stok = mysqli_fetch_assoc($cekStok)['stok'] + $data_lama['jumlah'];
    if ($stok < $jumlah) {
        echo "<script>alert('Stok obat tidak mencukupi. Stok saat ini: $stok'); window.location='pasien.php';</script>";
        exit;
    }

    $update = mysqli_query($koneksi, "UPDATE data_pasien SET nama='$nama', status='$status', gejala='$gejala', profesi='$profesi', id_obat='$id_obat', id_pengurus='$id_pengurus', tanggal='$tanggal', jumlah='$jumlah' WHERE id_pasien='$id_pasien'");

    if ($update) {
        if ($data_lama['jumlah'] != $jumlah) {
            $queryStok = "UPDATE data_obat SET stok = stok + '$data_lama[jumlah]' - '$jumlah' WHERE id_obat='$id_obat'";
            mysqli_query($koneksi, $queryStok);
        }
        echo "<script>alert('Data pasien berhasil diedit'); window.location='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal mengedit data pasien: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>
