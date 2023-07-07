<script>
    var userAuthToken = "<?php echo $MyUser->getAuthToken(); ?>";
</script>
<script>
    /* post to the /api/get-activities endpoint */

    function getActivities() {
        $.post({
            url: "/api/get-activities",
            data: {
                "userAuthToken": userAuthToken
            },
            success: function(response) {
                displayActivities(response);
            },
            error: function(xhr, status, error) {
                console.error("Error: " + status + " " + error);
            }
        });
    }

    function displayActivities(response){
        // decode json response 
        let activities = JSON.parse(response);
        let html = "<p>You have not added any activities yet.</p>";

        if(activities.length != 0){
            // loop through the activities and build the html
            html = "<table class='table table-striped'><thead><tr><th>Activity</th><th>Subject</th><th>Added Date</th></tr></thead><tbody>";
            for (let i = 0; i < activities.length; i++) {
                // format the date nicely
                activities[i].added_date = new Date(activities[i].added_date).toLocaleDateString();
                html += "<tr><td><a href='/activity/"+activities[i].id+"'>" + activities[i].activity + "</a></td><td>" + activities[i].subject + "</td><td>" + activities[i].added_date + "</td></tr>";
            }
            html += "</tbody></table>";
        }

        $("#activity-list").html(html);
    }

    getActivities();
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