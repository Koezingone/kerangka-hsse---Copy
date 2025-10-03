<?php
$host = "2lq-kt.h.filess.io";
$user = "DBabsensi_detailflag";
$pass = "db717d817c8046fcd12a4c36cf1d82371ffe54e1";
$db   = "DBabsensi_detailflag";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
