<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>List of Renters</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_renter">
					<i class="fa fa-plus"></i> New Renter
				</a></span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Name</th>
									<th class="">Stall Rented</th>
									<th class="">Monthly Rate</th>
									<th class="">Outstanding Balance</th>
									<th class="">Last Payment</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$tenant = $conn->query("SELECT t.*,concat(t.lastname,', ',t.firstname,' ',t.middlename) as name,h.stall_no,h.price FROM tenants t inner join stalls h on h.id = t.stall_id where t.status = 1 order by h.stall_no desc ");
								while($row=$tenant->fetch_assoc()):
									$months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($row['date_in']." 23:59:59"));
									$months = floor(($months) / (30*60*60*24));
									$payable = $row['price'] * $months;
									$paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenant_id =".$row['id']);
									$last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$row['id']." order by unix_timestamp(date_created) desc limit 1");
									$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
									$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['date_created'])) : 'N/A';
									$outstanding = $payable - $paid;
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<?php echo ucwords($row['name']) ?>
									</td>
									<td class="">
										 <p> <b><?php echo $row['stall_no'] ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo number_format($row['price'],2) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo number_format($outstanding,2) ?></b></p>
									</td>
									<td class="">
										 <p><b><?php echo  $last_payment ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['id'] ?>" >View</button>
										<button class="btn btn-sm btn-outline-primary edit_renter" type="button" data-id="<?php echo $row['id'] ?>" >Edit</button>
										<button class="btn btn-sm btn-outline-danger delete_renter" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	
	 $('#new_renter').click(function () {
        // Check for available stalls before opening the modal
        $.ajax({
            url: 'ajax.php?action=check_stalls', // Replace with the correct server-side endpoint
            method: 'GET',
            success: function (response) {
                // Parse the response (assume the server returns {available: true/false})
                const result = JSON.parse(response);
                if (result.available) {
                    // If stalls are available, open the modal
                    uni_modal("New Tenant", "manage_renter.php", "mid-large");
                } else {
                    // If no stalls are available, prompt the user
                    alert("No stalls are available at the moment.");
                }
            },
            error: function () {
                // Handle errors (e.g., server unavailable)
                alert("An error occurred while checking stall availability.");
            },
        });
    });

	$('.view_payment').click(function(){
		uni_modal("Payments","view_payment.php?id="+$(this).attr('data-id'),"large")
		
	})
	$('.edit_renter').click(function(){
		uni_modal("Manage Renter Details","manage_renter.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_renter').click(function(){
		_conf("Are you sure to delete this Renter?","delete_renter",[$(this).attr('data-id')])
	})
	
	function delete_renter($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_renter',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>