<?php 
include 'db_connect.php'; 

// Fetch payment details if 'id' is set in the query string
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM payments WHERE id = " . intval($_GET['id']));
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $val) {
            $$k = $val; // Create dynamic variables
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="manage-payment">
        <input type="hidden" name="id" value="<?php echo isset($id) ? htmlspecialchars($id) : '' ?>">
        <div id="msg"></div>
        <div class="form-group">
            <label for="tenant_id" class="control-label">Renter</label>
            <select name="tenant_id" id="tenant_id" class="custom-select select2">
                <option value=""></option>
                <?php 
                $tenant = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM tenants WHERE status = 1 ORDER BY name ASC");
                while ($row = $tenant->fetch_assoc()) :
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($tenant_id) && $tenant_id == $row['id'] ? 'selected' : '' ?>>
                    <?php echo ucwords($row['name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group" id="details"></div>

        <div class="form-group">
            <label for="invoice" class="control-label">Invoice:</label>
            <input type="text" class="form-control" name="invoice" value="<?php echo isset($invoice) ? htmlspecialchars($invoice) : '' ?>">
        </div>
        <div class="form-group">
            <label for="amount" class="control-label">Amount Paid:</label>
            <input type="number" class="form-control text-right" step="any" name="amount" value="<?php echo isset($amount) ? htmlspecialchars($amount) : '' ?>">
        </div>
    </form>
</div>

<div id="details_clone" style="display: none">
    <div class='d'>
        <large><b>Details</b></large>
        <hr>
        <p>Renter: <b class="tname"></b></p>
        <p>Monthly Rental Rate: <b class="price"></b></p>
        <p>Outstanding Balance: <b class="outstanding"></b></p>
        <p>Total Paid: <b class="total_paid"></b></p>
        <p>Rent Started: <b class='rent_started'></b></p>
        <p>Payable Months: <b class="payable_months"></b></p>
        <hr>
    </div>
</div>

<script>
$(document).ready(function() {
    if ('<?php echo isset($id) ? 1 : 0 ?>' == 1) {
        $('#tenant_id').trigger('change'); 
    }
    
    $('.select2').select2({
        placeholder: "Please Select Here",
        width: "100%"
    });
    
    $('#tenant_id').change(function() {
        if ($(this).val() <= 0) return false;

        start_load();
        $.ajax({
            url: 'ajax.php?action=get_tdetails',
            method: 'POST',
            data: {id: $(this).val(), pid: '<?php echo isset($id) ? htmlspecialchars($id) : '' ?>'},
            success: function(resp) {
                if (resp) {
                    resp = JSON.parse(resp);
                    var details = $('#details_clone .d').clone();
                    details.find('.tname').text(resp.name);
                    details.find('.price').text(resp.price);
                    details.find('.outstanding').text(resp.outstanding);
                    details.find('.total_paid').text(resp.paid);
                    details.find('.rent_started').text(resp.rent_started);
                    details.find('.payable_months').text(resp.months);
                    $('#details').html(details);
                }
            },
            complete: function() {
                end_load();
            }
        });
    });

    $('#manage-payment').submit(function(e) {
        e.preventDefault();
        start_load();
        $('#msg').html('');
        
        $.ajax({
            url: 'ajax.php?action=save_payment',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved.", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    $('#msg').html('<div class="alert alert-danger">Error saving data.</div>');
                }
            }
        });
    });
});
</script>
