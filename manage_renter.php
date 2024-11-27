<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM tenants where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-renter">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="row form-group">
			<div class="col-md-4">
				<label for="" class="control-label">Last Name</label>
				<input type="text" class="form-control" name="lastname"  value="<?php echo isset($lastname) ? $lastname :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">First Name</label>
				<input type="text" class="form-control" name="firstname"  value="<?php echo isset($firstname) ? $firstname :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Middle Name</label>
				<input type="text" class="form-control" name="middlename"  value="<?php echo isset($middlename) ? $middlename :'' ?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Email</label>
				<input type="email" class="form-control" name="email"  value="<?php echo isset($email) ? $email :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Contact #</label>
				<input type="text" class="form-control" name="contact"  value="<?php echo isset($contact) ? $contact :'' ?>" required>
			</div>
			
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Stall</label>
				<select name="stall_id" id="" class="custom-select select2">
					<option value=""></option>
					
					<?php 
					// $stall = $conn->query("SELECT * FROM stalls WHERE id NOT IN (SELECT stall_id FROM tenants WHERE status = 1) " . (isset($stall_id) ? " OR id = $stall_id" : ""));
					$stall = $conn->query("SELECT h.*,c.name as cname FROM stalls h inner join categories c on c.id = h.category_id order by id asc");
					if ($stall->num_rows > 0): // Check if any stalls are available
						while ($row = $stall->fetch_assoc()):
					?>
							<option value="<?php echo $row['id']; ?>" <?php echo isset($stall_id) && $stall_id == $row['id'] ? 'selected' : ''; ?>>
								<?php echo $row['stall_no']; ?>: <?php echo $row['cname'] ?>
							</option>
					<?php 
						endwhile; 
					else: 
					?>
						<option value="" disabled>No stalls available</option>
					<?php endif; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Registration Date</label>
				<input type="date" class="form-control" name="date_in"  value="<?php echo isset($date_in) ? date("Y-m-d",strtotime($date_in)) :'' ?>" required>
			</div>
		</div>
	</form>
</div>
<script>
	
	$('#manage-renter').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_renter',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved.",'success')
						setTimeout(function(){
							location.reload()
						},1000)
				}
			}
		})
	})
</script>