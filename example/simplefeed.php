<?php
/*
	captures instagram user posts
*/

	require "vendor/autoload.php";

	use InstaFeed\Profile as Profile;
	$profile = new Profile();

	$feed = $profile->get('wallace.rio');
		
	print_r($feed);