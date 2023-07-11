<?php
$subjects = [
    'Art',
    'English Language Arts',
    'Foreign Language',
    'History',
    'Math',
    'Music',
    'Physical Education',
    'Science',
    'Social Studies'
];

sort($subjects);
?>

<form id="activity-form">
    <label for="activity">Activity:</label>
    <input type="text" id="activity" name="activity" class="form-control mb-2 mr-sm-2" placeholder="E.g., 'My child built a LEGO tower' (More detailed = better analysis)" required>
    <label for="grade">Developmental Grade:</label>
    <select class="form-control mb-2 mr-sm-2" id="grade" name="grade">
        <option value="">Select Developmental Grade...</option>
        <option value="Toddler">Toddler</option>
        <option value="Preschool">Preschool</option>
        <option value="Transitional-Kindergarten">Transitional Kindergarten</option>
        <option value="Kindergarten">Kindergarten</option>
        <option value="First">1st Grade</option>
        <option value="Second">2nd Grade</option>
        <option value="Third">3rd Grade</option>
        <option value="Fourth">4th Grade</option>
        <option value="Fifth">5th Grade</option>
        <option value="Sixth">6th Grade</option>
        <option value="Seventh">7th Grade</option>
        <option value="Eighth">8th Grade</option>
        <option value="Ninth">9th Grade</option>
        <option value="Tenth">10th Grade</option>
        <option value="Eleventh">11th Grade</option>
        <option value="Twelfth">12th Grade</option>
    </select>

    <h6 class="my-4">Select Subject(s)</h6>
    <div class="row">
        <?php
        $columns = array_chunk($subjects, ceil(count($subjects) / 3));
        foreach ($columns as $column) {
            echo '<div class="col-md-4">';
            foreach ($column as $subject) {
                echo '
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="' . $subject . '" id="' . str_replace(' ', '', $subject) . '">
          <label class="form-check-label" for="' . str_replace(' ', '', $subject) . '">
            ' . $subject . '
          </label>
        </div>
      ';
            }
            echo '</div>';
        }
        ?>
    </div>



    <button type="submit" id="submit-activity" class="btn btn-primary mb-2 mt-3">Analyze Activity</button>
</form>

<div class="activity-results">
    <!-- display a waiting message that's hidden at first -->
    <div id="waiting" class="text-center" style="display: none;">
        <p class="lead">Analyzing activity...</p>
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div id="results"></div>
</div>
<script>
    function getSelectedSubjects() {
        const selectedSubjects = [];
        const checkedBoxes = document.querySelectorAll('input[type=checkbox]:checked');

        checkedBoxes.forEach((checkbox) => {
            selectedSubjects.push(checkbox.value);
        });

        return selectedSubjects;
    }

    function analyzeActivity() {
        let activity = $("#activity").val();
        let subjects = getSelectedSubjects();
        let grade = $("#grade").val();

        if(subjects.length == 0) {
            alert("Please select at least one subject!");
            return;
        }

        // disable the submit button
        $("#submit-activity").prop("disabled", true);

        // remove the contents of the results div
        $("#results").empty();

        // show the hidden waiting message
        $("#waiting").show();

        if (activity && subjects.length > 0) {
            
            $.post({
                url: "/api/analyze-activity",

                data: {
                    "activity": activity,
                    "subjects": subjects,
                    "grade": grade,
                    "userAuthToken": userAuthToken
                },
                success: function(response) {
                    displayResults(response);
                    // enable the submit button
                    $("#submit-activity").prop("disabled", false);
                    // hide the waiting message
                    $("#waiting").hide();

                    if (userAuthToken != '') getActivities();
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + status + " " + error);
                    // enable the submit button
                    $("#submit-activity").prop("disabled", false);
                    // hide the waiting message
                    $("#waiting").hide();
                }
            });
        } else {
            alert("Please enter an activity!");
        }
    }

    function displayResults(data) {
        let resultsDiv = $("#results");
        resultsDiv.empty();
        // convert json response to an object
        let result = JSON.parse(data);

        var analysisButton = '<a href="/signin" class="btn btn-primary w-100">Sign In To Save Analysis</a>';

        if (userAuthToken != '') {
            analysisButton = '';
        } else {
            document.cookie = `resultId=${result.id}; path=/;`;
        }

        let resultHTML = `<div class="result card mb-3">
    <div class="card-body">
        <h3 class="card-title">Your Results</h3>
        <p class="card-text">${result.response}</p>
        ${analysisButton}
    </div>
</div>`;

        resultsDiv.append(resultHTML);
    }

    $(document).ready(function() {
        $("#activity-form").submit(function(event) {
            event.preventDefault();
            analyzeActivity();
        });
    });
</script>