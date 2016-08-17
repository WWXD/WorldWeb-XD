<?php
if (!defined('BLARG')) die();

//Category/forum editor -- By Nikolaj
//Secured and improved by Dirbaio
// Adapted to Blargboard by StapleButter.

$title = __("Edit forums");

CheckPermission('admin.editforums');

MakeCrumbs(array(actionLink("admin") => __("Admin"), actionLink("editfora") => __("Edit forum list")));

/**
	Okay. Much like the category editor, now the action is specified by $_POST["action"].

	Possible actions are:
	- updateforum: Updates the settings of a forum in the DB.
	- addforum: Adds a new forum to the DB.
	- deleteforum: Deletes a forum from the DB. Also, depending on $_GET["threads"]: (NOT YET)
		- "delete": DELETES all threads and posts in the DB.
		- "trash": TRASHES all the threads (move to trash and close)
		- "move": MOVES the threads to forum ID $_POST["threadsmove"]
		- "leave": LEAVES all the threads untouched in the DB (like the old forum editor. Not recommended. Will cause "invisible posts" that will still count towards user's postcounts)

	- forumtable: Returns the forum table for the left panel.
	- editforum: Returns the HTML code for the forum settings in right panel.
		- editforumnew: Returns the forum edit box to create a new forum. This way the huge HTML won't be duplicated in the code.
		- editforum: Returns the forum edit box to edit a forum.
		
		
	PERMISSION EDITING PRESETS
	
	* Full: full access
	* Standard: view, post threads, reply to threads
	* Reply-only: view, reply to threads (ie announcement forum)
	* Read-only: view
	* No access: (none)
	* Custom

**/

$noFooter = true;

function recursionCheck($fid, $cid)
{
	if ($cid >= 0) return $cid;
	
	$check = array();
	for (;;)
	{
		$check[] = -$cid;
		if ($check[0] == $fid)
			dieAjax(__('Endless recursion detected; choose another parent for this forum.'));
		
		$cid = FetchResult("SELECT catid FROM {forums} WHERE id={0}", -$cid);
		if ($cid >= 0) return $cid;
	}
}

