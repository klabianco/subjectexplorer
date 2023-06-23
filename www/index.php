<?php

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../..");
$dotenv->load();

// check if the url is "/api/analyze-activity"
if ($_SERVER['REQUEST_URI'] == '/api/analyze-activity') {
    // check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // check if the request body is not empty
        if (!empty(file_get_contents('php://input'))) {
            // get the request body
            $requestBody = $_POST;

            // check if the request body has a activity and subject
            if (isset($requestBody['activity']) && isset($requestBody['subject'])) {
                // require the SubjectExplorer class
                require_once '../src/SubjectExplorer.php';
                require_once '../src/Db.php';

                // create a new SubjectExplorer object
                $subjectExplorer = new SubjectExplorer();

                // set the activity and subject
                $subjectExplorer->activity = $requestBody['activity'];
                $subjectExplorer->subject = $requestBody['subject'];
                $subjectExplorer->grade = $requestBody['grade'];

                // get the response from OpenAI
                $response = $subjectExplorer->getResponseFromOpenAi();

                // check if the response is not empty
                if ($response != '') {
                    // return the response
                    echo json_encode(['response' => $response]);
                    $DB = new Db();
                    $DB->prepExec('INSERT INTO `activities` (`activity`, `subject`, `grade`, `response`,`added_date`) VALUES (:activity, :subject, :grade, :response,NOW())', [
                        'activity' => $requestBody['activity'],
                        'subject' => $requestBody['subject'],
                        'grade' => $requestBody['grade'],
                        'response' => $response
                    ]);
                } else {
                    // return an error
                    echo json_encode(['error' => 'There was an error analyzing the activity.']);
                }
            } else {
                // return an error
                echo json_encode(['error' => 'The request body is missing a description or subject.']);
            }
        } else {
            // return an error
            echo json_encode(['error' => 'The request body is empty.']);
        }
    } else {
        // return an error
        echo json_encode(['error' => 'The request method is not POST.']);
    }
    die;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Explorer - Discover Your Child's Learning</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!--script src="subject-explorer.js"></script-->
    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
                (c[a].q = c[a].q || []).push(arguments)
            };
            t = l.createElement(r);
            t.async = 1;
            t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "hnz5t9hnfz");
    </script>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-N60XK5LN7W"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-N60XK5LN7W');
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-259175413-3"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-259175413-3');
</script>


<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a href="/" class="navbar-brand">Subject Explorer</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!--div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="resources.html" class="nav-link">Resources</a></li>
                    <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
                </ul>
            </div-->
        </nav>
    </header>
    <main>
        <section class="jumbotron">
            <div class="container">
                <h1 class="display-4">Discover Your Child's Learning</h1>
                <p class="lead">Enter an activity your child did, and our AI will describe how they have learned specific concepts in any given subject.</p>
            </div>
        </section>
        <section class="activity-input">
            <div class="container">
                <form id="activity-form">
                    <label for="activity">Activity:</label>
                    <input type="text" id="activity" name="activity" class="form-control mb-2 mr-sm-2" placeholder="E.g., 'My child built a LEGO tower'" required>
                    <label for="grade">Developmental Grade:</label>
                    <select class="form-control mb-2 mr-sm-2" id="grade" name="grade">
                        <option value="">Select Developmental Grade...</option>
                        <option value="Toddler">Toddler</option>
                        <option value="Preschool">Preschool</option>
                        <option value="Transitional-Kindergarten">English</option>
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
            </div>
        </section>
        <section class="activity-results">
            <div class="container">
                <!-- display a waiting message that's hidden at first -->
                <div id="waiting" class="text-center" style="display: none;">
                    <p class="lead">Analyzing activity...</p>
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="results">
                    <!-- Results will be displayed here -->
                </div>
            </div>
        </section>
    </main>
    <footer class="mt-5 mb-5">
        <div class="container">
            <p class="text-center">&copy; 2023 Subject Explorer. <!--All rights reserved.--></p>
        </div>
    </footer>

    <script>
        // Handle form submission
        $(document).ready(function() {
            $("#activity-form").submit(function(event) {
                event.preventDefault();
                analyzeActivity();
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