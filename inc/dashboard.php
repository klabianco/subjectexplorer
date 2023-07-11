<?php 
$jsInclude[] = "/r/js/dashboard.min.js?".date("YmdHis", filemtime($_SERVER['DOCUMENT_ROOT']."/r/js/dashboard.min.js"));
?>
<script>
    var userAuthToken = "<?php echo $MyUser->getAuthToken(); ?>";
</script>
<div class="container">
    <div class="row">
        <div class="col">
            <h2>Analyze An Activity</h2>
            <?php include "components/activity_form.php"; ?>
        </div>
    </div>

    <div class="row border-top mt-4 pt-4">
        <div class="col">
            <h2>Activities You've Added</h2>
            <div id="activity-list"></div>
        </div>
    </div>
</div>