if (isset($_REQUEST['action']) && isset($_POST['key']))
{
	//Check for the key
	if ($loguser['token'] != $_POST['key'])
		Kill(__("No."));
			
	switch($_REQUEST['action'])
	{
		case 'updateforum':

			//Get new forum data
			$id = (int)$_POST['id'];
			$title = $_POST['title'];
			if($title == "") dieAjax(__("Title can't be empty."));
			$description = $_POST['description'];
			$category = ($_POST['ptype'] == 0) ? (int)$_POST['category'] : -(int)$_POST['pforum'];
			$forder = (int)$_POST['forder'];
			
			$catid = recursionCheck($id, $category);
			$board = FetchResult("SELECT board FROM {categories} WHERE id={0}", $catid);
			
			// L/R tree shiz
			$oldlr = Fetch(Query("SELECT l,r FROM {forums} WHERE id={0}", $id));
			$diff = $oldlr['r'] - $oldlr['l'] + 1;
			
			$c = Query("SELECT id FROM {forums} WHERE l>{0} AND r<{1}", $oldlr['l'], $oldlr['r']);
			$children = array();
			while ($blarg = Fetch($c)) $children[] = $blarg['id'];
			
			Query("UPDATE {forums} SET l=l-{0} WHERE l>{1}", $diff, $oldlr['r']);
			Query("UPDATE {forums} SET r=r-{0} WHERE r>{1}", $diff, $oldlr['r']);
			
			$l = FetchResult("SELECT MAX(r) FROM {forums} WHERE catid={0} AND (forder<{1} OR (forder={1} AND id<{2}))", $category, $forder, $id);
			if (!$l || $l == -1)
			{
				if ($category >= 0)
					$l = 0;
				else
					$l = FetchResult("SELECT l FROM {forums} WHERE id={0}", -$category);
			}
			$l++;
			$r = $l + $diff - 1;
			
			if (!empty($children))
			{
				Query("UPDATE {forums} SET l=l+{0} WHERE l>={1} AND id NOT IN ({2c})", $diff, $l, $children);
				Query("UPDATE {forums} SET r=r+{0} WHERE r>={1} AND id NOT IN ({2c})", $diff, $l, $children);
				Query("UPDATE {forums} SET l=l+{0}, r=r+{0} WHERE id IN ({1c})", $l-$oldlr['l'], $children);
			}
			else
			{
				Query("UPDATE {forums} SET l=l+{0} WHERE l>={1}", $diff, $l);
				Query("UPDATE {forums} SET r=r+{0} WHERE r>={1}", $diff, $l);
			}
			
			//Send it to the DB
			Query("UPDATE {forums} SET title = {0}, description = {1}, catid = {2}, forder = {3}, hidden={4}, redirect={5}, offtopic={6}, board={8}, l={9}, r={10} WHERE id = {7}", 
				$title, $description, $category, $forder, (int)$_POST['hidden'], $_POST['redirect'], (int)$_POST['offtopic'], $id, $board, $l, $r);
				
			SetPerms($id);
			
			dieAjax('Ok');
			break;
			
		case 'updatecategory':

			//Get new cat data
			$id = (int)$_POST['id'];
			$name = $_POST['name'];
			if($name == "") dieAjax(__("Name can't be empty."));
			$corder = (int)$_POST['corder'];
			
			$board = $_POST['board'];
			if (!isset($forumBoards[$board])) $board = '';

			//Send it to the DB
			Query("UPDATE {categories} SET name = {0}, corder = {1}, board={3} WHERE id = {2}", $name, $corder, $id, $board);
			
			// update boards of forums in this category
			$blarg = Fetch(Query("SELECT MIN(l) minl, MAX(r) maxr FROM {forums} WHERE catid={0}", $id));
			Query("UPDATE {forums} SET board={0} WHERE l>={1} AND r<={2}", $board, $blarg['minl'], $blarg['maxr']);
			
			// no need to update the L/R tree. Category order doesn't matter.
			
			dieAjax('Ok');
			break;

		case 'addforum':

			//Get new forum data
			$title = $_POST['title'];
			if($title == "") dieAjax(__("Title can't be empty."));
			$description = $_POST['description'];
			$category = ($_POST['ptype'] == 0) ? (int)$_POST['category'] : -(int)$_POST['pforum'];
			$forder = (int)$_POST['forder'];

			//Figure out the new forum ID.
			//I think it'd be better to use InsertId, but...
			$newID = FetchResult("SELECT id+1 FROM {forums} WHERE (SELECT COUNT(*) FROM {forums} f2 WHERE f2.id={forums}.id+1)=0 ORDER BY id ASC LIMIT 1");
			if($newID < 1) $newID = 1;
			
			$catid = recursionCheck($newID, $category);
			$board = FetchResult("SELECT board FROM {categories} WHERE id={0}", $catid);
			
			// L/R tree shiz
			$l = FetchResult("SELECT MAX(r) FROM {forums} WHERE catid={0} AND (forder<{1} OR (forder={1} AND id<{2}))", $category, $forder, $newID);
			if (!$l || $l == -1)
			{
				if ($category >= 0)
					$l = 0;
				else
					$l = FetchResult("SELECT l FROM {forums} WHERE id={0}", -$category);
			}
			$l++;
			Query("UPDATE {forums} SET l=l+2 WHERE l>={0}", $l);
			Query("UPDATE {forums} SET r=r+2 WHERE r>={0}", $l);
			$r = $l + 1;

			//Add the actual forum
			Query("INSERT INTO {forums} (`id`, `title`, `description`, `catid`, `forder`, `hidden`, `redirect`, `offtopic`, `board`, `l`, `r`) VALUES ({0}, {1}, {2}, {3}, {4}, {5}, {6}, {7}, {8}, {9}, {10})", 
				$newID, $title, $description, $category, $forder, (int)$_POST['hidden'], $_POST['redirect'], (int)$_POST['offtopic'], $board, $l, $r);
			
			$id = InsertId();
			SetPerms($id);

			dieAjax('Ok|'.$id);
			break;

		case 'addcategory':

			//Get new cat data
			$name = $_POST['name'];
			if($name == "") dieAjax(__("Name can't be empty."));
			$corder = (int)$_POST['corder'];
			
			$board = (int)$_POST['board'];
			if (!isset($forumBoards[$board])) $board = '';

			Query("INSERT INTO {categories} (`name`, `corder`, `board`) VALUES ({0}, {1}, {2})", $name, $corder, $board);

			dieAjax('Ok|'.InsertId());
			break;
			
		case 'deleteforum':
			//Get Forum ID
			$id = (int)$_POST['id'];

			//Check that forum exists
			$rForum = Query("SELECT id FROM {forums} WHERE id={0}", $id);
			if (!NumRows($rForum))
				dieAjax("No such forum.");

			//Check that forum has threads.
			if (FetchResult("SELECT COUNT(*) FROM {threads} WHERE forum={0}", $id) > 0)
				dieAjax(__('Cannot delete a forum that contains threads.'));
				
			//
			
			// L/R tree shiz
			$oldlr = Fetch(Query("SELECT l,r FROM {forums} WHERE id={0}", $id));
			$diff = $oldlr['r'] - $oldlr['l'] + 1;
			
			$c = FetchResult("SELECT COUNT(*) FROM {forums} WHERE l>{0} AND r<{1}", $oldlr['l'], $oldlr['r']);
			if ($c > 0)
				dieAjax(__('Cannot delete a forum that has subforums. Delete them or move them first.'));
			
			Query("UPDATE {forums} SET l=l-{0} WHERE l>{1}", $diff, $oldlr['r']);
			Query("UPDATE {forums} SET r=r-{0} WHERE r>{1}", $diff, $oldlr['r']);

			//Delete
			Query("DELETE FROM `{forums}` WHERE `id` = {0}", $id);
			dieAjax('Ok');
			break;
			
		case 'deletecategory':
			//Get Cat ID
			$id = (int)$_POST['id'];

			//Check that forum exists
			$rCat = Query("SELECT id FROM {categories} WHERE id={0}", $id);
			if (!NumRows($rCat))
				dieAjax(__("No such category."));
				
			if (FetchResult("SELECT COUNT(*) FROM {forums} WHERE catid={0}", $id) > 0)
				dieAjax(__('Cannot delete a category that contains forums.'));

			//Delete
			Query("DELETE FROM `{categories}` WHERE `id` = {0}", $id);
			dieAjax('Ok');
			break;
	}
}

