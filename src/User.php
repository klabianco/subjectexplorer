<?php

class User
{
    use IdAddedDate, UserTiers;

    private $_email, $_firstName, $_lastName, $_authToken, $_publicId, $_db;

    public function setFirstName($s)
    {
        $this->_firstName = $s;
    }

    public function setDb($db)
    {
        $this->_db = $db;
    }

    public function getDb()
    {
        return $this->_db;
    }

    public function setLastName($s)
    {
        $this->_lastName = $s;
    }

    public function getFirstName()
    {
        return $this->_firstName;
    }

    public function getLastName()
    {
        return $this->_lastName;
    }

    public function setEmail($s)
    {
        $this->_email = $s;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function hasName()
    {
        if ($this->getFirstName() != "") return true;
        return false;
    }

    public function setAuthToken($authToken)
    {
        $this->_authToken = $authToken;
    }

    public function getAuthToken()
    {
        return $this->_authToken;
    }

    public function setPublicId($id)
    {
        $this->_publicId = $id;
    }

    public function getPublicId()
    {
        return $this->_publicId;
    }

    public function doesExist($where, $data)
    {
        $q = "select count(*) as c from `users` where " . $where;

        $r = $this->getDb()->prepExecFetchColumn($q, $data);

        if ($r == 1) return true;
        else return false;
    }

    public function doesAccountExist()
    {
        $d = [':email' => $this->getEmail()];

        return $this->doesExist("email = :email", $d);
    }

    public function setByEmail()
    {
        if ($this->getEmail() != '') {
            $q = "SELECT * FROM `users` u right join user_tiers t on u.id = t.user_id where email = :email";
            $d = [":email" => $this->getEmail()];

            $r = $this->getDb()->prepExecFetch($q, $d);
            $this->setAll($r);
        }
    }

    public function setById()
    {
        $q = "SELECT * FROM `users` where id = :id";
        $d = [":id" => $this->getId()];

        $r = $this->getDb()->prepExecFetch($q, $d);
        $this->setAll($r);
    }

    public function getFreeToken($length, $where, $var)
    {
        do {
            $authToken = $this->generateRandomString($length);
            $d = [$var => $authToken];
        } while ($this->doesExist($where, $d));

        return $authToken;
    }

    public function getFreeAuthToken()
    {
        $where = "auth_token = :auth_token";
        $var = ':auth_token';

        return $this->getFreeToken(32, $where, $var);
    }

    public function getFreePublicId()
    {
        $where = "public_id = :public_id";
        $var = ':public_id';

        return $this->getFreeToken(16, $where, $var);
    }

    public function addAndSetData()
    {
        $authToken = $this->getFreeAuthToken();
        $publicId = $this->getFreePublicId();

        $q = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `added_date`, `auth_token`, `public_id`) VALUES (:first, :last, :email, NOW(), :auth_token, :public_id)";
        $d = [":email" => $this->getEmail(), ':first' => $this->getFirstName(), ':last' => $this->getLastName(), ":auth_token" => $authToken, ":public_id" => $publicId];

        $this->getDb()->prepExec($q, $d);
        $this->setId($this->getDb()->getLastInsertId());
    }

    public function isLoggedIn()
    {
        if ($this->getEmail() == '') return false;
        return true;
    }

    public function setAll($a)
    {
        $this->setId($a['id']);
        $this->setAddedDate($a['added_date']);
        $this->setFirstName($a['first_name']);
        $this->setLastName($a['last_name']);
        $this->setEmail($a['email']);
        $this->setAuthToken($a['auth_token']);
        $this->setPublicId($a['public_id']);
        $this->setTier($a['tier']);
        $this->setTierId($a['tier_id']);
        $this->setItinerariesLeft($a['itineraries_left']);
        $this->setCustomerId($a['customer_id']);
    }

    public function getFullName()
    {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();

        if ($firstName === null && $lastName === null) {
            $email = $this->getEmail();
            $nameFromEmail = strstr($email, '@', true);
            return $nameFromEmail;
        }

        return $firstName . ' ' . $lastName;
    }

    public function loadByEmail()
    {
        if ($this->doesAccountExist()) {
            $this->setByEmail();
        } else {
            $this->addAndSetData();
            $this->dbAddTierForNewUser($this->getId(), 'free', 3);
            $this->setByEmail();
        }
    }

    public function loadById()
    {
        if ($this->getId() != '') {
            $q = "SELECT * FROM `users` u right join user_tiers t on u.id = t.user_id where id = :id";
            $d = [":id" => $this->getId()];

            $r = $this->getDb()->prepExecFetch($q, $d);
            $this->setAll($r);
        }
    }

