<?php
require ('dbconnect.php'); // ToDo! DB Connect
require ('curl-functions.php'); // ToDo! Get CURL functions
require ('seo-techniques-functions.php'); // ToDo! Get SEO techniques' functions

//create_table(); // ToDo! Create MySQL Table
//$retrieve = retrieve_data_from_google(); // ToDo! Retrieve data from google
clear_dataset(); // ToDO! Remove logistics website missing
$websites = create_dataset_array(); // ToDo! Create an array with our dataset
foreach($websites AS $id => $website) { // ToDo! Run through our dataset
    $curl_results = do_curl_website($website); // ToDo! Do curl on current website
    $dom =  $curl_results[0]; // ToDo! Get dom
    $info = $curl_results[1]; // ToDo! get curl header info
    $seo_checks = do_seo_checks($curl_results); // ToDo! Do SEO checks using the getElementsByTagName
    $add_results_to_db = add_results_to_db($seo_checks,$id); // ToDo! Add SEO results to DB
}