if (isset($_REQUEST['action']))
{
	switch ($_REQUEST['action'])
	{
		case 'forumtable':
			WriteForumTableContents();
			dieAjax('');
			break;

		case 'editforumnew':
		case 'editforum':

			//Get forum ID
			if($_REQUEST['action'] == 'editforumnew')
				$fid = -1;
			else
				$fid = (int)$_GET['fid'];

			WriteForumEditContents($fid);
			dieAjax('');
			break;

		case 'editcategorynew':
		case 'editcategory':

			//Get cat ID
			if($_REQUEST['action'] == 'editcategorynew')
				$cid = -1;
			else
				$cid = (int)$_GET['cid'];

			WriteCategoryEditContents($cid);
			dieAjax('');
			break;
	}
}



//Main code.

?>
	<style type="text/css">
		.permselect, .permselect > option {color: black!important;}
		.perm { display: inline-block; width: 100%; box-sizing: border-box; -moz-box-sizing: border-box; padding-right: 7px; }
		.perm .permselect { float: right; }
	</style>
<?php

echo '
<script src="'.resourceLink('js/editfora.js').'" type="text/javascript"></script>
<div id="editcontent" style="float: right; width: 49.7%;">
	&nbsp;
</div>
<div id="flist">';

WriteForumTableContents();

echo '
</div>';




//Helper functions

