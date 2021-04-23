<?php
session_start();
$conn = include('./configs/database.php');

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
}

if (isset($_GET['logout']) == 1) {
    session_unset();
    session_destroy();
    header('Location: login.php');
}

$uid = $_SESSION['user']['uid'];

if (isset($_GET['create']) == 1) {
    $mname = $_GET['medicine-name'];
    $mdosage = $_GET['medicine-dosage'];
    $mfreq = $_GET['medicine-frequency'];

    header('Location: account.php');
    if (strlen($mname) > 0 && strlen($mdosage) > 0 && strlen($mfreq) > 0) {
        $sql = 'insert into medicines(uid,name,dosage,frequency) values(?,?,?,?)';
        $medicine = $conn->prepare($sql);
        $medicine->execute([$uid,$mname,$mdosage,$mfreq]);
        $_SESSION['mess'] = "<h6 class='btn btn-success p-2' style='width: 100%;'>Successfully added medicine</h6>";
    } else {
        $_SESSION['mess'] = "<h6 class='btn btn-danger p-2' style='width: 100%;'>Inputs cant be empty</h6>";
    }

}



if (isset($_SESSION['most_recent_activity']) &&
    (time() -   $_SESSION['most_recent_activity'] > 1)) {

    //600 seconds = 10 minutes
    unset($_SESSION['mess']);

}
$_SESSION['most_recent_activity'] = time(); // the start of the session.

if (isset($_GET['delete']) == 1) {
    $mid = $_GET['mid'];
    $sql = "delete from medicines where mid = ?";
    $medicine = $conn->prepare($sql);
    $medicine->execute([$mid]);
    header('Location: account.php');
    $_SESSION['mess'] = "<h6 class='btn btn-success p-2' style='width: 100%;'>Successfully deleted medicine</h6>";
}

if (isset($_GET['delete']) == 2) {
    $sql = "delete from medicines where uid = ?";
    $medicine = $conn->prepare($sql);
    $medicine->execute([$uid]);
    header('Location: account.php');
    $_SESSION['mess'] = "<h6 class='btn btn-success p-2' style='width: 100%;'>Successfully deleted all medicines</h6>";
}

if (isset($_GET['startEdit'])) {
    $mname = $_GET['medicine-name-edit'];
    $mdosage = $_GET['medicine-dosage-edit'];
    $mfreq = $_GET['medicine-frequency-edit'];
    $mid = $_GET['startEdit'];
    header('Location: account.php');
    if (strlen($mname) > 0 && strlen($mdosage) > 0 && strlen($mfreq) > 0) {
        $sql = 'update medicines set name = ?, dosage = ?, frequency = ? where mid = ?';
        $medicine = $conn->prepare($sql);
        $medicine->execute([$mname,$mdosage,$mfreq,$mid]);
        $_SESSION['mess'] = "<h6 class='btn btn-success p-2' style='width: 100%;'>Successfully edited medicine</h6>";
    } else {
        $_SESSION['mess'] = "<h6 class='btn btn-danger p-2' style='width: 100%;'>Inputs cant be empty</h6>";
    }
}

function getMedicines($uid) {
    global $conn;
    $sql = 'select * from medicines where uid = ?';
    $medicines = $conn->prepare($sql);
    $medicines->execute([$uid]);
    return $medicines->fetchAll();
}

$medicines = getMedicines($uid);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php include('./includes/head.php') ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <title>Add Medicine</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="account.php">Medicine</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="?logout=1">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
if (isset($_SESSION['mess'])) {
    echo $_SESSION['mess'];
}
?>


