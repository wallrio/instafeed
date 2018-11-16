<?php
/*
	captures instagram user posts
*/

	require "vendor/autoload.php";

	use InstaFeed\Profile as Profile;
	$profile = new Profile();

	$profile->username('wallace.rio');
	$feed = $profile->get();
		
	print_r($feed);