// $fid == -1 means that a new forum should be made :)
function WriteForumEditContents($fid)
{
	global $loguser;

	//Get all categories.
	$rCats = Query("SELECT * FROM {categories} ORDER BY board, corder, id");

	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat;
		
	$rFora = Query("SELECT * FROM {forums} ORDER BY l");

	$fora = array();
	$cid = -1;
	while ($forum = Fetch($rFora))
	{
		if ($forum['catid'] >= 0) $cid = $forum['catid'];
		$fora[$cid][] = $forum;
	}
	
	$g = Query("SELECT id,name,type,color_unspec FROM {usergroups} ORDER BY type, rank");
	$groups = array();
	while ($group = Fetch($g))
	{
		$name = htmlspecialchars($group['name']);
		if ($group['type'] == 0)
			$name = '<strong style="color:'.htmlspecialchars($group['color_unspec']).';">'.$name.'</strong>';
		
		$groups[$group['id']] = array('name' => $name, 'permFields' => '');
	}

	if($fid != -1)
	{
		$rForum = Query("SELECT * FROM {forums} WHERE id={0}", $fid);
		if (!NumRows($rForum))
		{
			Kill(__("Forum not found."));
		}
		$forum = Fetch($rForum);
		
		$candelete = FetchResult("SELECT COUNT(*) FROM {threads} WHERE forum={0}", $fid) == 0;
		$c = FetchResult("SELECT COUNT(*) FROM {forums} WHERE l>{0} AND r<{1}", $forum['l'], $forum['r']);
		if ($c > 0) $candelete = false;

		$title = htmlspecialchars($forum['title']);
		$description = htmlspecialchars($forum['description']);
		$catselect = MakeCatSelect('cat', $cats, $fora, $forum['catid'], $forum['id']);
		$forder = $forum['forder'];
		
		$fperms = GetForumPerms($fid);
		foreach ($groups as $gid=>$group)
			$groups[$gid]['permFields'] = PermFields($gid, $fperms[$gid]);

		$boxtitle = __("Editing forum ").$title;
		
		$fields = array
		(
			'title' => '<input type="text" name="title" value="'.$title.'" size=64>',
			'description' => '<input type="text" name="description" value="'.$description.'" size=80>',
			'parent' => $catselect,
			'order' => '<input type="text" name="forder" value="'.$forder.'" size=3>',
			'redirect' => '<input type="text" name="redirect" value="'.htmlspecialchars($forum['redirect']).'" size=80>',
			'hidden' => '<label><input type="checkbox" name="hidden" value="1"'.($forum['hidden']?' checked="checked"':'').'> '.__('Hidden').'</label>',
			'offtopic' => '<label><input type="checkbox" name="offtopic" value="1"'.($forum['offtopic']?' checked="checked"':'').'> '.__('Off-topic').'</label>',
			
			'btnSave' => '<button onclick="changeForumInfo('.$fid.'); return false;">Save</button>',
			'btnDelete' => '<button '.($candelete ? 'onclick="deleteForum(); return false;"' : 'disabled="disabled"').'>Delete</button>',
		);
		$delMessage = $candelete ? '' : ($c ? __('Before deleting a forum, delete or move its subforums.') : __('Before deleting a forum, remove all threads from it.'));
	}
	else
	{
		$catselect = MakeCatSelect('cat', $cats, $fora, 1, -1);
		
		$fperms = GetForumPerms(0);
		foreach ($groups as $gid=>$group)
			$groups[$gid]['permFields'] = PermFields($gid, $fperms[$gid]);

		$boxtitle = __("New forum");

		$fields = array
		(
			'title' => '<input type="text" name="title" value="" size=64>',
			'description' => '<input type="text" name="description" value="" size=80>',
			'parent' => $catselect,
			'order' => '<input type="text" name="forder" value="0" size=3>',
			'redirect' => '<input type="text" name="redirect" value="" size=80>',
			'hidden' => '<label><input type="checkbox" name="hidden" value="1"> '.__('Hidden').'</label>',
			'offtopic' => '<label><input type="checkbox" name="offtopic" value="1"> '.__('Off-topic').'</label>',
			
			'btnSave' => '<button onclick="addForum(); return false;">Save</button>',
			'btnDelete' => '',
		);
		$delMessage = '';
	}

	echo "
	<form method=\"post\" id=\"forumform\" action=\"".htmlentities(actionLink("editfora"))."\">
	<input type=\"hidden\" name=\"key\" value=\"".$loguser['token']."\">
	<input type=\"hidden\" name=\"id\" value=\"$fid\">";
	
	RenderTemplate('form_editforum', array('formtitle' => $boxtitle, 'fields' => $fields, 'groups' => $groups, 'delMessage' => $delMessage));
	
	echo "
	</form>";
}


