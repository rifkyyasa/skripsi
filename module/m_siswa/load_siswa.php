<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi
$con = mysqli_connect('db', 'root', 'root', 'kohk_7173_e-learning');
if (!$con) {
    echo json_encode(["error" => "Koneksi gagal: " . mysqli_connect_error()]);
    exit;
}

// Ambil parameter dari datatables
$request = $_REQUEST;
$col = array(
    0 => 'id',
    1 => 'nis',
    2 => 'nama_lengkap',
    3 => 'alamat',
    4 => 'jenis_kelamin'
);

// Hitung total data
$sql = "SELECT * FROM siswa WHERE th_keluar = '9999'";
$query = mysqli_query($con, $sql) or die(json_encode(["error" => mysqli_error($con)]));
$totalData = mysqli_num_rows($query);
$totalFilter = $totalData;

// Pencarian
$sql = "SELECT * FROM siswa WHERE th_keluar = '9999'";
if (!empty($request['search']['value'])) {
    $search = $request['search']['value'];
    $sql .= " AND (nis LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR alamat LIKE '%$search%' OR jenis_kelamin LIKE '%$search%')";
}

$query = mysqli_query($con, $sql) or die(json_encode(["error" => mysqli_error($con)]));
$totalFilter = mysqli_num_rows($query);

// Order dan limit
$columnIndex = intval($request['order'][0]['column']);
$orderDir = $request['order'][0]['dir'];
$start = intval($request['start']);
$length = intval($request['length']);
$sql .= " ORDER BY " . $col[$columnIndex] . " $orderDir LIMIT $start, $length";

$query = mysqli_query($con, $sql) or die(json_encode(["error" => mysqli_error($con)]));
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $subdata = [];
    $subdata[] = $row['nis'];
    $subdata[] = $row['nama_lengkap'];
    $subdata[] = $row['alamat'];
    $subdata[] = $row['jenis_kelamin'];
    $subdata[] = '<a href="?module=m_siswa&act=view_data&id=' . $row['id'] . '" class="btn-sm btn-info"><i class="fas fa-info"></i> Detail</a> | <a href="?module=m_siswa&act=edit_data&id=' . $row['id'] . '" class="btn-sm btn-warning"><i class="fas fa-edit"></i></a>';
    $data[] = $subdata;
}

// Format output
$json_data = [
    "draw" => intval($request['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFilter),
    "data" => $data
];

// Kirim JSON ke datatables
echo json_encode($json_data);
?>
