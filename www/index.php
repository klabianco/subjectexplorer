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

if ($session !== null) {
    $MyUser->setAndLoadByEmail($session->user['email'], $session->user['given_name'], $session->user['family_name']);
}

$siteName = "Subject Explorer";
$pageDescription = '';

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', $requestPath);

// check if the url is "/api/analyze-activity"
if ($_SERVER['REQUEST_URI'] == '/api/analyze-activity') {
    require_once __DIR__ . '/../api/analyze-activity.php';
    die;
} else if ($_SERVER['REQUEST_URI'] == '/api/get-activities') {
    require_once __DIR__ . '/../api/get-activities.php';
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
} else if ($_SERVER['REQUEST_URI'] == '/signout') {
    header("Location: " . $auth0->logout($_SERVER['AUTH0_BASE_URL'] . "/"));
} else if ($_SERVER['REQUEST_URI'] == "/signin") {
    $auth0->clear();
    if (isset($_SERVER['HTTP_REFERER'])) {
        $previous_url = $_SERVER['HTTP_REFERER'];
        setcookie("previous_url", $previous_url, time() + (86400 * 30), "/");
    }
    header("Location: " . $auth0->login($_SERVER['AUTH0_BASE_URL'] . "/api/login/callback"));
    exit;
} else if (str_starts_with($requestPath, "/api/login/callback")) {
    $auth0->exchange($_SERVER['AUTH0_BASE_URL'] . "/api/login/callback");
    if (isset($_COOKIE["previous_url"])) {
        // get the cookie to a local var
        $previous_url = $_COOKIE["previous_url"];
        // delete the cookie
        setcookie("previous_url", "", time() - 3600, "/");
        // redirect to the cookie value
        header("Location: " . $previous_url);
    } else {
        header("Location: /");
    }
} else if ($pathParts[1] == "activity") {
    $activityId = $pathParts[2];
    $Activity = new Activity();
    $Activity->setId($activityId);
    $Activity->dbLoadById();

    if ($Activity->hasId()) {
        $mainContent = "activity";

        $pageTitle = $Activity->getActivity();
    } else {
        header("Location: /");
    }
} else {
    if ($MyUser->isLoggedIn()) {
        $mainContent = "dashboard";
        $pageTitle = "Dashboard";
    } else {
        $mainContent = "homepage";
        $pageTitle = "Discover Your Child's Learning";
        $pageDescription = "Enter an activity your child did, and our AI will describe how they have learned specific concepts in any given subject.";
    }
}

require_once '../inc/components/header.php';
require_once '../inc/' . $mainContent . '.php';
require_once '../inc/components/footer.php';