    public function loadByAuthToken()
    {
        if ($this->getAuthToken() != '') {
            $q = "SELECT * FROM `users` u right join user_tiers t on u.id = t.user_id where BINARY auth_token = :auth_token";
            $d = [":auth_token" => $this->getAuthToken()];

            $r = $this->getDb()->prepExecFetch($q, $d);
            $this->setAll($r);
        }
    }

    public function loadByPublicId()
    {
        if ($this->getPublicId() != '') {
            $q = "SELECT * FROM `users` u right join user_tiers t on u.id = t.user_id where BINARY public_id = :id";
            $d = [":id" => $this->getPublicId()];

            $r = $this->getDb()->prepExecFetch($q, $d);
            $this->setAll($r);
        }
    }

    public function getAllUserPublicIds()
    {
        $q = "SELECT public_id as id FROM `users` where first_name is not null";
        return $this->getDb()->prepExecFetchAll($q);
    }

    public function updateFirstLastName()
    {
        $q = "UPDATE `users` SET `first_name` = :first_name, `last_name` = :last_name WHERE `users`.`id` = :id";
        $d = [':first_name' => $this->getFirstName(), ':last_name' => $this->getLastName(), ':id' => $this->getId()];

        $this->getDb()->prepExec($q, $d);
    }

    public function generateRandomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function dbUpdateAllUsersWithoutColumn($column)
    {
        $q = "SELECT id FROM `users` where $column is null";
        $r = $this->getDb()->prepExecFetchAll($q);

        foreach ($r as $data) {
            $id = $data['id'];

            if ($column == "auth_token") $val = $this->getFreeAuthToken();
            else if ($column == "public_id") $val = $this->getFreePublicId();

            $q2 = "UPDATE `users` SET $column = :col WHERE `users`.`id` = :id";
            $d = [':col' => $val, ':id' => $id];

            $this->getDb()->prepExec($q2, $d);
        }
    }

    public function subtractItineraryCount()
    {
        $q = "UPDATE `user_tiers` SET `itineraries_left` = `itineraries_left` - 1 WHERE `user_tiers`.`user_id` = :id";
        $d = [':id' => $this->getId()];

        $this->getDb()->prepExec($q, $d);

        // get the itineray count
        $q = "SELECT itineraries_left FROM `user_tiers` where user_id = :id";
        $d = [':id' => $this->getId()];
        $count = $this->getDb()->prepExecFetchColumn($q, $d);

        $this->setItinerariesLeft($count);
    }

    public function addMoreItineraries($number)
    {
        $q = "UPDATE `user_tiers` SET `itineraries_left` = `itineraries_left` + :number WHERE `user_tiers`.`user_id` = :id";
        $d = [':id' => $this->getId(), ':number' => $number];

        $this->getDb()->prepExec($q, $d);
    }

    public function getItinerariesLeftMessage()
    {
        $itineraryLeftCount = $this->getItinerariesLeft();

        $itineraryLeftMessage = '';

        if ($itineraryLeftCount <= 0) {
            $itineraryLeftMessage = 'Free Trial - No Itineraries Left';
        } else if ($itineraryLeftCount == 1) {
            $itineraryLeftMessage = 'Free Trial - 1 Itinerary Left';
        } else if ($itineraryLeftCount == 2) {
            $itineraryLeftMessage = 'Free Trial - ' . $itineraryLeftCount . ' Itineraries Left';
        }

        if ($itineraryLeftMessage != '') {
            $itineraryLeftMessage = '<div class="p-1"><a href="javascript:void(0)" class="show-paywall-modal text-white">' . $itineraryLeftMessage . '</a><br><div><button class="show-paywall-modal btn btn-sm" style="background-color: white; color: black">Upgrade Now For Unlimited Itineraries &amp; No Ads</button></div></div>';
        }

        return $itineraryLeftMessage;
    }

    public function updateAllUsersWithoutAuthToken()
    {
        $this->dbUpdateAllUsersWithoutColumn("auth_token");
    }

    public function updateAllUsersWithoutPublicId()
    {
        $this->dbUpdateAllUsersWithoutColumn("public_id");
    }

    public function hasSignedIn()
    {
        if ($this->getFirstName() == '') return false; // first and last name exist on people that have signed in.
        return true;
    }

    public function set30DayCookie()
    {
        setcookie("planTripAuthToken", $this->getAuthToken(), strtotime('+30 days'), "/");
    }

    public function dbDelete()
    {
        $this->getDb()->deleteThis("users", "id", $this->getId());
    }

    public function dbDeleteTier()
    {
        $this->getDb()->deleteThis("user_tiers", "user_id", $this->getId());
    }

    public function isOwner($id){
        if($this->getId() == $id) return true;
        return false;
    }

    public function setAndLoadByEmail($email, $firstName, $lastName)
    {
        $this->setEmail($email);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->loadByEmail();
    }
}