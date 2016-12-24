<?php

$title = __("Delete the user");

makeCrumbs(array(actionlink('deleteuser') => __("Delete User")));

/*	Make 18 checks:
	1) If the user can edit his own profile: you don't want to be a hypocrite. This one is checked twice, with one of them being a message, while the other is displaying the page.
	2) If the user can edit other profiles. This is also checked twice, with one of them displaying the page and the other is a message.
	3) If the user has the delete permission set to on. 
	4) If the user has the ban permission set to on: You need to ban the user in order to delete, so it logical that you need that permission on. This is checked twice, one for the message and one to display the delete page. 
	5) If the user is above rank ID -1, it'll block it. It'll only delete the user if the user is below rank ID 0. This one is checked four times, twice being a message and the other two being to display the page. You'll need to ban the user before you delete him.
	6) If the user trying to delete another user is even logged in.
	7) If the user being deleted even exists.
	8) If the user trying to delete is banned and all the nessesary permissions are deactivated (it should normally be deactivated, but who knows) :P: Just because you're banned doesn't mean you have to go nuts and delete users. It'll be a strongly worded message to put some sence into them. Not like the end user will get some sence due to that user getting banned.
	9) If the user trying to delete has a lower rank than the one being deleted. This is checked 4 times, twice being the message, while the other two is displaying the nuke page.

	Yes, I know, its a lot of checks, but you have to be secure. Otherwise, you'll have a destroyed board: The delete feature is very powerfull. If ever someone got a hold of the nuke plugin pre-security updates with the nuke permission, your board can be done for, especially considering that recalc is only allowed to be ran by owner. (And no, I'm not going to make it being ran by permissions, at least, not right now.) I'm not sure how MyBB does it (and I don't want to know; it doesn't interest me.).

	ToDo: Make it that you can't delete a temp banned user.
	*/

$uid = (int)$_GET["id"];

$user = fetch(query("select * from {users} where id={0}", $uid));

$userdeleteperms = HasPermission('admin.editusers') && HasPermission('admin.userdelete') && HasPermission('user.editprofile') && HasPermission('admin.banusers');

if(!$loguserid)
	Kill(__("You must be logged in to delete a profile."));

if (!$userdeleteperms && $loguser['banned'])
	Kill(__("You may not use the user nuke due to you being banned. Look, just because your banned doesn't mean you have to ruin it for everyone else who's in your banned user club. Try improving, instead of trying to get revenge on the staff like an immature freak."));

if (!HasPermission('user.editprofile'))
	Kill(__("Don't be such a hypocrite. Before you decide to delete other users, how about check yourself."));

if (!HasPermission('admin.editusers'))
	Kill(__("The deleting function is part of the editing other users function."));

if (!HasPermission('admin.banusers'))
	Kill(__("How do you expect to delete someone while you can't even ban them?"));

if(!$user)
	Kill(__("You cannot delete a user that doesn't exist."));

if ($targetrank >= $myrank)
	Kill(__("You may not delete a user whose level is equal to or above yours."));

if($myrank =< $targetrank)
	Kill(__("You may not delete a user whose level is equal to or above yours."));

if($user["primarygroup"] > 0)
	Kill(__('You can\'t delete a staff member or a normal user. Ban him/her first.'));

if($user["primarygroup"] < 1)
	Kill(__('You can\'t delete a staff member or a normal user. Ban him/her first.'));

if ($userdeleteperms && $user["primarygroup"] < 1 && $user["primarygroup"] > 0 && $myrank >= $targetrank && $targetrank =< $myrank) {
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
			query("delete pt from {pmsgs_text} pt
				left join {pmsgs} p on pt.pid = p.id
				where p.userfrom={0} or p.userto={0}", $uid);
			query("delete p from {pmsgs} p
				where p.userfrom={0} or p.userto={0}", $uid);

			//Delete THE USER ITSELF
			query("delete from {users}
					where id={0}", $uid);

			//and then IP BAN HIM
			query("insert into {ipbans} (ip, reason, date) 
					values ({0}, {1}, 0)
					on duplicate key update ip=ip", $user["lastip"], "Deleting ".$user["name"]);

			//Log that the user is deleted: Just a safety check if an admin wants to know what happend to that user, and not make the user dissapear without a trace. It also now displays his ID (In case the delete function didn't delete something and an account has some problems, you know if its linked or not) and who nuked him.
			Report("[b]".$loguser['name']."[/] successfully deleted ".$user["name"]." (#".$uid.").");

			echo "User deleted!<br/>";
			echo "You will need to ", actionLinkTag("Recalculate statistics now", "recalc");

			throw new KillException();
		} else
			$passwordFailed = true;
	}

	if($passwordFailed) {
		Report("[b]".$loguser['name']."[/] tried to delete ".$user["name"]." (#".$uid.").");
		Alert("Invalid password. Please try again.");
	}
	
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
	Kill(__("You may not use the user delete function."));

