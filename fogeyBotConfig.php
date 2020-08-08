<?php
$consumer_key = "xxx";
$consumer_secret = "xxx";
$access_key = "xxx";
$access_secret = "xxx";

require_once('twitteroauth.php');// Use the twitteroauth library
$lastTweetScreenName=NULL;
$twitter = new TwitterOAuth ($consumer_key ,$consumer_secret , $access_key , $access_secret );// Connect
// Random replies
$randomReply = array("Glenn Miller","Benny Goodman","Mitch Miller","Lawrence Welk","Perry Como","Henry Mancini","Pat Boone","Duke Ellington","Cab Calloway","Count Basie","Louis Armstrong","Dean Martin","Bobby Darin","Billie Holiday","Ella Fitzgerald","Robert Goulet","Nat King Cole","Andy Williams","Mel Tormé","Frank Sinatra","Liza Minnelli","Tony Bennett","Bing Crosby","Johnny Mathis","Tom Jones","Jimmie Rodgers","Roy Rogers","Merle Travis","Merle Haggard","Hank Snow","Gene Autry","The Carter Family","Hank Williams","Flatt & Scruggs","Bill Monroe","Ralph Stanley","Buck Owens","Bob Wills and His Texas Playboys","Conway Twitty","Chet Atkins","Roger Miller","Elvis Presley","Doris Day","The Everly Brothers","The Weavers","Pete Seeger","Fats Domino","Neil Diamond","Ricky Nelson","Paul Anka","Dionne Warwick","Brenda Lee","Kenny Rogers","Sam Cooke","Jackie Wilson","Chubby Checker","Roy Orbison","Neil Sedaka","Herb Alpert & The Tijuana Brass","Tommy James & The Shondells","Wilson Pickett","Chuck Berry","The Righteous Brothers","Marty Robbins","Petula Clark","Jan & Dean","The Sons of the Pioneers","Cliff Edwards","Hoagy Carmichael","Les Paul & Mary Ford","Sister Rosetta Tharpe","Ernest Tubb","Miles Davis","John Lee Hooker","Lionel Hampton","Peggy Lee","Buddy Holly","Dinah Shore","Muddy Waters","T-Bone Walker","Woody Guthrie","Rose Murphy","Raymond Scott","Fred Astaire","The Dave Brubeck Quartet","Tennessee Ernie Ford","The Kingston Trio","Jim Reeves","Frankie Avalon","Screamin' Jay Hawkins","Nina Simone","George Jones","Little Richard","Harry Belafonte","Wanda Jackson","The Coasters","Jerry Lee Lewis","B.B. King","Howlin' Wolf","Bo Diddley","Ray Charles","Bill Haley and His Comets","Johnny Cash");
$newTweetCount = 0;// Set the tweets count to zero
$newFavs = 0;// Set the favs count to zero
$myUserInfo = $twitter->get('account/verify_credentials');// Get the authorized user's info
$myLastTweet = $twitter->get('statuses/user_timeline', array('user_id' => $myUserInfo->id_str, 'count' => 1));// Use the user's info to get the authorized user's last tweet

