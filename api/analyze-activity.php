<?php

// check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // check if the request body is not empty
    if (!empty(file_get_contents('php://input'))) {
        // get the request body
        $requestBody = $_POST;
        $subjects = $requestBody['subjects'];
        $subjectsCount = count($subjects);

        // check if the request body has a activity and subject
        if (isset($requestBody['activity']) && $subjectsCount > 0) {
            $Activity = new Activity();
            $Activity->setActivity($requestBody['activity']);
            $Activity->setSubject($subjects);
            $Activity->setGrade($requestBody['grade']);
            $Activity->getAndSetResponseFromOpenAi();

            $U = new User();

            if ($_POST['userAuthToken'] != "") {
                $U->setDb($Db);
                $U->setAuthToken($_POST['userAuthToken']);
                $U->loadByAuthToken();

                if ($U->getId() != null)  $Activity->setUserId($U->getId());
            }

            $Activity->dbInsert();
            $id = $Activity->getId();
            $response = $Activity->getResponse();

            echo json_encode(['response' => $response, 'id' => $id]);
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
