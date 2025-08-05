<?php
// Aktifkan error log saat debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Set header JSON
header('Content-Type: application/json');

// Koneksi ke database
$con = mysqli_connect('db', 'root', 'root', 'kohk7173_e-learning') or die(json_encode(["error" => "Connection failed"]));

$request = $_REQUEST;

// Kolom-kolom untuk sorting
$col = array(
    0   => 'id',
    1   => 'nis',
    2   => 'nama_lengkap',
    3   => 'alamat',
    4   => 'jenis_kelamin'
);

// Ambil total data tanpa filter
$sql = "SELECT * FROM siswa WHERE th_keluar = '9999'";
$query = mysqli_query($con, $sql);
$totalData = mysqli_num_rows($query);
$totalFilter = $totalData;

// Query untuk pencarian
$sql = "SELECT * FROM siswa WHERE th_keluar = '9999'";

if (!empty($request['search']['value'])) {
    $search = mysqli_real_escape_string($con, $request['search']['value']);
    $sql .= " AND (nis LIKE '%$search%' ";
    $sql .= " OR nama_lengkap LIKE '%$search%' ";
    $sql .= " OR jenis_kelamin LIKE '%$search%' ";
    $sql .= " OR alamat LIKE '%$search%') ";
}

// Hitung total setelah filter
$query = mysqli_query($con, $sql);
$totalFilter = mysqli_num_rows($query);

// Sorting dan limit
$order_column_index = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 0;
$order_direction = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'asc';
$order_column = isset($col[$order_column_index]) ? $col[$order_column_index] : 'id';

$sql .= " ORDER BY $order_column $order_direction LIMIT {$request['start']}, {$request['length']}";

$query = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_array($query)) {
    $subdata = array();
    $subdata[] = $row['nis'];
    $subdata[] = $row['nama_lengkap'];
    $subdata[] = $row['alamat'];
    $subdata[] = $row['jenis_kelamin'];
    $subdata[] = '<a href="?module=m_siswa&act=view_data&id=' . $row['id'] . '" class="btn-sm btn-info"><i class="fas fa-info">&nbsp;</i>Detail</a> 
                  | <a href="?module=m_siswa&act=edit_data&id=' . $row['id'] . '" class="btn-sm btn-warning"><i class="fas fa-edit">&nbsp;</i></a>';
    $data[] = $subdata;
}

// Buat JSON response
$json_data = array(
    "draw"            => intval($request['draw']),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFilter),
    "data"            => $data
);

echo json_encode($json_data);
?>