// Search the Twitterverse for ppl saying 'recommend me music' and fav them and reply to them
$search = $twitter->get('search/tweets', array('q' => '%22recommend%20music%20for%20me%22OR%22recommend%20music%20to%20me%22OR%22recommend%20me%22AND%22music%22', 'count' => 5, 'since_id' => $myLastTweet[0]->id_str));// Get new search results since the user's last tweet
foreach($search->statuses as $tweet) {// Loop the following for each search result
	shuffle($randomReply);// Shuffle the random reply for each search result
	//$status = 'I recommend '.$randomReply[0].' @'.$tweet->user->screen_name.' “'.$tweet->text.'”';// Tweet is composed of: random reply, @screen_name, and “the original tweet”
	$status = 'I recommend '.$randomReply[0]." https://twitter.com/".$tweet->user->screen_name."/status/".$tweet->id_str;// Tweet is composed of: random reply, URL of original tweet
	if(strlen($status) > 140) $status = substr($status, 0, 139);// If tweet is too long, shorten it.
	if(count($tweet->entities->user_mentions) < 1){// If there are no @mentions
		if(empty($tweet->retweeted_status) and empty($tweet->quoted_status)){// If not retweet and not quote tweet
			//$twitter->post('favorites/create', array('id' => $tweet->id_str));// Fav the original tweet
			$twitter->post('statuses/update', array('status' => $status,'in_reply_to_status_id' => $tweet->id_str));// Post tweet
			$newTweetCount++;// Add one to output counter
			echo $status."\n";// Display tweet
			echo "<br/>";
		}
	}elseif($tweet->entities->user_mentions[0]->screen_name == "fogeybot"){// If the first @mention is fogeybot
			//$twitter->post('favorites/create', array('id' => $tweet->id_str));// Fav the original tweet
			$twitter->post('statuses/update', array('status' => $status,'in_reply_to_status_id' => $tweet->id_str));// Post tweet
			$newTweetCount++;// Add one to output counter
			echo $status."\n";// Display tweet
			echo "<br/>";
	}
	$lastTweetScreenName = $tweet->user->screen_name;
}

// Check for @mentions and fav them AND follow the user
$search = $twitter->get('statuses/mentions_timeline', array('count' => 10, 'since_id' => $myLastTweet[0]->id_str));// Get new @mentions since the user's last tweet
//echo(count($search));echo "<br/>";
//$idString = (string)$search[0]->id_str;
if(count($search) > 0){// If search is not empty
//foreach($search->statuses as $tweet) {// Loop the following for each @mention
	//$twitter->post('favorites/create', array('id' => (string)$search[0]->id_str));// Fav the original tweet
	$newFavs++;// Add one to output counter
	//$twitter->post('friendships/create', array('screen_name' => (string)$search[0]->user->screen_name));
	echo (string)$search[0]->user->screen_name;
	$newTweetScreenName = (string)$search[0]->user->screen_name;
	foreach($search as $tweet) {// Loop the following for each search result
		if( (($shouldI_pos = stripos((string)$tweet->text, "recommend")) !== FALSE ) AND (($shouldI_pos = stripos((string)$tweet->text, "me")) !== FALSE ) AND (($shouldI_pos = stripos((string)$tweet->text, "music")) !== FALSE ) AND ($newTweetScreenName !== $lastTweetScreenName)){	//Reply to ppl tweeting recommend me music
			shuffle($randomReply);// Shuffle the random reply for each search result
			//$status = 'I recommend '.$randomReply[0].' @'.$tweet->user->screen_name.' “'.$tweet->text.'”';// Tweet is composed of: random reply, @screen_name, and “the original tweet”
			$status = ".@".$tweet->user->screen_name." I recommend ".$randomReply[0];// Tweet is composed of: random reply, URL of original tweet
			if(strlen($status) > 140) $status = substr($status, 0, 139);// If tweet is too long, shorten it.

				//$twitter->post('favorites/create', array('id' => $tweet->id_str));// Fav the original tweet
				$twitter->post('statuses/update', array('status' => $status,'in_reply_to_status_id' => $tweet->id_str));// Post tweet
				$newTweetCount++;// Add one to output counter
				echo $status."\n";// Display tweet
				echo "<br/>";
		}
	}
}

