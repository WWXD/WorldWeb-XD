<?php

$uid = (int)$_GET["id"];

$user = fetch(query("select * from {users} where id={0}", $uid));

if(!$user)
	Kill(__("You cannot delete a user that doesn't exist."));

if(!$loguserid)
	Kill(__("You must be logged in to delete a profile."));

if($uid == 1)
	kill("You may not delete the owner.");

if($user["primarygroup"] > -1)
	Kill(__('You can\'t delete a staff member or a normal user. Ban him first.'));

//	Make 4 checks:
//	1) If the user can edit his own profile: you don't want to be a hypocrite
//	2) If the user can edit other profiles.
//	3) If the user has the delete permission set to on. 
//	4) If the user is above rank ID -1, it'll block it. It'll only delete the user if the user is below rank ID 0. This one is checked twice. You'll need to ban the user before you delete him.

if (HasPermission('admin.editusers') && HasPermission('admin.userdelete') && HasPermission('user.editprofile') && $user["primarygroup"] < 0) {
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
				
			//Delete the PM's sent to the user or sent by the user
			query("delete from {pmsgs}
					where userfrom={0}", $uid);

			//Delete THE USER ITSELF
			query("delete from {users}
					where id={0}", $uid);

			//and then IP BAN HIM
			query("insert into {ipbans} (ip, reason, date) 
					values ({0}, {1}, 0)
					on duplicate key update ip=ip", $user["lastip"], "Deleting ".$user["name"]);
				
			//Log that the user is nuked.
			Report("".$user["name"]."was deleted.");
				
			echo "User deleted!<br/>";
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
					".__("Delete the user!!")."
				</th>
			</tr>
			<tr>
				<td class=\"cell2\">
				</td>
				<td class=\"cell0\">
					".__("WARNING: This will IP-ban the user, and permanently and irreversibly delete the user itself and all his posts, threads, private messages, and profile comments. This user will be gone forever, as if he never existed.")."
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
					<input type=\"submit\" name=\"actionlogin\" value=\"".__("Delete the user!!")."\" />
				</td>
			</tr>
		</table>
	</form>";
} else {
	Kill(__("You may not use the delete function."));
}	

