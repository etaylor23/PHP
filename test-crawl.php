<?php








class URLProcessor
{
    function processURL($urlArg) {
        echo "--------------------------- \nBeginning Crawl Of: ".$urlArg."\n";
        $pageContents = file_get_contents($urlArg);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($pageContents);
        $body = $doc->getElementsByTagName("body")->item(0);
        $title = $doc->getElementsByTagName("title")->item(0)->textContent;

        $doc = $doc->saveHTML($body);
        $doc = mysql_escape_string($doc);

		$domain   = "127.0.0.1:8889";  // or yourdomainname.com
		$username = "root";       // db username
		$password = "root";    // db password
		$dbName   = "wordpress";   // db name


		$conn = mysqli_connect($domain,$username,$password,$dbName);
		if (mysqli_connect_errno()){
		    echo "Failed to connect to MySQL: " . mysqli_connect_error();
		    die('Mysql connection error');
		}else{
		    echo "Connection Established\n";

			$sql = "INSERT INTO `wp_posts`(`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`,`post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES ('',1,now(),now(),'".$doc."','".$title."','Test excerpt','draft','open','open','','Test post name','Test to ping','','','','','','".$urlArg."','0','page','','')";

			if ($conn->query($sql) === TRUE) {
			    echo "Inserted record\n";
			} else {
			    echo "Error creating table: " . $conn->error;
			}
		}
        echo "Finished Crawl Of: ".$urlArg."\n---------------------------\n";
    }
}










$process = new URLProcessor();


echo "Starting a new migration\n";

$URL = "http://www.ellistaylor.co.uk/sitemap.xml";

$URLContents = file_get_contents($URL);

$xml = new SimpleXMLElement($URLContents);


foreach ($xml->url as $url_list) {
    $url = $url_list->loc;
    $process->processURL($url);
}

?>
