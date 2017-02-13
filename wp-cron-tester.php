<?php

/**
 * Plugin Name: WP CRON Tester
 * Description: This plugin adds a test CRON job output in single posts for quickly checking if WordPress CRON is running ok. 
 * Author: Con Schneider
 * Author URI: http://conschneider.de/
 * License: GPLv2 or later
 * Text Domain: crte-
 * Version: 1.1
 * Domain Path: /languages/
 
 */

if ( ! defined( 'ABSPATH' ) ) 
{ 
    exit; // Exit if accessed directly
}


		//register own interval of 5 seconds to test cron fast
		add_filter( 'cron_schedules', 'crte_add_cron_intervals' );

		function crte_add_cron_intervals( $schedules ) 
		{
			  $schedules['5seconds'] = array
			  ( 
			      'interval' => 5, // Intervals are listed in seconds
			      'display' => __('Every 5 Seconds') // Easy to read display name
		   	  );
		   		 return $schedules; // Do not forget to give back the list of schedules!
		}

		//schedule our custom interval so it runs
		add_action( 'crte_cron_hook', 'crte_cron_output' );

		if( !wp_next_scheduled( 'crte_cron_hook' ) ) 
		{
		   wp_schedule_event( time(), '5seconds', 'crte_cron_hook' );
		}

		//output serious test content :)

		function crte_cron_output() 
		{	
				$test_cron_output = "<h3>CRON Test Job Successful:</h3>
			    Oh Lookie! This is your scheduled cron, grinding out some hardcore tasks...Maximum Effort for the win!<br/><figure><img src='../wp-content/plugins/wp-cron-tester/cron_output.jpg'/><figcaption>Deadpool approves.</figcaption></figure>";
			    echo "$test_cron_output";

		}

		// tear down test CRON at plugin deactivation
		register_deactivation_hook( __FILE__, 'crte_deactivate' );

		function crte_deactivate() {
		   $timestamp = wp_next_scheduled( 'crte_cron_hook' );
		   wp_unschedule_event($timestamp, 'crte_cron_hook' );
		}



// Hook output to main post content
add_filter('the_content', 'crte_print_tasks');

function crte_print_tasks($content) 

{	
	//load the wp.config.php for access
	require_once ("wp-config.php"); 

	//get boolean of constants and store in variables for reliable access instead of direct access
	if (is_single())
	{
			function crte_getconst($const)
			{
			    //return value if set, if not set return null instead to prevent errors.
			    return (defined($const)) ? constant($const) : null;
			}
		
	//get both constant values
	$disable_const = crte_getconst('DISABLE_WP_CRON');
	$alternative_const = crte_getconst('ALTERNATE_WP_CRON');

	}
	//output only on single post page
	if(is_single())
	{
		
		//get timestamp for our test CRON for quick test
		$timestamp = wp_next_scheduled( 'crte_cron_hook' );
					
   			
		//output CRON info
		echo "<h5>CRON Test Job START:</h5>" ;
		echo "<br>CRON Test Job Timestamp: <strong>" . $timestamp . "</strong><br>";
		echo "<i>This value changes every 5 seconds. Reload this page to test.</i>";
		echo "<br><br><hr>";
		echo "<strong>How do I know my CRON works?</strong>";
		echo "<br><br>The plugin adds a test CRON Job.";
		echo "<br><br>→ Refresh this page after 5 seconds. <br>If the timestamp value changes, CRON is running ok. <br><br>→ Visit the output page by clicking on the link below. <br><a target='_blank' href='../wp-cron.php'>View Test CRON Job Output</a>. <br>If the output is visible and stays, CRON is running ok.";
		echo '';
		echo "<br><i>If the link does not work, visit <code>http://YOURSITEURL/wp-cron.php</code></i>.";
		echo "<br><br>• If the number value stays the same or the output is blank, CRON is in trouble.";
		echo "<h5>CRON Paramenter Status:</h5>";
		
		//output CRON parameter info
			if ($disable_const)
		   			{
		   				echo "DISABLE_WP_CRON is set in your <code>wp-config.php</code> and preventing CRON jobs from running.<br>";
		   			} 

		   	if (!$disable_const && defined('DISABLE_WP_CRON')) 
					{
						echo "DISABLE_WP_CRON is set in your <code>wp-config.php</code> but deactivated: set to <code>false</code>.<br>";
					} 

			if (!defined('DISABLE_WP_CRON'))
					{
						echo "DISABLE_WP_CRON is not set in your <code>wp-config.php</code>.<br>"; 
					}
			
			
			if ($alternative_const)
		   			{
		   				echo "ALTERNATE_WP_CRON is set in your <code>wp-config.php</code> and overwriting default CRON.<br>";
		   			} 


		   	if (!$alternative_const && defined('ALTERNATE_WP_CRON'))
					{
						echo "ALTERNATE_WP_CRON is set in your <code>wp-config.php</code> but deactivated: set to <code>false</code>.<br>";
					} 

			if (!defined('ALTERNATE_WP_CRON'))
					{
						echo "ALTERNATE_WP_CRON is not set in your <code>wp-config.php</code>.<br>"; 
					}
			
			if (!$disable_const && !defined('DISABLE_WP_CRON') && !$alternative_const && !defined('ALTERNATE_WP_CRON')) 
					{
						echo "<br>No CRON parameters found in <code>wp-config.php</code>. <br>CRON should run OK.<br>";
					} 
			

					echo '<br><br>For further information: <a href="https://codex.wordpress.org/Editing_wp-config.php#Alternative_Cron" target="_blank">View WordPress codex information on CRON parameters</a>';
					
					echo "<h5>CRON Test Job END. <br><br><hr>Post content:</h5>";

					
   					return $content;
   			
	}  else 
			{
				return $content;
			}
	
} 
			
	 
	