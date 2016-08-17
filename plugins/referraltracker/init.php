<?php


function youtubeIdFromUrl($url) {
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}

function cleanUpReferral($referral)
{
	$ytid = youtubeIdFromUrl($referral);
	if($ytid)
		$referral = "http://www.youtube.com/watch?v=".$ytid;
		
	//TODO: Unify google search URLs too?
	return $referral;
}

if(!$ajaxPage)
{
	$referral = $_SERVER['HTTP_REFERER'];
	$startwith = 'http://'.$_SERVER['SERVER_NAME'].'/';
	$startwith2 = 'https://'.$_SERVER['SERVER_NAME'].'/';
	if ($referral && substr($referral, 0, strlen($startwith)) != $startwith && substr($referral, 0, strlen($startwith2)) != $startwith2)
	{
		$referral = cleanUpReferral($referral);	
	
		Query("INSERT INTO {referrals} (ref_hash,referral,count) VALUES ({0}, {1}, 1) ON DUPLICATE KEY UPDATE count=count+1", 
			md5($referral), $referral);
	}
}


?>
