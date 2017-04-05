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

class pylos {

	/* 
	 *	Get a list of blocks
	 */

    function get_blocks() {
        // load list of gists
        
        
        // setup defaults
        $gists = array();
        
        
        // get details for each gist via api
        
        //
        
        $this->model = "VW";
    }
    
	/* 
	 *	Add block to our list
	 */

    function add_block() {
        // load list of gists
        
        
        // setup defaults
        $gists = array();
        
        
        // get details for each gist via api
        
        //
        
        $this->model = "VW";
    }
    
    
}


/* 
 *	The Pylos class
 */


// create an object
$pylos = new pylos();

// show object properties
echo $pylos->model; 



?>