<?php
/*
 * ToDO! All SEO techniques in an ARRAY
 */
$seo_techniques_functions = array(
    'images_alt',
    'links_title',
    'rss',
    'sitemap',
    'robots',
    'heading1',
    'heading2',
    'web_ssl',
    'meta_description',
    'opengraph',
    'url_seo_friendly',
    'amp',
    'minified_css',
    'minified_js',
    'title',
    'structured_data',
    'speed_test',
    'responsive_test',
    'check_if_wordpress',
    'check_if_use_yoast'
);

/*
 * ToDO! Create table function using $seo_techniques_functions ARRAY
 */
function create_table(){
    global $conn,$seo_techniques_functions;

    $add_to_create = '';
    foreach($seo_techniques_functions as $technique){
        if($technique=='speed_test'){
            $add_to_create.= $technique.' DECIMAL(15,4) NOT NULL, ';
        }else {
            $add_to_create .= $technique . ' INT(1) NOT NULL, ';
        }
    }

    $sql = "CREATE TABLE SEOTechniques (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
status INT(6) NOT NULL,
url TEXT NOT NULL,
legal_name TEXT NOT NULL,
da INT(3) NOT NULL,
keywords BIGINT NOT NULL,
backlinks BIGINT NOT NULL,
reviews BIGINT NOT NULL,
rating DECIMAL(15,1) NOT NULL,
traffic_1 BIGINT NOT NULL,
traffic_2 BIGINT NOT NULL,
traffic_3 BIGINT NOT NULL,
traffic_4 BIGINT NOT NULL,
traffic_5 BIGINT NOT NULL,
traffic_6 BIGINT NOT NULL,
traffic_7 BIGINT NOT NULL,
traffic_8 BIGINT NOT NULL,
traffic_9 BIGINT NOT NULL,
traffic_10 BIGINT NOT NULL,
traffic_11 BIGINT NOT NULL,
traffic_12 BIGINT NOT NULL,
{$add_to_create}
)";
    return $sql;
    if ($conn->query($sql) === TRUE) {
        return 1;
    } else {
        return $conn->error;
    }
}

/*
 * ToDO! Clear dataset from missing websites
 */
function clear_dataset(){
    global $conn;
    $sql = "SELECT id FROM SEOTechniques WHERE url=''";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $sql = "DELETE FROM SEOTechniques WHERE id='$id'"; // i can do that without select WHERE url=''
            if ($conn->query($sql) === TRUE) { } else { return $conn->error; }
        }
    } else {
        return 0;
    }
    return 1;
}

/*
 * ToDO! Print dataset
 */
function print_dataset(){
    global $conn;
    $sql = "SELECT id,url FROM SEOTechniques";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $pr = '<table>';
        while($row = $result->fetch_assoc()) {
            $parse = parse_url($row["url"]);
            $parse1 = str_replace("www.","",$parse['host']);
            $pr .= '<tr><td>'.$row["id"].'</td><td>'.$parse1.'</td></tr>';
        }
        $pr .= '</table>';
    }
    return $pr;
}

function count_average_traffic(){
    global $conn;
    $sql = "SELECT * FROM SEOTechniques";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $web_traffic = 0;
            for ($x = 0; $x <= 12; $x++) {
                $web_traffic += $row["traffic_".$x];
            }
            $web_traffic = $web_traffic/12; // average web traffic
            $sql = "UPDATE SEOtechniques SET web_traffic='$web_traffic' WHERE id='$id'";
            if ($conn->query($sql) === TRUE) {}
        }
    }
}


/*
 * ToDO! Create ARRAY from DB
 */
function create_dataset_array(){
    global $conn;
    $websites = array();
    $sql = "SELECT id,url FROM SEOTechniques WHERE status=0";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $websites[$row["id"]] = $row["url"];
        }
    }
    return $websites;
}

/*
 * ToDO! SEO Techniques fuctions - Begin
 */
function images_alt()
{ // Valid Structure: <img src=".." alt="..." />
    global $dom; // i use global for simplicity
    $all_images = array();
    $missing_alts = array();
    $objects = $dom->getElementsByTagName('img'); // get all img elements
    foreach ($objects as $object) { // run through $objects
        $all_images[$object->getAttribute('src')] = $object->getAttribute('alt');
        if(empty($object->getAttribute('alt'))){ // if missing alts
            array_push($missing_alts,$object->getAttribute('src'));
        }
    }
    if(empty($missing_alts)){
        return 1;
    }else{ return 0; }
}

function title()
{ // Valid Structure: <title>...</title>
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('img'); // get title element
    if ($objects->length > 0) { //if title tag exists
        return 1;
    }else{
        return 0;
    }
}

