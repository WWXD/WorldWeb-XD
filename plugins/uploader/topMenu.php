<?php
if(isAllowed("viewUploader"))
	$navigation->add(new PipeMenuLinkEntry(__("Uploader"), "uploader", "", "", "cloud-upload"));