// $fid == -1 means that a new forum should be made :)
function WriteCategoryEditContents($cid)
{
	global $loguser, $forumBoards;
	
	$boardlist = '';

	if($cid != -1)
	{
		$rCategory = Query("SELECT * FROM {categories} WHERE id={0}", $cid);
		if (!NumRows($rCategory))
		{
			Kill("Category not found.");
		}
		$cat = Fetch($rCategory);
		
		$candelete = FetchResult("SELECT COUNT(*) FROM {forums} WHERE catid={0}", $cid) == 0;

		$name = htmlspecialchars($cat['name']);
		$corder = $cat['corder'];
		
		if (count($forumBoards) > 1)
		{
			foreach ($forumBoards as $bid=>$bname)
			{
				$boardlist .= '<label><input type="radio" name="board" value="'.htmlspecialchars($bid).'"'.($cat['board']==$bid ? ' checked="checked"':'').'> '.htmlspecialchars($bname).'</label>';
			}
		}

		$boxtitle = __("Editing category ").$name;
			
		$fields = array
		(
			'name' => '<input type="text" name="name" value="'.$name.'" size=64>',
			'order' => '<input type="text" name="corder" value="'.$corder.'" size=3>',
			'board' => $boardlist,
			
			'btnSave' => '<button onclick="changeCategoryInfo('.$cid.'); return false;">Save</button>',
			'btnDelete' => '<button '.($candelete ? 'onclick="deleteCategory(); return false;"' : 'disabled="disabled"').'>Delete</button>',
		);
		$delMessage = $candelete ? '' : __('Before deleting a category, remove all forums from it.');
	}
	else
	{		
		if (count($forumBoards) > 1)
		{
			foreach ($forumBoards as $bid=>$bname)
			{
				$boardlist .= '<label><input type="radio" name="board" value="'.htmlspecialchars($bid).'"'.($bid=='' ? ' checked="checked"':'').'> '.htmlspecialchars($bname).'</label>';
			}
		}
		
		$boxtitle = __("New category");
		
		$fields = array
		(
			'name' => '<input type="text" name="name" value="" size=64>',
			'order' => '<input type="text" name="corder" value="0" size=3>',
			'board' => $boardlist,
			
			'btnSave' => '<button onclick="addCategory(); return false;">Save</button>',
			'btnDelete' => '',
		);
		$delMessage = '';
	}

	echo "
	<form method=\"post\" id=\"forumform\" action=\"".htmlentities(actionLink("editfora"))."\">
	<input type=\"hidden\" name=\"key\" value=\"".$loguser["token"]."\">
	<input type=\"hidden\" name=\"id\" value=\"$cid\">";
	
	RenderTemplate('form_editcategory', array('formtitle' => $boxtitle, 'fields' => $fields, 'delMessage' => $delMessage));

	echo "
	</form>";
}


