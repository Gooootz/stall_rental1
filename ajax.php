<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'update_account'){
	$save = $crud->update_account();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_category"){
	$save = $crud->save_category();
	if($save)
		echo $save;
}

if($action == "delete_category"){
	$delete = $crud->delete_category();
	if($delete)
		echo $delete;
}
if($action == "save_stall"){
	$save = $crud->save_stall();
	if($save)
		echo $save;
}
if($action == "delete_stall"){
	$save = $crud->delete_stall();
	if($save)
		echo $save;
}

if($action == "save_renter"){
	$save = $crud->save_renter();
	if($save)
		echo $save;
}
if($action == "delete_renter"){
	$save = $crud->delete_renter();
	if($save)
		echo $save;
}
if($action == "get_tdetails"){
	$get = $crud->get_tdetails();
	if($get)
		echo $get;
}

if($action == "save_payment"){
	$save = $crud->save_payment();
	if($save)
		echo $save;
}
if($action == "delete_payment"){
	$save = $crud->delete_payment();
	if($save)
		echo $save;
}

if (isset($_GET['action']) && $_GET['action'] == 'check_stalls') {
    include 'db_connect.php';

    $stall = $conn->query("SELECT * FROM stalls WHERE id NOT IN (SELECT stall_id FROM tenants WHERE status = 1)");
    $response = ['available' => $stall->num_rows > 0];
    
    echo json_encode($response);
    exit;
}

ob_end_flush();
?>