<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Manage <b>Medicines</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <button id="addMedicineModal-btn" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add Medicine</span></button>
                        <button id="deleteMedicineModal-btn" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></button>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                        </th>
                        <th>Medicine Name</th>
                        <th>Medicine Dose</th>
                        <th>Medicine Frequency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($medicines as $medicine) { ?>
                    <tr>
                        <td></td>
                            <td><?php echo $medicine['name'] ?></td>
                            <td><?php echo $medicine['dosage'] ?></td>
                            <td><?php echo $medicine['frequency'] ?></td>
                        <td>
                            <a id="editMedicineModal-btn" href="?edit=<?php echo $medicine['mid']; ?>" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                            <a href="<?php echo "?delete=1&mid=$medicine[mid]" ?>" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Add Medicine Modal -->
<div id="addMedicineModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="account.php" method="GET">
                <div class="modal-header">
                    <h4 class="modal-title">Add Medicine</h4>
                    <button type="button" class="close btn-danger" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Medicine Name</label>
                        <input type="hidden" name="create" value="1" class="form-control" required>
                        <input type="text" name="medicine-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Medicine Dose</label>
                        <input type="number" name="medicine-dosage" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Medicine Frequency</label>
                        <input type="number" name="medicine-frequency" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" style="width: 100%;" class="btn btn-success" value="Add">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal HTML -->
<div id="editMedicineModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="account.php" method="GET">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Medicine</h4>
                    <button type="button" class="close btn-danger" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Medicine Name</label>
                        <input type="hidden" name="startEdit" value="<?php echo $_GET['edit'] ?>" class="form-control" required>
                        <input type="text" name="medicine-name-edit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Medicine Dose</label>
                        <input type="number" name="medicine-dosage-edit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Medicine Frequency</label>
                        <input type="number" name="medicine-frequency-edit" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" style="width: 100%;" class="btn btn-success" value="Submit Edit">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal HTML -->
<div id="deleteMedicineModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h4 class="modal-title">Delete Medicines</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete all these Medicines?</p>
                    <p class="text-warning"><small>This action cannot be undone.</small></p>
                </div>
                <form action="account.php" method="POST" class="modal-footer">
                   <button type="submit" name="delete" value="2" class="btn btn-danger mb-2 mr-2">Delete</button>
                </form>
            </form>
        </div>
    </div>
</div>

</body>
</html>


<style>
    body {
        color: #566787;
        background: #f5f5f5;
        font-family: 'Varela Round', sans-serif;
        font-size: 13px;
    }
    .table-responsive {
        margin: 30px 0;
    }
    .table-wrapper {
        background: #fff;
        padding: 20px 25px;
        border-radius: 3px;
        min-width: 1000px;
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
    .table-title {
        padding-bottom: 15px;
        background: #435d7d;
        color: #fff;
        padding: 16px 30px;
        min-width: 100%;
        margin: -20px -25px 10px;
        border-radius: 3px 3px 0 0;
    }
    .table-title h2 {
        margin: 5px 0 0;
        font-size: 24px;
    }
    .table-title .btn-group {
        float: right;
    }
    .table-title .btn {
        color: #fff;
        float: right;
        font-size: 13px;
        border: none;
        min-width: 50px;
        border-radius: 2px;
        border: none;
        outline: none !important;
        margin-left: 10px;
    }
    .table-title .btn i {
        float: left;
        font-size: 21px;
        margin-right: 5px;
    }
    .table-title .btn span {
        float: left;
        margin-top: 2px;
    }
    table.table tr th, table.table tr td {
        border-color: #e9e9e9;
        padding: 12px 15px;
        vertical-align: middle;
    }
    table.table tr th:first-child {
        width: 60px;
    }
    table.table tr th:last-child {
        width: 100px;
    }
    table.table-striped tbody tr:nth-of-type(odd) {
        background-color: #fcfcfc;
    }
    table.table-striped.table-hover tbody tr:hover {
        background: #f5f5f5;
    }
    table.table th i {
        font-size: 13px;
        margin: 0 5px;
        cursor: pointer;
    }
    table.table td:last-child i {
        opacity: 0.9;
        font-size: 22px;
        margin: 0 5px;
    }
    table.table td a {
        font-weight: bold;
        color: #566787;
        display: inline-block;
        text-decoration: none;
        outline: none !important;
    }
    table.table td a:hover {
        color: #2196F3;
    }
    table.table td a.edit {
        color: #FFC107;
    }
    table.table td a.delete {
        color: #F44336;
    }
    table.table td i {
        font-size: 19px;
    }
    table.table .avatar {
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 10px;
    }
    .pagination {
        float: right;
        margin: 0 0 5px;
    }
    .pagination li a {
        border: none;
        font-size: 13px;
        min-width: 30px;
        min-height: 30px;
        color: #999;
        margin: 0 2px;
        line-height: 30px;
        border-radius: 2px !important;
        text-align: center;
        padding: 0 6px;
    }
    .pagination li a:hover {
        color: #666;
    }
    .pagination li.active a, .pagination li.active a.page-link {
        background: #03A9F4;
    }
    .pagination li.active a:hover {
        background: #0397d6;
    }
    .pagination li.disabled i {
        color: #ccc;
    }
    .pagination li i {
        font-size: 16px;
        padding-top: 6px
    }
    .hint-text {
        float: left;
        margin-top: 10px;
        font-size: 13px;
    }
    /* Custom checkbox */
    .custom-checkbox {
        position: relative;
    }
    .custom-checkbox input[type="checkbox"] {
        opacity: 0;
        position: absolute;
        margin: 5px 0 0 3px;
        z-index: 9;
    }
    .custom-checkbox label:before{
        width: 18px;
        height: 18px;
    }
    .custom-checkbox label:before {
        content: '';
        margin-right: 10px;
        display: inline-block;
        vertical-align: text-top;
        background: white;
        border: 1px solid #bbb;
        border-radius: 2px;
        box-sizing: border-box;
        z-index: 2;
    }
    .custom-checkbox input[type="checkbox"]:checked + label:after {
        content: '';
        position: absolute;
        left: 6px;
        top: 3px;
        width: 6px;
        height: 11px;
        border: solid #000;
        border-width: 0 3px 3px 0;
        transform: inherit;
        z-index: 3;
        transform: rotateZ(45deg);
    }
    .custom-checkbox input[type="checkbox"]:checked + label:before {
        border-color: #03A9F4;
        background: #03A9F4;
    }
    .custom-checkbox input[type="checkbox"]:checked + label:after {
        border-color: #fff;
    }
    .custom-checkbox input[type="checkbox"]:disabled + label:before {
        color: #b8b8b8;
        cursor: auto;
        box-shadow: none;
        background: #ddd;
    }
    /* Modal styles */
    .modal .modal-dialog {
        max-width: 400px;
    }
    .modal .modal-header, .modal .modal-body, .modal .modal-footer {
        padding: 20px 30px;
    }
    .modal .modal-content {
        border-radius: 3px;
        font-size: 14px;
    }
    .modal .modal-footer {
        background: #ecf0f1;
        border-radius: 0 0 3px 3px;
    }
    .modal .modal-title {
        display: inline-block;
    }
    .modal .form-control {
        border-radius: 2px;
        box-shadow: none;
        border-color: #dddddd;
    }
    .modal textarea.form-control {
        resize: vertical;
    }
    .modal .btn {
        border-radius: 2px;
        min-width: 100px;
    }
    .modal form label {
        font-weight: normal;
    }
</style>


<script>
    $(document).ready(function() {
        $('#addMedicineModal-btn').click(function() {
            $('#addMedicineModal').css({'display': 'grid','place-items': 'center', 'opacity': 1});
            $('.close').click(function() {
                $('#addMedicineModal').css({'display': 'none', 'place-items': 'none', 'opacity': 0});
            })
        })
        $('#deleteMedicineModal-btn').click(function() {
            $('#deleteMedicineModal').css({'display': 'grid','place-items': 'center', 'opacity': 1});
            $('.close').click(function() {
                $('#deleteMedicineModal').css({'display': 'none', 'place-items': 'none', 'opacity': 0});
            })
        })
        if (window.location.href.includes('edit')) {
            $('#editMedicineModal').css({'display': 'grid','place-items': 'center', 'opacity': 1});
            $('.close').click(function() {
                $('#editMedicineModal').css({'display': 'none', 'place-items': 'none', 'opacity': 0});
                window.location.href = 'account.php';
            })
        }
    })
</script>
<script>
    $(document).ready(function() {
        $('.navbar-toggler').click(function() {
            $('.navbar-collapse').toggleClass('show');
        })
    })
</script>