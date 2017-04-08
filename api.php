<?php 
	/*	
	 *	Pylos API
	 *	Collection of server-side services for the pylos platform.
	 *	
	 *	Version:		1.0.0
	 *	Last modified:	2017 April 01 2031
	 *	Author:			Sean Wittmeyer sean.wittmeyer@zgf.com
	 *
	 *
	 *
	 */
	 
/* 
 *	The Pylos class
 */

require('./config.php');

/* 
 *	The Pylos class
 */

class pylos {

	/* 
	 *	Get a list of blocks
	 */

    function get($url,$args='') {
	    global $context;
	    $url = $url.AUTH_ARGS.$args;
	    curl_setopt($context, CURLOPT_URL, $url);
		$headers[] = 'User-Agent: '.ORIGIN_ACCOUNT.'/'.ORIGIN_REPO;
		curl_setopt($context, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($context, CURLOPT_RETURNTRANSFER, true);
	    return curl_exec($context);
    }
    
	/* 
	 *	Get a list of members of our org
	 */

    function members($url) {
	    // https://api.github.com/orgs/zgfarchitectsllp/members

    }
    
}


/* 
 *	The Pylos class
 */

// how long will this take?
$mtime = explode(" ",microtime()); 
$starttime = $mtime[1] + $mtime[0];; 


// create an object
$pylos = new pylos();

$context = curl_init();
//multiple calls to get() here
$forks = json_decode($pylos->get('https://api.github.com/repos/'.ORIGIN_ACCOUNT.'/'.ORIGIN_REPO.'/forks'));

$repos = array();
foreach ($forks as $fork) {
	$repos[] = array(
		'uri' => $fork->full_name,
		'repo' => $fork->name,
		'user' => $fork->owner->login,
		'created' => $fork->created_at,
		'updated' => $fork->updated_at
	);
}

$return = array(
	'success' => 'success',
	'samples' => array(),
	'last_updated' => date(DATE_ATOM)
);

foreach ($repos as $repo) {
	// get tree
	$tree = json_decode($pylos->get('https://api.github.com/repos/'.$repo['uri'].'/git/trees/master','&recursive=1'));
	$files = $tree->tree;
	$samples = array();
	foreach ($files as $file) {
		// if in name input.json
		if (strpos($file->path, '/input.json')) {
			//get path and name
			$path = explode('/', $file->path);
			$count = count($path)-2;
			$name = $path[$count];
			$path = str_replace('/input.json', '', $file->path);
			// get tags
			$input = json_decode($pylos->get('https://raw.githubusercontent.com/'.$repo['uri'].'/master/'.$path.'/input.json'));
			$keywords = array();
			//var_dump($input);
			foreach ($input->tags as $tag) $keywords[] = $tag;
			foreach ($input->dependencies as $tag) $keywords[] = $tag;
			foreach ($input->components as $tag => $misc) $keywords[] = $tag;
			$tags = $repo['user'].$name.$repo['repo'];
			foreach ($keywords as $tag) $tags .= $tag;
			
			// add to samples
			$samples[] = array(
				'name' => $name,
				'path' => $path,
				'tags' => strtolower($tags)
			);
		}
	}
	
	foreach ($samples as $sample) {
		// add sample block to return array 
		$return['samples'][] = array(
			"owner" => $repo['user'],
			"id" => $sample['name'],
			"fork" => $repo['repo'],
			"created_at" => $repo['created'],
			"modified_at" => $repo['updated'],
			"thumbnail" => 'https://raw.githubusercontent.com/'.$repo['uri'].'/master/'.$sample['path'].'/thumbnail.png',
			"tags" => $sample['tags']
		);
	}
}


curl_close($context);

// how long will this take?
$mtime = explode(" ",microtime()); 
$endtime = $mtime[1] + $mtime[0];; 

$return['load'] = round($endtime - $starttime, 2); 

header('Content-Type: application/json');
print_r(json_encode($return, 64));



?>