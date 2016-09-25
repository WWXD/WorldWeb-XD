<?php

CheckPermission('admin.usernuke');

$uid = (int)$_GET["id"];

$user = fetch(query("select * from {users} where id={0}", $uid));

if(!$user)
	kill("User not found");

if($user["powerlevel"] > 0)
	kill("You can't nuke a staff member. Demote him first.");
	
$passwordFailed = false;

if(isset($_POST["currpassword"]))
{
	$sha = doHash($_POST['currpassword'].SALT.$loguser['pss']);
	if($loguser['password'] == $sha)
	{
		
		//Delete posts from threads by user
		query("delete pt from {posts_text} pt
				left join {posts} p on pt.pid = p.id
				left join {threads} t on p.thread = t.id
				where t.user={0}", $uid);
		query("delete p from {posts} p
				left join {threads} t on p.thread = t.id
				where t.user={0}", $uid);

		//Delete posts by user			
		query("delete pt from {posts_text} pt
				left join {posts} p on pt.pid = p.id
				where p.user={0}", $uid);
		query("delete p from {posts} p
				where p.user={0}", $uid);
		
		//Delete threads by user
		query("delete t from {threads} t
				where t.user={0}", $uid);

		//Delete usercomments by user or to user
		query("delete from {usercomments}
				where uid={0} or cid={0}", $uid);

		//Delete THE USER ITSELF
		query("delete from {users}
				where id={0}", $uid);

		//and then IP BAN HIM
		query("insert into {ipbans} (ip, reason, date) 
				values ({0}, {1}, 0)
				on duplicate key update ip=ip", $user["lastip"], "Nuking ".$user["name"]);
				
		echo "User nuked!<br/>";
		echo "You will need to ", actionLinkTag("Recalculate statistics now", "recalc");

		throw new KillException();
	}
	else
		$passwordFailed = true;
}

if($passwordFailed)
	Alert("Invalid password. Please try again.");
echo "
<form name=\"confirmform\" action=\"".actionLink("nuke", $uid)."\" method=\"post\">
	<table class=\"outline margin width50\">
		<tr class=\"header0\">
			<th colspan=\"2\">
				".__("NUKE!!")."
			</th>
		</tr>
		<tr>
			<td class=\"cell2\">
			</td>
			<td class=\"cell0\">
				".__("WARNING: This will IP-ban the user, and permanently and irreversibly delete the user itself and all his posts, threads, and profile comments. This user will be gone forever, as if he never existed.")."
				<br/><br/>
				".__("Please enter your password to confirm.")."
			</td>
		</tr>
		<tr>
			<td class=\"cell2\">
				<label for=\"currpassword\">".__("Password")."</label>
			</td>
			<td class=\"cell1\">
				<input type=\"password\" id=\"currpassword\" name=\"currpassword\" size=\"13\" maxlength=\"32\" />
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"actionlogin\" value=\"".__("NUKE!!")."\" />
			</td>
		</tr>
	</table>
</form>";

