<?php
$host = "172.31.202.205";
$dbname = "bjbs_skenario_test";
$username = "bjbs_skenario_test_user";
$password = "Bjbs2024!!!";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; 
}

$stmt = $conn->query("SELECT * FROM test_steps");
$test_steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Skenario Test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="css/export.css"> 
</head>
<body>
    <div class="container">
        <div class="text-left mb-3">
            <a href="dashboard" class="btn btn-light btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="header-title">Export Skenario Test</div>
        <div class="data-tables datatable-dark">
            <table id="mauexport" class="table table-bordered table-striped" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Expected Result</th>
                        <th>Actual Result</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($test_steps)): ?>
                        <?php $i = 1; foreach ($test_steps as $step): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $step['title']; ?></td>
                            <td><?php echo $step['expected_result']; ?></td>
                            <td><?php echo $step['actual_result']; ?></td>
                            <td>
                                <?php
                                $response_status = [
                                    '1' => 'Success',
                                    '2' => 'Review',
                                    '3' => 'Bugs',
                                    '4' => 'Failed',
                                    '5' => 'Pending'
                                ];
                                echo $response_status[$step['status']] ?? 'Unknown'; 
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">No data available in table</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#mauexport').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
    </script>
</body>
</html>