function structured_data()
{
    global $dom; // i use global for simplicity

    // check for microdata
    $objects = $dom->getElementsByTagName('div'); // get script element
    foreach($objects as $object) {
        $alt1 = $object->getAttribute('itemtype'); //get type
        if ((strpos($alt1, 'schema.org') !== false)) {
            return 1;
        }
    }
    // check for json_ld
    $objects = $dom->getElementsByTagName('script'); // get script element
    foreach($objects as $object) {
        $alt1 = $object->getAttribute('type'); //get type
        if($alt1==='application/ld+json'){
            return 1;
        }
    }
    // check for rdfa
    $objects = $dom->getElementsByTagName('div'); // get script element
    foreach($objects as $object) {
        $alt1 = $object->getAttribute('vocab'); //get type
        if($alt1==='http://schema.org/'){
            return 1;
        }
    }

    return 0; // return 0 if there are no structured data
}

function links_title()
{ // Valid Structure: <a href="..." title="...">...</a>
    global $dom; // i use global for simplicity
    $all_hrefs = array();
    $missing_title = array();
    $objects = $dom->getElementsByTagName('a'); // get all a elements
    foreach ($objects as $object) { // run through $objects
        $all_hrefs[$object->getAttribute('href')] = $object->getAttribute('title');
        if(empty($object->getAttribute('title'))){ // if missing titles
            array_push($missing_title,$object->getAttribute('href'));
        }
    }
    if(empty($missing_title)){
        return 1;
    }else{ return 0; }
}

function rss()
{ // Valid Structure: <link rel="alternate" type="application/rss+xml" href="...">
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('link'); // get all link elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('rel')==='alternate' && $object->getAttribute('type')==='application/rss+xml'){
            return 1; //$object->getAttribute('href') return rss href
        }
    }
    return 0; // missing rss
}

function sitemap()
{ // Valid Structure: <link rel="sitemap" type="application/xml" href="sitemap.xml">
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('link'); // get all link elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('rel')==='sitemap' && $object->getAttribute('type')==='application/xml'){
            return 1; //$object->getAttribute('href') return sitemap href
        }
    }
    return 0; // missing sitemap
}

function robots()
{ // robots.txt file should be in the public_html folder
    global $website; // i use global for simplicity
    $ch = curl_init($website.'/robots.txt');
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($responseCode == 200){ return 1; } // if file exists
    else{ return 0; } // if file missing
}

function heading1()
{
    global $dom; // i use global for simplicity
    $all_h1 = array();
    $objects = $dom->getElementsByTagName('h1'); // get all h1 elements
    foreach ($objects as $object) { // run through $objects
        array_push($all_h1,$object->nodeValue);
    }
    if(empty($all_h1)){
        return 0;
    }else{
        return 1;
    }
}

function heading2()
{
    global $dom; // i use global for simplicity
    $all_h2 = array();
    $objects = $dom->getElementsByTagName('h2'); // get all h1 elements
    foreach ($objects as $object) { // run through $objects
        array_push($all_h2,$object->nodeValue);
    }
    if(empty($all_h2)){
        return 0;
    }else{
        return 1;
    }
}

function web_ssl()
{
    global $website; // i use global for simplicity

    //TODO REMOVE THIS BEFORE UPLOAD
    if (strpos($website, 'https') !== false) {
        return 1;
    }else{
        return 0;
    }


    // check if given url include https
    if (strpos($website, 'https') !== false) {
        $given_url = $website; // leave it as it is
    }else{ // given url not including https
        // check if there is http
        if (strpos($website, 'http') !== false) {
            $given_url = str_replace("http", "https", $website); // replace http with https
        }else{ // add https in front of the given url
            $given_url = 'https://'.$website;
        }
    }
    // curl the given url checking if response code is 200
    $ch = curl_init($given_url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($responseCode == 200){ return 1; } // if file exists
    else{ return 0; } // if file missing
}

function meta_description()
{ // Valid Structure: <meta name="description" content="...">
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('meta'); // get all meta elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('name')==='description') {
            return 1; // $object->getAttribute('content');
        }
    }
    return 0;
}

function opengraph()
{ // Valid Structure: <meta property="og:type" content="website" />
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('meta'); // get all meta elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('property')==='og:type') {
            return 1; //$object->getAttribute('content');
        }
    }
    return 0;
}

function url_seo_friendly()
{ // Valid chars: a-z 0-9 - / :
    global $website; // i use global for simplicity
    if (preg_match("%([A-Za-z0-9-:/])$%i", $website)) {
        return 1;
    }else{
        return 0;
    }
}

function amp()
{ // Valid Structure: <link rel="amphtml" href="...">
    global $dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('link'); // get all meta elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('rel')==='amphtml') {
            return 1; //$object->getAttribute('href');
        }
    }
    return 0;
}

