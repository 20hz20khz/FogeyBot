<?php
$consumer_key = "xxx";
$consumer_secret = "xxx";
$access_key = "xxx";
$access_secret = "xxx";

require_once('twitteroauth.php');
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$decade = array("20s","30s","40s","50s");
shuffle($decade);echo $decade[0];echo "<br/>";
$randomTweetText = array("Good ol' days!","#nostalgia","Don't know this? You're too young!","#memories","#flashback","#agetest","Only 19".$decade[0]." kids remember","Ya dig?","#".$decade[0],"#19".$decade[0],"Cool it Daddy-O","#memory","#cool","I dig it","Better times","The best","Don't make 'em like they used to!","#classic","#vintage","#retro","Golden Age","not too shabby","#neato","still got it!","remember this?","#masterpiece","#OldSchool","Better Times","Good Times","#oldie","Oldie but a goodie!",$decade[0]." child");
shuffle($randomTweetText);
$giphy = 'http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&tag=19'.$decade[0];
$giphy = json_decode(file_get_contents($giphy));
echo "<img src='".$giphy->data->image_url."' />";echo "<br/>";
$rand = mt_rand(1,10);
if( $rand == 5){
  $connection = new TwitterOAuth($consumer_key ,$consumer_secret , $access_key , $access_secret );
  $media1 = $connection->upload('media/upload', array('media' => $giphy->data->image_url));
  echo $media1->media_id_string;echo "<br/>";
  $parameters = array(
      'status' => $randomTweetText[0]." ".$randomTweetText[1],
      'media_ids' => $media1->media_id_string,
      //    'media_ids' => implode(',', array($media1->media_id_string)),
  );
  $result = $connection->post('statuses/update', $parameters);
  //echo $result;
  print_r($result);
} else {
  echo $rand." ".$randomTweetText[0]." ".$randomTweetText[1];
}

//Post a tweet
//$connection = new TwitterOAuth ($consumer_key ,$consumer_secret , $access_key , $access_secret );
//$connection->post('statuses/update', array('status' => "Hello Twitter OAuth!"));

?>
