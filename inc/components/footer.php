<footer class="mt-5 mb-5 border-top">
    <div class="container text-center mt-3">
        <div class="row mb-3">
            <div class="col-12">
                <a href="/advertise">Advertise With Us</a> |
                <a href="/terms-of-service">Terms Of Service</a> |
                <a href="/privacy-policy">Privacy Policy</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <button class="btn btn-secondary btn-feedback">Feedback</button>

                <form id="feedback-form" style="display: none;">
                    <div class="form-group">
                        <label for="feedback">Feedback:</label><br>
                        <textarea class="form-control" id="feedback" name="feedback" placeholder="Your feedback here... You can leave your email address if you want a reply." required></textarea><br>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12 small-font">
                &copy; 2023 Subject Explorer. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<script>
    // Handle form submission
    $(document).ready(function() {
        $("#activity-form").submit(function(event) {
            event.preventDefault();
            analyzeActivity();
        });

        $(".btn-feedback").click(function() {
            $(this).hide();
            $("#feedback-form").show();
        });

        $("#feedback-form").on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting normally

            var feedback = $('#feedback').val();

            $.ajax({
                type: "POST",
                url: "/api/feedback",
                data: {
                    feedback: feedback
                },
                success: function(data) {
                    console.log("Feedback submitted successfully");
                    // You might want to hide the form and show a thank you message here
                    $("#feedback-form").hide();
                    $(".btn-feedback").show().text("Thank you for your feedback!");
                },
                error: function(data) {
                    console.log("Error submitting feedback");
                    // Handle error here
                }
            });
        });
    });

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
                    "grade": grade
                },
                success: function(response) {
                    displayResults(response);
                    // enable the submit button
                    $("#submit-activity").prop("disabled", false);
                    // hide the waiting message
                    $("#waiting").hide();
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
</script>
</body>

</html>