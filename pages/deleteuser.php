<?php

$title = __("Delete the user");

makeCrumbs(array(actionlink('deleteuser') => __("Delete User")));

/*	Make 12 checks:
	1) If the user can edit his own profile: you don't want to be a hypocrite. This one is checked twice, with one of them being a message, while the other is displaying the page.
	2) If the user can edit other profiles. This is also checked twice, with one of them displaying the page and the other is a message.
	3) If the user has the delete permission set to on. 
	4) If the user is above rank ID -1, it'll block it. It'll only delete the user if the user is below rank ID 0. This one is checked four times, twice being a message and the other two being to display the page. You'll need to ban the user before you delete him.
	5) If the user trying to delete another user is logged in.
	6) If the user being deleted even exists
	7) If the user trying to nuke is banned and all the nessesary permissions are deactivated (it should normally be deactivated, but who knows) :P: Just because you're banned doesn't mean you have to go nuts and delete users. It'll be a strongly worded message to put some sence into them. Not like the end user will get some sence due to that user getting banned

	Yes, I know, its a lot of checks, but you have to be secure. Otherwise, you'll have a destroyed board
*/

$uid = (int)$_GET["id"];

$user = fetch(query("select * from {users} where id={0}", $uid));

if(!$loguserid)
	Kill(__("You must be logged in to delete a profile."));

if (!HasPermission('admin.editusers') && !HasPermission('admin.userdelete') && !HasPermission('user.editprofile') && $loguser['banned'])
	Kill(__("You may not use the user nuke due to you being banned. Look, just because your banned doesn't mean you have to ruin it for everyone else who's in your banned user club. Try improving, instead of trying to get revenge on the staff."));

if (!HasPermission('user.editprofile'))
	Kill(__("Don't be such a hypocrite. Before you decide to delete other users, how about check yourself."));

if (!HasPermission('admin.editusers'))
	Kill(__("The deleting function is part of the editing other users function."));

if(!$user)
	Kill(__("You cannot delete a user that doesn't exist."));

if($user["primarygroup"] > -1)
	Kill(__('You can\'t delete a staff member or a normal user. Ban him/her first.'));
else if($user["primarygroup"] < 0)
	Kill(__('You can\'t delete a staff member or a normal user. Ban him/her first.'));

if (HasPermission('admin.editusers') && HasPermission('admin.userdelete') && HasPermission('user.editprofile') && $user["primarygroup"] < 0 && $user["primarygroup"] > -1) {
	$passwordFailed = false;

	if(isset($_POST["currpassword"])) {
		$sha = doHash($_POST['currpassword'].SALT.$loguser['pss']);
		if($loguser['password'] == $sha) {

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

			//Log that the user is deleted: Just a safety check if an admin wants to know what happend to that user, and not make the user dissapear without a trace.
			Report("".$user["name"]."was deleted.");

			echo "User deleted!<br/>";
			echo "You will need to ", actionLinkTag("Recalculate statistics now", "recalc");

			throw new KillException();
		} else
			$passwordFailed = true;
	}

	if($passwordFailed)
		Alert("Invalid password. Please try again.");
	echo "
	<form name=\"confirmform\" action=\"".actionLink("deleteuser", $uid)."\" method=\"post\">
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
} else 
	Kill(__("You may not use the delete function."));