function is_minified($website,$file)
{ // If the CSS file has more lines than 1 we assume that is not minified

    if ((strpos($file, '.min.') !== false)) {
        return 1;
    }else{
        return 0;
    }

    //TODO REMOVE THIS BEFORE UPLOAD
    // The function bellow fails
    if (strpos($website, $file) !== true) { // if not absolute url
        $absolute_url = $website.'/'.$file;
    }else{ // if absolute url
        $absolute_url = $file;
    }
    $linecount = 0;
    $handle = fopen($absolute_url, "r"); // Open the CSS file
    while(!feof($handle)){ // Count the number of lines
        $line = fgets($handle);
        $linecount++;
        // If the CSS file has more lines than 1 we assume that is not minified
        if ( $linecount > 1 ) {
            return 0;
        }
    }
    fclose($handle); // Close the CSS file
    return 1;
}

function minified_css()
{
    global $website,$dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('link'); // get all link elements
    foreach ($objects as $object) { // run through $objects
        if($object->getAttribute('rel')==='stylesheet'){ // check only the stylesheets
            $file = $object->getAttribute('href');
            // check if this file is minified
            if(is_minified($website,$file)==0){ return 0; }
        }
    }
    return 1;
}

function minified_js()
{
    global $website,$dom; // i use global for simplicity
    $objects = $dom->getElementsByTagName('script'); // get all script elements
    foreach ($objects as $object) { // run through $objects
        $file = $object->getAttribute('src');
        // check if this file is minified
        if(is_minified($website,$file)==0){ return 0; }
    }
    return 1;
}

function check_if_wordpress(){
    global $conn,$id,$dom,$website; // i use global for simplicity
    $dom = file_get_contents($website);
    $pattern = "/wp-content/";
    if(preg_match($pattern, $dom)==TRUE){ // is wordpress
        return 1;
    }else{
        return 0;
    }
}

function check_if_use_yoast(){
    global $conn,$id,$dom,$website; // i use global for simplicity
    $dom = file_get_contents($website);
    $pattern = "/Yoast SEO plugin/"; // if installed there is a comment in the source code <!-- This site is optimized with the Yoast SEO plugin v17.7.1 - https://yoast.com/wordpress/plugins/seo/ -->
    if(preg_match($pattern, $dom)==TRUE){ // has yoast installed
        return 1;
    }else{
        return 0;
    }
}

function speedCheck($website,$speed_apikey) ## Get API key from https://developers.google.com/speed/docs/insights/v5/get-started ##
{
    $ch = curl_init("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$website&key=$speed_apikey");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    $obj = json_decode($res, true);
    return $obj['lighthouseResult']['audits']['speed-index']['displayValue'];
}
function speed_test()
{
    global $website;
    $speed_apikey = 'AIzaSyAVDI6VTjyzpG1KrGrCMuM15Dw7BH1g3zE';
    $speed =  speedCheck($website,$speed_apikey);
    $page_load_time_var = preg_replace("/[^0-9\.]/", '', $speed); //remove unwanted chars and spaces - get only number
    if(is_numeric($page_load_time_var)){
        return $page_load_time_var;
    }else{
        return 0;
    }
}

function responsiveCheck($website,$responsive_apikey)  ## Get API key from https://developers.google.com/speed/docs/insights/v5/get-started ##
{
//i enable my key for use on https://console.developers.google.com/apis/api/searchconsole.googleapis.com/overview?project=203947678735

    $data = array(
        'url' => $website,
        'requestScreenshot' => 'false'
    );

    $payload = json_encode($data);

// Prepare new cURL resource
    $ch = curl_init('https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?key='.$responsive_apikey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Set HTTP Header for POST request
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
    );

// Submit the POST request
    $result = curl_exec($ch);
    $obj = json_decode($result, true);
    return $obj['mobileFriendliness']; //MOBILE_FRIENDLY


// Close cURL session handle
    curl_close($ch);
}
function responsive_test()
{
    global $website;
    $responsive_apikey = 'AIzaSyABgSO4Z0p-zEjnzHIRRWjS5fEBNWal7-s';
    $responsive_check = responsiveCheck($website,$responsive_apikey);
    if($responsive_check==='MOBILE_FRIENDLY'){
        return 1;
    }else{
        return 0;
    }
}
/*
 * ToDO! SEO Techniques fuctions - End
 */

/*
 * ToDO! Main function handling all the above
 */
function do_seo_checks($curl_results)
{
    global $seo_techniques_functions,$website; // i use global for simplicity
    $dom =  $curl_results[0];
    $info = $curl_results[1];
    $results = array();
    foreach ($seo_techniques_functions as $technique){
        if(function_exists($technique)) {
            $this_technique = $technique(); // run this specific SEO technique
            if($this_technique!==0){ // if this technique succeed // some functions return 1or0 and some other return 0or'text'
                $results[$technique] = $this_technique; // if this technique succeed store it to $results
            } // else leave it 0 on db
        }
    }
    return $results; // if SEO checks completed return the results
}