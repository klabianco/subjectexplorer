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
                //require_once '../src/Ican.php';

                // create a new SubjectExplorer object
                $subjectExplorer = new SubjectExplorer();

                // set the activity and subject
                $subjectExplorer->activity = $requestBody['activity'];
                $subjectExplorer->subject = $requestBody['subject'];
                $subjectExplorer->grade = $requestBody['grade'];


                /*
                $Ican = new Ican($requestBody['grade'], $requestBody['subject']);
                $statements = $Ican->getStatements();
                $subjectExplorer->statements = $statements;
                */

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
} else if ($_SERVER['REQUEST_URI'] == '/api/feedback') {
    $feedback = $_POST['feedback'];

    require_once '../src/Db.php';
    $DB = new Db();
    $DB->prepExec('INSERT INTO `feedback` (`feedback`,`added_date`) VALUES (:feedback,NOW())', [
        'feedback' => $feedback
    ]);
    die;
} else if($_SERVER['REQUEST_URI'] == '/advertise'){
    $mainContent = "advertise";
    $pageTitle = "Advertise With Us";
} else {
    $mainContent = "homepage";
    $pageTitle = "Discover Your Child's Learning";
}

require_once '../inc/components/header.php';
require_once '../inc/'.$mainContent.'.php';
require_once '../inc/components/footer.php';