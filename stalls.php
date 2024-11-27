<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <!-- FORM Panel -->
            <div class="col-md-4">
                <form action="" id="manage-stall">
                    <div class="card">
                        <div class="card-header">
                            Stall Form
                        </div>
                        <div class="card-body">
                            <div class="form-group" id="msg"></div>
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label class="control-label">Stall No</label>
                                <input type="text" class="form-control" name="stall_no" required="">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Category</label>
                                <select name="category_id" class="custom-select" required>
                                    <?php 
                                    $categories = $conn->query("SELECT * FROM categories order by name asc");
                                    if($categories->num_rows > 0):
                                    while($row = $categories->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                        <option selected="" value="" disabled="">Please check the category list.</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea name="description" cols="30" rows="4" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Price</label>
                                <input type="number" class="form-control text-right" name="price" step="any" required="">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                                    <button class="btn btn-sm btn-default col-sm-3" type="reset">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- FORM Panel -->

            <!-- Table Panel -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <b>Stall List</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Stall</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $stall = $conn->query("SELECT h.*,c.name as cname FROM stalls h inner join categories c on c.id = h.category_id order by id asc");
                                while($row = $stall->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td>
                                            <p>Stall #: <b><?php echo $row['stall_no'] ?></b></p>
                                            <p><small>Stall Type: <b><?php echo $row['cname'] ?></b></small></p>
                                            <p><small>Description: <b><?php echo $row['description'] ?></b></small></p>
                                            <p><small>Price: <b><?php echo number_format($row['price'], 2) ?></b></small></p>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_stall" type="button"
                                                data-id="<?php echo $row['id'] ?>"
                                                data-stall_no="<?php echo $row['stall_no'] ?>"
                                                data-description="<?php echo $row['description'] ?>"
                                                data-category_id="<?php echo $row['category_id'] ?>"
                                                data-price="<?php echo $row['price'] ?>">Edit
											</button>
                                            <button class="btn btn-sm btn-danger delete_stall" type="button"
                                                data-id="<?php echo $row['id'] ?>">Delete
											</button>
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
    td {
        vertical-align: middle !important;
    }
    td p {
        margin: unset;
        padding: unset;
        line-height: 1em;
    }
</style>

<script>
    $(document).ready(function () {
        $('table').dataTable();

        $('#manage-stall').on('reset', function () {
            $('#msg').html('');
        });

		$('#manage-stall').submit(function(e){
			e.preventDefault();
			start_load();
			$('#msg').html('');

			var formData = new FormData(this);

			// Debugging: Log all form entries
			for (var pair of formData.entries()) {
				console.log(pair[0]+ ': ' + pair[1]);
			}

			$.ajax({
				url: 'ajax.php?action=save_stall',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				success: function(resp){
					if(resp == 1){
						alert_toast("Data successfully saved", 'success');
						setTimeout(function(){
							location.reload();
						}, 1500);
					} else if(resp == 2){
						$('#msg').html('<div class="alert alert-danger">Stall number already exists.</div>');
						end_load();
					}
				}
			});
		});


        $('.edit_stall').click(function () {
            start_load();
            var cat = $('#manage-stall');
            cat.get(0).reset();
            cat.find("[name='id']").val($(this).attr('data-id'));
            cat.find("[name='stall_no']").val($(this).attr('data-stall_no'));
            cat.find("[name='description']").val($(this).attr('data-description'));
            cat.find("[name='price']").val($(this).attr('data-price'));
            cat.find("[name='category_id']").val($(this).attr('data-category_id'));
            end_load();
        });

        $('.delete_stall').click(function () {
            _conf("Are you sure to delete this stall?", "delete_stall", [$(this).attr('data-id')]);
        });
    });

    function delete_stall($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_stall',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
