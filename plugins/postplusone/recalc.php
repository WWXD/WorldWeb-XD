<?php

startFix();
Query("UPDATE {posts} p SET postplusones =
			(SELECT COUNT(*) FROM {postplusones} pp WHERE pp.post = p.id)
		WHERE 1");
reportFix(__("Counting post +1's&hellip;"));

startFix();
Query("UPDATE {users} u SET postplusonesgiven =
			(SELECT COUNT(*) FROM {postplusones} pp WHERE pp.user = u.id)
		WHERE 1");
reportFix(__("Counting user +1's given&hellip;"));

startFix();
Query("UPDATE {users} u SET postplusones =
			(SELECT COUNT(*) FROM {postplusones} pp 
			LEFT JOIN {posts} p on pp.post = p.id
			WHERE p.user = u.id)
		WHERE 1");
reportFix(__("Counting user +1's received&hellip;"));

