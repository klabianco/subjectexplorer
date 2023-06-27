<?php

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../..");
$dotenv->load();

spl_autoload_register(function ($class_name) {
    include('../src/' . $class_name . '.php');
});

$Db = new Db();
$MyUser = new User();
$MyUser->setDb($Db);

$auth0 = new \Auth0\SDK\Auth0([
    'domain' => $_SERVER['AUTH0_DOMAIN'],
    'clientId' => $_SERVER['AUTH0_CLIENT_ID'],
    'clientSecret' => $_SERVER['AUTH0_CLIENT_SECRET'],
    'cookieSecret' => $_SERVER['AUTH0_COOKIE_SECRET']
]);

$session = $auth0->getCredentials();

$siteName = "Subject Explorer";
$pageDescription = '';

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

    $DB = new Db();
    $DB->prepExec('INSERT INTO `feedback` (`feedback`,`added_date`) VALUES (:feedback,NOW())', [
        'feedback' => $feedback
    ]);
    die;
} else if ($_SERVER['REQUEST_URI'] == '/privacy-policy') {
    $mainContent = "privacy-policy";
    $pageTitle = "Privacy Policy";
} else if ($_SERVER['REQUEST_URI'] == '/terms-of-service') {
    $mainContent = "terms-of-service";
    $pageTitle = "Terms of Service";
} else if ($_SERVER['REQUEST_URI'] == '/advertise') {
    $mainContent = "advertise";
    $pageTitle = "Advertise With Us";
    $pageDescription = "Partner with us to share your educational resources, services, and tools with a dedicated community of learners and educators.";
} else if ($_SERVER['REQUEST_URI'] == "/signin") {
    $auth0->clear();
    header("Location: " . $auth0->login($_SERVER['AUTH0_BASE_URL'] . "/api/login/callback"));
    exit;
} else {
    $mainContent = "homepage";
    $pageTitle = "Discover Your Child's Learning";
    $pageDescription = "Enter an activity your child did, and our AI will describe how they have learned specific concepts in any given subject.";
}

require_once '../inc/components/header.php';
require_once '../inc/' . $mainContent . '.php';
require_once '../inc/components/footer.php';
