<?php

trait UserTiers
{
    use UserId;
    private $_tierId, $_tier, $_subscribeDate, $_itinerariesLeft, $_customerId;

    public function setCustomerId($id)
    {
        $this->_customerId = $id;
    }

    public function getCustomerId()
    {
        return $this->_customerId;
    }

    public function setTierId($id)
    {
        $this->_tierId = $id;
    }

    public function getTierId()
    {
        return $this->_tierId;
    }

    public function setTier($tier)
    {
        $this->_tier = $tier;
    }

    public function getTier()
    {
        return $this->_tier;
    }

    public function setSubscribeDate($date)
    {
        $this->_subscribeDate = $date;
    }

    public function getSubscribeDate()
    {
        return $this->_subscribeDate;
    }

    public function setItinerariesLeft($num)
    {
        $this->_itinerariesLeft = $num;
    }

    public function getItinerariesLeft()
    {
        return $this->_itinerariesLeft;
    }

    public function isPro()
    {
        if ($this->getTier() == "pro") return true;
        return false;
    }

    public function getTierLabel()
    {
        if ($this->getTier() == "pro") return "Professional";
        if ($this->getTier() == "admin") return "Administrator";
        if ($this->getTier() == "standard") return "Premium";
        return "Free";
    }

    public function isAdmin()
    {
        if ($this->getTier() == "admin") return true;
        return false;
    }

    public function isStandard()
    {
        if ($this->getTier() == "standard") return true;
        return false;
    }

    public function isFree()
    {
        if ($this->getTier() == "free" || $this->getTier() == '') return true;
        return false;
    }

    public function hasItineraryLimit()
    {
        if ($this->isFree()) return true;
        return false;
    }

    public function dbAddTierForNewUser($id, $tier, $itinerariesLeft)
    {
        $q = "INSERT INTO `user_tiers` (`tier_id`, `user_id`, `tier`, `itineraries_left`, `added_date`) VALUES (NULL, :user_id, :tier, :itineraries_left, now())";
        $d = [':user_id' => $id, ':tier' => $tier, ':itineraries_left' => $itinerariesLeft];

        $this->getDb()->prepExec($q, $d);
    }

    public function upgradeToPro()
    {
        $q = "UPDATE `user_tiers` SET `tier` = 'pro', `customer_id` = :customer_id, `subscribe_date` = NOW() WHERE `user_tiers`.`user_id` = :user_id";
        $d = [':user_id' => $this->getId(), ':customer_id' => $this->getCustomerId()];

        $this->getDb()->prepExec($q, $d);
    }

    public function upgradeToStandard()
    {
        $q = "UPDATE `user_tiers` SET `tier` = 'standard', `customer_id` = :customer_id, `subscribe_date` = NOW() WHERE `user_tiers`.`user_id` = :user_id";
        $d = [':user_id' => $this->getId(), ':customer_id' => $this->getCustomerId()];

        $this->getDb()->prepExec($q, $d);
    }

    /*
    public function getStripeManageLink()
    {
        \Stripe\Stripe::setApiKey($_SERVER["STRIPE_SECRET_KEY"]);

        $customer_id = $this->getCustomerId();

        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $customer_id,
            'return_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/',
        ]);

        return $session->url;
    }
    */
}
