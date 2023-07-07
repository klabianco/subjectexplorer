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
    <label for="activity">Subject:</label>
    <select class="form-control mb-2 mr-sm-2" id="subject" name="subject">
        <option value="English Language Arts">English Language Arts</option>
        <option value="Math">Math</option>
        <option value="Science">Science</option>
        <option value="Social Studies">Social Studies</option>
        <option value="History">History</option>
        <option value="Art">Art</option>
        <option value="Music">Music</option>
        <option value="Foreign Language">Foreign Language</option>
        <option value="Physical Education">Physical Education</option>
    </select>
    <button type="submit" id="submit-activity" class="btn btn-primary mb-2">Analyze Activity</button>
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
    function analyzeActivity() {
        let activity = $("#activity").val();
        let subject = $("#subject").val();
        let grade = $("#grade").val();

        // disable the submit button
        $("#submit-activity").prop("disabled", true);

        // remove the contents of the results div
        $("#results").empty();

        // show the hidden waiting message
        $("#waiting").show();

        if (activity && subject) {
            $.post({
                url: "/api/analyze-activity",

                data: {
                    "activity": activity,
                    "subject": subject,
                    "grade": grade,
                    "userAuthToken": userAuthToken
                },
                success: function(response) {
                    displayResults(response);
                    // enable the submit button
                    $("#submit-activity").prop("disabled", false);
                    // hide the waiting message
                    $("#waiting").hide();

                    if(userAuthToken != '') getActivities();
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
        result = JSON.parse(data);
        console.log('result', result);


        let resultHTML = `<div class="result card mb-3">
                    <div class="card-body">
                        <h3 class="card-title">Your Results</h3>
                        <p class="card-text">${result.response}</p>
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