// Post a GIF 7% of the time
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$decade = array("20s","30s","40s","50s","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59","60","61","62");
shuffle($decade);echo $decade[0];echo "<br/>";
$randomTweetText = array("😀","😁","😂","😃","😄","😅","😆","😇","😈","👿","😉","😊","😋","😌","😍","😎","😏","😐","😑","😒","😓","😔","😕","😖","😗","😘","😙","😚","😛","😜","😝","😞","😟","😠","😡","😢","😣","😤","😥","😦","😧","😨","😩","😪","😫","😬","😭","😮",
"😯","😰","😱","😲","😳","😴","😵","😶","😷","😸","😹","😺","😻","😼","😽","😾","😿","🙀","👣","👤","👥","👦","👧","👨","👩","👨","👩","👦","👨","👩","👧","👪","👨","👩","👦","👦","👫","👬","👭","👮","👯","👰","👱","👲","👳","👴","👵","👶","👷",
"👸","💂","👼","🎅","👻","👹","👺","💩","💀","👽","👾","🙇","💁","🙅","🙆","🙋","🙎","🙍","💆","💇","💑","👩👩👨","💏💋","💋","👅","👋","👍","👎","👆","👇","👈","👉","👌","👊","✊","✋","💪","👐","🙌","👏","🙏",
".@DungeonCrawlr ↓",".@fogeybot",".@sunrisecopy",".@perfectmanbot",".@BakulaFan",".@DTrumpFan",".@botmaze",
"#throwback","#replay","#yuss","#childhood","#FuddyDuddy","#geezer","#internet","#TodaysKidsWillNeverKnow","#BackInMyDay","#".$decade[0],"#19".$decade[0],"hashtag","yep","#this","#wow","#nice","This.","This wins the internet.","This, just this.","Good ol' days!","#nostalgia","Don't know this? You're too young!","#memories","#flashback","#agetest","Only 19".$decade[0]." kids remember","Ya dig?","#".$decade[0],"#19".$decade[0],"Cool it Daddy-O","#memory","#cool","I dig it","Better times","The best","Don't make 'em like they used to!","#classic","#vintage","#retro","Golden Age","not too shabby","#neato","still got it!","remember this?","#masterpiece","#OldSchool","Better Times","Good Times","#oldie","Oldie but a goodie!",$decade[0]." child","👀","👇","👴","👻","💥","💫","📣","📺","📻","🔔","🔥","🌝","🌜","🗽","📰","📫","📝","📚","💸","💰","💡","🎬","🎩","🎦","🎥","#hashtag","#tweet","#twitter","#random","#yolo","#fogey","#USA","🌛","🌟","🌠","⭐","⛅","☎","#Good","#Better","#Best","⏳","⏳⏰⌛","⏰","⌛","⤵","#GettingOld","#ThatsMisterFogeyToYou","#WerthersOriginal","#OldMan","#SeniorMoment","¯\_(ツ)_/¯","ಠ_ಠ");
shuffle($randomTweetText);
$rand = mt_rand(1,29);
if( $rand == 1){
	$giphy = 'http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&tag=19'.$decade[0];
	$giphy = json_decode(file_get_contents($giphy));
	echo "<img src='".$giphy->data->image_url."' />";echo "<br/>";
  $connection = new TwitterOAuth($consumer_key ,$consumer_secret , $access_key , $access_secret );
  $media1 = $connection->upload('media/upload', array('media' => $giphy->data->image_url));
  echo $media1->media_id_string;echo "<br/>";
  $parameters = array(
      'status' => $randomTweetText[0]." ".$randomTweetText[1]." ".$randomTweetText[2],
      'media_ids' => $media1->media_id_string,
      //    'media_ids' => implode(',', array($media1->media_id_string)),
  );
  $result = $connection->post('statuses/update', $parameters);
  //echo $result;
  print_r($result);echo "<br/>";
	$newTweetCount++;// Add one to output counter
} else {
  echo $rand." ".$randomTweetText[0]." ".$randomTweetText[1]." ".$randomTweetText[2];echo "<br/>";
}

// Make corny joke about new followers
//$userLists = $twitter->get('lists/list');// Get list ID
//echo " userLists ";print_r($userLists);// Print list ID
//$jokedList = $twitter->get('lists/members', array('list_id' => 215467717));// Get members of list
//echo " jokedList ";print_r($jokedList);// Print list ID
//$mostRecentFollower = $twitter->get('followers/list', array('user_id' => $myUserInfo->id_str, 'count' => 1));//
//echo " mostRecentFollower ";print_r($mostRecentFollower);// Print list ID
//foreach($jokedList->users as $usercheck) {// Loop the following for each search result
//	echo array_search($mostRecentFollower->users[0]->id_str,$usercheck->id_str);// true = 2795184106
//}

echo "Success! Check your twitter bot for ".$newTweetCount." new tweets, ".$newFavs." favs.";
?>
