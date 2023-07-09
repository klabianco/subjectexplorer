function getActivities() {
  $.post({
    url: "/api/get-activities",
    data: {
      userAuthToken: userAuthToken,
    },
    success: function (response) {
      displayActivities(response);
    },
    error: function (xhr, status, error) {
      console.error("Error: " + status + " " + error);
    },
  });
}

function displayActivities(response) {
  // decode json response
  let activities = JSON.parse(response);
  let html = "<p>You have not added any activities yet.</p>";

  if (activities.length != 0) {
    // loop through the activities and build the html
    html =
      "<table class='table table-striped'><thead><tr><th>Activity</th><th>Subject</th><th>Actions</th></tr></thead><tbody>";
    for (let i = 0; i < activities.length; i++) {
      // format the date nicely
      let formatAdded = new Date(activities[i].added_date).toLocaleDateString();
      let id = activities[i].id;
      html +=
        "<tr id='activity_" +
        id +
        "'><td>" +
        activities[i].activity +
        "<br><small class='text-muted'>Added " +
        formatAdded +
        "</small></td><td>" +
        activities[i].subject +
        "</td><td><a href='/activity/" +
        id +
        "' class='btn btn-sm btn-outline-secondary'>View</a> <button class='btn btn-sm btn-outline-danger delete-user-activity' data-id='" +
        id +
        "'>Delete</button></td></tr>";
    }
    html += "</tbody></table>";
  }

  $("#activity-list").html(html);
}

function deleteActivity(id) {
  // prompt if they're sure
  if (!confirm("Are you sure you want to delete this activity?")) return;

  $.post({
    url: "/api/delete-activity",
    data: {
      id: id,
      userAuthToken: userAuthToken,
    },
    success: function (response) {
      // remove the activity from the list
      $("#activity_" + id).remove();
    },
    error: function (xhr, status, error) {
      console.error("Error: " + status + " " + error);
    },
  });
}

getActivities();

$(document).ready(function () {
  $(document).on("click", ".delete-user-activity", function () {
    let id = $(this).data("id");
    deleteActivity(id);
  });
});
