<?php

class Activity
{
    use IdAddedDate, UserId;

    private $_activity, $_subject, $_grade, $_response;

    public function __construct($id = null)
    {
        if ($id != null) {
            $this->setId($id);
            $this->dbLoadById();
        }
    }

    public function getActivity()
    {
        return $this->_activity;
    }

    public function setActivity($activity)
    {
        $this->_activity = $activity;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function getSubjectString()
    {
        $subjectString = '';
        foreach ($this->getSubject() as $subject) {
            $subjectString .= $subject . ', ';
        }
        return rtrim($subjectString, ', ');
    }

    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    public function getGrade()
    {
        return $this->_grade;
    }

    public function setGrade($grade)
    {
        $this->_grade = $grade;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setResponse($response)
    {
        $this->_response = $response;
    }

    // has grade
    public function hasGrade()
    {
        if ($this->getGrade() == '') return false;
        return true;
    }

    // has response

    public function hasResponse()
    {
        if ($this->getResponse() == '') return false;
        return true;
    }

    // has subject

    public function hasSubject()
    {
        if ($this->getSubject() == '') return false;
        return true;
    }

    public function dbInsert()
    {
        global $Db;

        $q = "INSERT INTO `activities` (`activity`, `subject`, `grade`, `response`,`added_date`, `user_id`) VALUES (:activity, :subject, :grade, :response,NOW(), :user_id)";
        $d = [
            ':activity' => $this->getActivity(),
            ':subject' => $this->getSubjectString(),
            ':grade' => $this->getGrade(),
            ':response' => $this->getResponse(),
            ':user_id' => $this->getUserId()
        ];

        $Db->prepExec($q, $d);

        $this->setId($Db->getLastInsertId());
    }

    public function dbLoadById()
    {
        global $Db;

        $q = "SELECT * FROM `activities` WHERE `id` = :id";
        $d = [
            ':id' => $this->getId()
        ];

        $activity = $Db->prepExecFetchAll($q, $d);

        if (count($activity) > 0) {
            $this->setActivity($activity[0]['activity']);
            $this->setSubject($activity[0]['subject']);
            $this->setGrade($activity[0]['grade']);
            $this->setResponse($activity[0]['response']);
            $this->setAddedDate($activity[0]['added_date']);
            $this->setUserId($activity[0]['user_id']);
        } else {
            $this->setId('');
        }
    }

    public function dbUpdateUserId()
    {
        global $Db;

        $q = "UPDATE `activities` SET `user_id` = :user_id WHERE `id` = :id";
        $d = [
            ':user_id' => $this->getUserId(),
            ':id' => $this->getId()
        ];

        $Db->prepExec($q, $d);
    }

    public function dbRemoveUserId()
    {
        global $Db;

        $q = "UPDATE `activities` SET `user_id` = NULL WHERE `id` = :id";
        $d = [
            ':id' => $this->getId()
        ];

        $Db->prepExec($q, $d);
    }

    public function getAndSetResponseFromOpenAi()
    {
        if ($this->getActivity() != '' && $this->getSubjectString() != '') {
            $grade = '';
            if ($this->getGrade() != '') $grade = $this->getGrade() . '-grade';

            $prompt = 'Activity: "' . $this->getActivity() . '”

Start by writing bullet points of how the ' . $grade . ' child has learned specific concepts from the activity for the subject(s) of ' . $this->getSubjectString() . ': 3 to 4 bullet points per subject. Do not assume the child used any materials beyond those mentioned in the description.
Then add a descriptive paragraph <p> with tips on creative ways for continued development related to the activity.
Finally add a "Book Recommendatons" section with 3  '.$grade.' reading level books related to '.$this->getActivity().' and the subjects.

HTML Format/Example:
(Repeat)
<h4>[Subject Name]</h4>
<ul>
<li>[Analysis]</li>
</ul>
(End Repeat)
<p>[Insert tips paragraph (From step 2)]</p>
<h4>Book Recommendations</h4>
<ul id="books">
<li><b>[Book Title]</b> by [Author]: [Short Description]</li>
</ul>

Start full HTML output with <div>';

            $AI = new AI();
            $AI->setPrompt($prompt);

            $response = $AI->getResponseFromOpenAi("You are a helpful teacher. Output in HTML",0.75,0,"gpt-3.5-turbo",2800);

            $doc = new DOMDocument();
            $doc->loadHTML($response, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_use_internal_errors(true);  // suppress HTML5 errors
            libxml_clear_errors();  // clear errors for HTML5
            $cleanHTML = $doc->saveHTML();
            $doc->loadHTML($cleanHTML, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $xpath = new DOMXPath($doc);

            // Find all <b> tags within a <li> within an <ul> with id 'books'
            $query = '//ul[@id="books"]/li/b';

            $elements = $xpath->query($query);

            foreach ($elements as $element) {
                $text = $element->nodeValue;

                // Create the new <a> element
                $a = $doc->createElement('a', $text);

                // Set the href attribute
                /*
                <a target="_blank" href="https://www.amazon.com/gp/search?ie=UTF8&tag=kevinl8888-20&linkCode=ur2&linkId=2fa44c89b3daf562c349b7ad4db64bd4&camp=1789&creative=9325&index=books&keywords=this is some text">book</a>
                */
                $a->setAttribute('href', 'https://www.amazon.com/gp/search?ie=UTF8&tag=kevinl8888-20&linkCode=ur2&linkId=2fa44c89b3daf562c349b7ad4db64bd4&camp=1789&creative=9325&index=books&keywords=' . urlencode($text));
                $a->setAttribute('target', '_blank');

                // Replace the <b> element with the new <a> element
                $element->parentNode->replaceChild($a, $element);
            }

            // if there were elements found, add a small disclaimer
            if ($elements->length > 0) {
                $disclaimer = $doc->createElement('p', 'If you click on these links and make a purchase, we may receive a small commission.');
                $disclaimer->setAttribute('style', 'font-size: 0.8rem;');

                $doc->appendChild($disclaimer);
            }

            // Save the modified HTML back to the response variable
            $response = $doc->saveHTML();

            $this->setResponse($response);
        }
    }
}
