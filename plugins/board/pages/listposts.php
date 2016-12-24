<?php
//  AcmlmBoard XD - Posts by user viewer
//  Access: all
if (!defined('BLARG')) die();

if(!isset($_GET['id']))
	Kill(__("User ID unspecified."));

$id = (int)$_GET['id'];

$rUser = Query("select * from {users} where id={0}", $id);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));

$title = __("Post list");


$total = FetchResult("
			SELECT
				count(p.id)
			FROM
				{posts} p
				LEFT JOIN {threads} t ON t.id=p.thread{$extrashit}
			WHERE p.user={0} AND t.forum IN ({1c})",
		$id, ForumsWithPermission('forum.viewforum'));


$ppp = $loguser['postsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$ppp) $ppp = 25;


$rPosts = Query("	SELECT
				p.*,
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.(_userfields), u.(rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock,fulllayout),
				ru.(_userfields),
				du.(_userfields),
				t.id thread, t.title threadname,
				f.id fid
			FROM
				{posts} p
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {users} ru ON ru.id=pt.user
				LEFT JOIN {users} du ON du.id=p.deletedby
				LEFT JOIN {threads} t ON t.id=p.thread
				LEFT JOIN {forums} f ON f.id=t.forum
				LEFT JOIN {categories} c ON c.id=f.catid
			WHERE u.id={1} AND f.id IN ({4c}){$extrashit}
			ORDER BY date ASC LIMIT {2u}, {3u}", $loguserid, $id, $from, $ppp, ForumsWithPermission('forum.viewforum'));

$numonpage = NumRows($rPosts);

$uname = $user["name"];
if($user["displayname"])
	$uname = $user["displayname"];

MakeCrumbs(array(actionLink("profile", $id, "", $user["name"]) => htmlspecialchars($uname),'' =>  __("List of posts")));

$pagelinks = PageLinks(actionLink("listposts", $id, "from=", $user['name']), $ppp, $from, $total);

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));

if(NumRows($rPosts))
{
	while($post = Fetch($rPosts))
		MakePost($post, POST_NORMAL, array('threadlink'=>1, 'tid'=>$post['thread'], 'fid'=>$post['fid'], 'noreplylinks'=>1));
}
else
	Alert('This user has no posts.', 'Notice');

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));

?>