function WriteForumTableContents()
{
	global $forumBoards;
	
	$boards = array();
	$cats = array();
	$forums = array();
	
	foreach ($forumBoards as $bid=>$bname)
		$boards[$bid] = array('id' => $bid, 'name' => $bname, 'cats' => array());
	
	$rCats = Query("SELECT * FROM {categories} ORDER BY board, corder, id");
	while ($cat = Fetch($rCats))
	{
		$cats[$cat['board']][$cat['id']] = $cat;
	}
	
	$rForums = Query("SELECT * FROM {forums} ORDER BY l");
	$cid = -1; $lastr = 0; $level = 1;
	while ($forum = Fetch($rForums))
	{
		if ($forum['catid'] >= 0) $cid = $forum['catid'];
		
		if ($lastr)
		{
			if ($forum['r'] < $lastr) // we went up one level
				$level++;
			else // we went down a few levels maybe
				$level -= $forum['l'] - $lastr - 1;
		}
		$forum['level'] = $level;
		$lastr = $forum['r'];
		
		$forums[$cid][$forum['id']] = $forum;
	}
	
	$btnNewForum = empty($cats) ? '' : '<button onclick="newForum();">'.__("Add forum").'</button>';
	$btnNewCategory = '<button onclick="newCategory();">'.__("Add category").'</button>';
	
	RenderTemplate('editfora_list', array(
		'boards' => $boards,
		'cats' => $cats,
		'forums' => $forums,
		'selectedForum' => (int)$_GET['s'],
		
		'btnNewForum' => $btnNewForum,
		'btnNewCategory' => $btnNewCategory,
	));
}



function MakeCatSelect($i, $cats, $fora, $v, $fid)
{
	global $forumBoards;
	
	$r = '
			<label><input type="radio" name="ptype" value="0"'.($v>=0 ? ' checked="checked"':'').'>Category:</label>
			<select name="category">';
	foreach ($cats as $opt)
	{
		$r .= '
				<option value="'.$opt['id'].'"'.($v == $opt['id'] ? ' selected="selected"' : '').'>
					'.htmlspecialchars($opt['name']).'
				</option>';
	}
	$r .= '
			</select>';
			
	$r .= '
			<br>
			<label><input type="radio" name="ptype" value="1"'.($v<0 ? ' checked="checked"':'').'>Forum:</label>
			<select name="pforum">';
			
	foreach ($cats as $cid=>$cat)
	{
		if (!isset($fora[$cid]) || empty($fora[$cid])) continue;
		
		$cname = $cat['name'];
		if ($cat['board']) $cname = $forumBoards[$cat['board']].' - '.$cname;
		
		$r .= '
			<optgroup label="'.htmlspecialchars($cname).'">';
		
		$lastr = 0; $level = 0;
		foreach ($fora[$cid] as $forum)
		{
			if ($lastr)
			{
				if ($forum['r'] < $lastr) // we went up one level
					$level++;
				else // we went down a few levels maybe
					$level -= $forum['l'] - $lastr - 1;
			}
			$lastr = $forum['r'];
			
			if ($forum['id'] == $fid) continue;
			//if ($forum['redirect']) continue;
			
			$r .= '				
				<option value="'.$forum['id'].'"'.($forum['id'] == -$v ? ' selected="selected"':'').'>'
				.str_repeat('&nbsp; &nbsp; ', $level).htmlspecialchars($forum['title'])
				.'</option>
';
		}
		
		$r .= '
			</optgroup>';
	}
	
	$r .= '
			</select>';
			
	return $r;
}

// returns: per-group permissions
// -2: deny (locked due to global perm)
// -1: deny
// 0: neutral
// 1: allow
function GetForumPerms($fid)
{
	$ret = array();
	
	// global perms
	$perms = Query("SELECT id,perm,value FROM {permissions} WHERE applyto=0 AND (SUBSTR(perm,1,6)={0} OR SUBSTR(perm,1,4)={1}) AND arg=0", 
		'forum.', 'mod.');

	while ($perm = Fetch($perms))
	{
		$val = $perm['value'];
		if ($val == -1)
			$ret[$perm['id']][$perm['perm']] = -2;
	}
	
	if (!$fid) return $ret;
	
	// specific perms
	$perms = Query("SELECT id,perm,value FROM {permissions} WHERE applyto=0 AND (SUBSTR(perm,1,6)={0} OR SUBSTR(perm,1,4)={1}) AND arg={2}", 
		'forum.', 'mod.', $fid);

	while ($perm = Fetch($perms))
	{
		$val = $perm['value'];
		
		if (isset($ret[$perm['id']][$perm['perm']]))
			$curval = $ret[$perm['id']][$perm['perm']];
		else 
			$curval = 0;
			
		if ($curval == -2) continue;
		/*if ($curval == 0)	// neutral -- need specific=allow
		{
			if ($val == 0) $val = -1;
		}
		else // curval=1 -- allow
		{
			if ($val == 0) $val = 1;
		}*/
		
		$ret[$perm['id']][$perm['perm']] = $val;
	}
	
	return $ret;
}

