<?php

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

            // get the response from OpenAI
            $response = $subjectExplorer->getResponseFromOpenAi();

            // check if the response is not empty
            if ($response != '') {
                // return the response

                $Activity = new Activity();
                $Activity->setActivity($requestBody['activity']);
                $Activity->setSubject($requestBody['subject']);
                $Activity->setGrade($requestBody['grade']);
                $Activity->setResponse($response);

                $U = new User();

                if ($_POST['userAuthToken'] != "") {
                    $U->setDb($Db);
                    $U->setAuthToken($_POST['userAuthToken']);
                    $U->loadByAuthToken();

                    if ($U->getId() != null)  $Activity->setUserId($U->getId());
                }

                $Activity->dbInsert();

                echo json_encode(['response' => $response]);
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
