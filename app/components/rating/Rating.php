<?php

use Nette\Application\UI;

class Rating extends UI\Control {
    private $rating_unitwidth = 30; // the width (in pixels) of each rating unit (star, etc.)
    private $id;
    private $units;
    private $rating_width;
    private $rating1;
    private $rating2;
    private $ncount;
    private $ip;
    private $voted;
    private $count;

    /**
     * @var ProductModel
     */
    protected $products;

    public function __construct(ProductModel $products) {
        parent::__construct();
        $this->products = $products;
    }

    public function rating_bar($productID) {
        //set some variables
        $this->ip = $_SERVER['REMOTE_ADDR'];
        if (!$this->units) {
            $this->units = 5;
        }

        // get votes, values, ips for the current rating bar
        $numbers = $this->products->fetchRankValues($productID);

        // insert the id in the DB if it doesn't exist already
        if (count($numbers) == 0) {
            $this->products->insertRankIfNotExists($productID);
        }

        if ($numbers['total_votes'] < 1) {
            $this->count = 0;
        } else {
            $this->count = $numbers['total_votes']; //how many votes total
        }
        $current_rating = $numbers->total_value; //total number of rating added together and stored
        // determine whether the user has voted, so we know how to draw the ul/li
        $this->voted = $this->products->whetherUserVoted('%' . $this->ip . '%', $productID);

        // now draw the rating bar
        $this->rating_width = @number_format($current_rating / $this->count, 2) * $this->rating_unitwidth;
        $this->rating1 = @number_format($current_rating / $this->count, 1);
        $this->rating2 = @number_format($current_rating / $this->count, 2);
    }

    public function render() {
        $this->rating_bar($_SESSION['productID']);

        $this->template->id = $this->id;
        $this->template->rating_unitwidth = $this->rating_unitwidth;
        $this->template->units = $this->units;
        $this->template->rating_width = $this->rating_width;
        $this->template->rating2 = $this->rating2;
        $this->template->ncount = $this->ncount;
        $this->template->ip = $this->ip;
        $this->template->voted = $this->voted;
        $this->template->rating1 = $this->rating1;
        $this->template->count = $this->count;

        $this->template->setFile(dirname(__FILE__) . '/template.latte');
        $this->template->render();
    }

    public function handleRate($ncount, $id, $ip, $units) {
        $productID = $_SESSION['productID'];
        //getting the values
        $vote_sent = preg_replace("/[^0-9]/", "", $ncount);
        $id_sent = preg_replace("/[^0-9a-zA-Z]/", "", $id);
        $ip_num = preg_replace("/[^0-9\.]/", "", $ip);
        $units = preg_replace("/[^0-9]/", "", $units);
        $ip = $_SERVER['REMOTE_ADDR'];
        $referer = $_SERVER['HTTP_REFERER'];

        if ($vote_sent > $units)
            die("Sorry, vote appears to be invalid."); // kill the script because normal users will never see this.


            
// get votes, values, ips for the current rating bar
        $numbers = $this->products->fetchRankValues($productID);
        $usedIP = unserialize($numbers['used_ips']);
        $count = $numbers['total_votes']; //how many votes total
        $current_rating = $numbers['total_value']; //total number of rating added together and stored
        $sum = $vote_sent + $current_rating; // add together the current vote value and the total vote value
        // checking to see if the first vote has been tallied
        // or increment the current number of votes
        ($sum == 0 ? $added = 0 : $added = $count + 1);

        // if it is an array i.e. already has entries the push in another value
        ((is_array($usedIP)) ? array_push($usedIP, $ip) : $usedIP = array($ip));
        $serIP = serialize($usedIP);

        if ($vote_sent >= 1 && $vote_sent <= $units) {
            $this->products->insertRankIfNotExists($added, $sum, $serIP, $productID);
            header("Location: $referer"); // go back to the page we came from 
            exit;
        }
    }

}