// val=-2: selector locked to Deny
function PermSelect($name, $val, $neutral)
{
	if (!$val && !$neutral) $val = -1;
	
	if ($val == -2)
		return '
		<select class="permselect" name="'.str_replace('.', '_', $name).'" disabled="disabled">
			<option value="-2" selected="selected" style="background:#f88;">'.__('Deny').'</option>
		</select>';
	
	return '
		<select class="permselect" name="'.str_replace('.', '_', $name).'">
			<option value="-1" '.($val==-1 ? 'selected="selected"':'').' style="background:#f88;">'.__('Deny').'</option>
			'.($neutral ? '<option value="0" '.($val==0 ? 'selected="selected"':'').' style="background:#ff8;">'.__('Neutral').'</option>':'').'
			<option value="1" '.($val==1 ? 'selected="selected"':'').' style="background:#8f8;">'.__('Allow').'</option>
		</select>';
}

function PermFields($gid, $gperms)
{
	global $permDescs;
	
	$ret = '<table class="layout-table"><tr>';
	$i = 0;
	$perrow = 2;
	
	foreach ($permDescs['forum'] as $perm=>$label)
	{
		if (isset($gperms[$perm])) $pval = $gperms[$perm];
		else $pval = 0;
		
		$ret .= '<td><label class="perm">'.$label.': '.PermSelect($perm.'['.$gid.']', $pval, true).'</label></td>';
		
		$i++;
		if (($i % $perrow) == 0) $ret .= '</tr><tr>';
	}
	
	foreach ($permDescs['mod'] as $perm=>$label)
	{
		if (isset($gperms[$perm])) $pval = $gperms[$perm];
		else $pval = 0;
		
		$ret .= '<td><label class="perm">'.$label.': '.PermSelect($perm.'['.$gid.']', $pval, true).'</label></td>';
		
		$i++;
		if (($i % $perrow) == 0) $ret .= '</tr><tr>';
	}
	
	return $ret.'</tr></table>';
}

function SetPerms($fid)
{
	global $usergroups, $permDescs;
	
	foreach ($usergroups as $gid=>$group)
	{
		foreach ($permDescs['forum'] as $perm=>$label)
		{
			$blarg = str_replace('.', '_', $perm);
			
			if (isset($_POST[$blarg][$gid]))
				$val = $_POST[$blarg][$gid];
			else
				$val = -2;
				
			if ($val != -2)
			{
				if ($val == 0)
					Query("DELETE FROM {permissions} WHERE applyto=0 AND id={0} AND perm={1} AND arg={2}",
						$gid, $perm, $fid);
				else
					Query("INSERT INTO {permissions} (applyto,id,perm,arg,value) VALUES (0,{0},{1},{2},{3})
						ON DUPLICATE KEY UPDATE value={3}",
						$gid, $perm, $fid, $val);
			}
		}
		
		foreach ($permDescs['mod'] as $perm=>$label)
		{
			$blarg = str_replace('.', '_', $perm);
			
			if (isset($_POST[$blarg][$gid]))
				$val = $_POST[$blarg][$gid];
			else
				$val = -2;
				
			if ($val != -2)
			{
				if ($val == 0)
					Query("DELETE FROM {permissions} WHERE applyto=0 AND id={0} AND perm={1} AND arg={2}",
						$gid, $perm, $fid);
				else
					Query("INSERT INTO {permissions} (applyto,id,perm,arg,value) VALUES (0,{0},{1},{2},{3})
						ON DUPLICATE KEY UPDATE value={3}",
						$gid, $perm, $fid, $val);
			}
		}
	}
}
