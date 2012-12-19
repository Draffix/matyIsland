<?php

use Nette\Application\UI;

class Rating extends UI\Control {

    private $rating_dbhost = 'localhost';
    private $rating_dbuser = 'root';
    private $rating_dbpass = 'jarda';
    private $rating_dbname = 'matyisland';
    private $rating_tableName = 'ratings';
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
    private $tense;

    public function rating_bar($id = '2id', $units = '5', $productID) {
        $rating_conn = mysql_connect($this->rating_dbhost, $this->rating_dbuser, $this->rating_dbpass) or die('Error connecting to mysql');
        //mysql_select_db($rating_dbname);
        //set some variables

        $ip = $_SERVER['REMOTE_ADDR'];
        if (!$units) {
            $units = 5;
        }

        // get votes, values, ips for the current rating bar
        $query = mysql_query("SELECT total_votes, total_value, used_ips FROM matyisland.product WHERE prod_id='$productID' ") or die(" Error: " . mysql_error());

        // insert the id in the DB if it doesn't exist already
        if (mysql_num_rows($query) == 0) {
            $result = mysql_query("UPDATE matyisland.product SET (total_votes = 0, total_value = 0, used_ips = '') WHERE prod_id='$productID'") or die(" Error: " . mysql_error());
        }


        $numbers = mysql_fetch_assoc($query);


        if ($numbers['total_votes'] < 1) {
            $count = 0;
        } else {
            $count = $numbers['total_votes']; //how many votes total
        }
        $current_rating = $numbers['total_value']; //total number of rating added together and stored
        $tense = ($count == 1) ? "hlas" : "hlasy"; //plural form votes/vote
        // determine whether the user has voted, so we know how to draw the ul/li
        $voted = mysql_num_rows(mysql_query("SELECT used_ips FROM matyisland.product WHERE used_ips LIKE '%" . $ip . "%' AND prod_id='" . $productID . "' "));

        // now draw the rating bar
        $rating_width = @number_format($current_rating / $count, 2) * $this->rating_unitwidth;
        $rating1 = @number_format($current_rating / $count, 1);
        $rating2 = @number_format($current_rating / $count, 2);

        $this->id = $id;
        $this->units = $units;
        $this->voted = $voted;
        $this->rating1 = $rating1;
        $this->count = $count;
    }

    public function render() {
        $this->rating_bar('2id', '5', $_SESSION['productID']);

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
        $rating_conn = mysql_connect($this->rating_dbhost, $this->rating_dbuser, $this->rating_dbpass) or die('Error connecting to mysql');
        //mysql_select_db($rating_dbname);

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
            
//connecting to the database to get some information
        $query = mysql_query("SELECT total_votes, total_value, used_ips FROM matyisland.product WHERE prod_id='$productID'") or die(" Error: " . mysql_error());
        $numbers = mysql_fetch_assoc($query);
        $usedIP = unserialize($numbers['used_ips']);
        $count = $numbers['total_votes']; //how many votes total
        $current_rating = $numbers['total_value']; //total number of rating added together and stored
        $sum = $vote_sent + $current_rating; // add together the current vote value and the total vote value

        $tense = ($count == 1) ? "vote" : "votes"; //plural form votes/vote
// checking to see if the first vote has been tallied
// or increment the current number of votes
        ($sum == 0 ? $added = 0 : $added = $count + 1);

// if it is an array i.e. already has entries the push in another value
        ((is_array($usedIP)) ? array_push($usedIP, $ip) : $usedIP = array($ip));
        $serIP = serialize($usedIP);

        if ($vote_sent >= 1 && $vote_sent <= $units) {
            $result = mysql_query("UPDATE matyisland.product SET total_votes='" . $added . "', total_value='" . $sum . "', used_ips='" . $serIP . "' WHERE prod_id='$productID'");
            header("Location: $referer"); // go back to the page we came from 
            exit;
        }
    }

}
