<?php

if(!$loguserid)
	Kill("There's nothing for you. Go away.");

CheckPermission('admin.userdatanuke');

$uid = (int)$_GET["id"];

$user = fetch(query("select * from {users} where id={0}", $uid));

if(!$user)
	kill("User not found");

if($user["powerlevel"] > 0)
	kill("You can't nuke the data of a staff member. Demote him first.");

if(isset($_POST["actionlogin"]))
{

if(isset($_POST["nuke"]))
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
				
		echo "User data nuked!<br/>";
		echo "You will need to ", actionLinkTag("Recalculate statistics now", "recalc");

		throw new KillException();
}
else 
	Kill("You must need to agree nuke the user's data...");

}

echo "<center>
<form name=\"confirmform\" action=\"".actionLink("datanuke", $uid)."\" method=\"post\">
	<table class=\"outline margin width50\">
		<tr class=\"header0\">
			<th colspan=\"2\">
				".__("DATA NUKE!!")."
			</th>
		</tr>
		<tr>
			<td class=\"cell2\">
			</td>
			<td class=\"cell0\">
				".__("WARNING: This permanently and irreversibly delete all posts, threads, private messages and profile comments of a user.")."
				<br/><br/>
				<input type=\"checkbox\" name=\"nuke\">I agree to nuke all data of this user</input>
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<center><input type=\"submit\" name=\"actionlogin\" value=\"".__("DATA NUKE!!")."\" /></center>
			</td>
		</tr>
	</table>
</form></center>";

