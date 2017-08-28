<?php
define("db_hostServer", "localhost"); 
define("db_userName", "root"); 
define("db_password", "admin.123"); 
define("db_database", "mejiaTest"); 

class Conn{

	public $conn = null;

	public function __construct(){

		if($this->conn!=null){
			return;
		}

		$servername = db_hostServer;
		$username = db_userName;
		$password = db_password;
		$dbname = db_database;

		$this->conn = new mysqli($servername, $username, $password,$dbname);
		$this->conn->set_charset("utf8");

		if($this->conn->connect_error){
			return false;
		}else{
			return true;
		}
	}
}


Class Database extends Conn {


	function getProfileDetail($username){
		$returnArray = array();
		$ret = array();
		$sql = sprintf("SELECT * FROM userprofile WHERE `username`='%s' ",$username);
		$result = $this->conn->query($sql);
		if($result->num_rows > 0){
			if($row = $result->fetch_assoc()) {
				$returnArray['lastName']	 = $row['LastName'];
				$returnArray['firstName']	 = $row['FirstName'];
				$returnArray['address']		 = $row['Address'];
				$returnArray['phone']		 = $row['Phone'];
				$returnArray['email']		 = $row['Email'];
				$ret = $returnArray;
			}
			return $ret;
		}
	}

    function updateProfile($userLastName, $userFirstName, $userAddress, $userPhone, $userEmail, $username){
        $sql = sprintf("UPDATE userprofile SET `FirstName`= '%s', `LastName`= '%s', `Address`= '%s',`Phone`= '%s',`Email`= '%s' WHERE `username`='%s' ",$userFirstName,$userLastName,$userAddress,$userPhone, $userEmail,$username);       
        $result = $this->conn->query($sql);
    }

    function setLikedVideo($videoNum,$country, $liked, $username){
    	$sql = "INSERT INTO liketable (username,videonumber,Liked,Country) VALUES ('" .$username. "' , '" .mysqli_real_escape_string($this->conn,$videoNum). "', '" . $liked. "','" .$country. "')";
        $result = $this->conn->query($sql);
    }

     function setLikedVideoCountry($videoNum,$country, $liked, $username){
     	if($liked == "liked"){
     		$columm = "Liked";
     		$likeValue = 1;
     		$dislikeValue = 0;
     	}else{
     		$columm = "Disliked";
     		$likeValue = 0;
     		$dislikeValue = 1;
     	}
     	$sql = sprintf("SELECT * FROM social WHERE `videonumber`='%s' AND `Country`='%s'",$videoNum,$country);
     	$result = $this->conn->query($sql);
     	if($result->num_rows > 0){
     		if($row = $result->fetch_assoc()) {
     		$row[''.$columm.''] = $row[''.$columm.''] + 1;
     		// echo "after add:".$row[''.$columm.''];
     		$sql2 = sprintf("UPDATE social SET `$columm` = %s WHERE `videonumber`='%s' AND`Country`='%s' ",$row[''.$columm.''],$videoNum,$country);
    		$result2 = $this->conn->query($sql2);
	    	}
     	}else{
     		$sql3 = "INSERT INTO social (videonumber,Liked,Disliked,Country) VALUES ('" .$videoNum. "' , " .$likeValue.", " .$dislikeValue.",'" .$country. "')";
	        $result3 = $this->conn->query($sql3);
     	}
    }


    function getLikedVideo($username){
    	$returnArray = array();
    	$returnArray2 = array();
		$ret = array();
		$sql = sprintf("SELECT * FROM liketable WHERE `username`='%s' ",$username);
    	$result = $this->conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
				$returnArray[]	 = $row['videonumber'];
				$returnArray2['vote']		 = $row['Liked'];
				$returnArray2['country']		 = $row['Country'];
				$returnArray3[] = $returnArray2;
				$ret['videonumber'] = $returnArray;
				$ret['struct'] = $returnArray3;
			}
			return $ret;

		}
	}

	function updateVote($videoNum, $liked, $username){
		$sql = sprintf("UPDATE liketable SET `Liked` = '%s' WHERE `videonumber`='%s' AND`username`='%s' ",$liked,$videoNum,$username);
    	$result = $this->conn->query($sql);

	}

	function updateVoteWorldwide($videoNum, $liked, $country){
		if($liked == "liked"){
     		$columm  = "Liked";
     		$columm2 = "Disliked";
     	}else{
     		$columm  = "Disliked";
     		$columm2 = "Liked";
     	}
     	echo "current country:".$country;
     	$sql = sprintf("SELECT * FROM social WHERE `videonumber`='%s' AND `Country`='%s'",$videoNum,$country);
     	$result = $this->conn->query($sql);
     	if($result->num_rows > 0){
     		if($row = $result->fetch_assoc()) {
	     		$row[''.$columm.'']  = $row[''.$columm.''] + 1;
	     		$row[''.$columm2.''] = $row[''.$columm2.''] - 1;
	     		$sql2 = sprintf("UPDATE social SET `$columm` = %s, `$columm2` = %s WHERE `videonumber`='%s' AND`Country`='%s' ",$row[''.$columm.''], $row[''.$columm2.''], $videoNum,$country);
	    		$result2 = $this->conn->query($sql2);
	    	}
	    }
	}

	function getAllUsers(){
		$returnArray = array();
		$returnArray2 = array();
		$ret = array();
		$sql = "SELECT * FROM loginprofile";
		$result = $this->conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
				$returnArray2[]	 = $row['Username'];
				$returnArray[]	 = $row['Password'];
				$ret['username'] = $returnArray2;
				$ret['password'] = $returnArray;
			}
			return $ret;
		}
	}

	function getWorldWideVotes($videoNum){
		$ret = array();
		$returnArray = array();
		$sql = sprintf("SELECT * FROM social WHERE `videonumber`='%s' ",$videoNum);
		$result = $this->conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
				$returnArray['country']		 = $row['Country'];
				$returnArray['liked']		 = $row['Liked'];
				$returnArray['disliked']	 = $row['Disliked'];
				$ret[] 						 = $returnArray;
			}
			return $ret;
		}
	}

	function getAllVideo(){
		$ret = array();
		$returnArray = array();
		$sql = "SELECT DISTINCT videonumber FROM social";
		$result = $this->conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
				$returnArray[]	= $row['videonumber'];
				$ret = $returnArray;
			}
		return $ret;
		}
	}



}

	// $jsonData = "";
	$requestAction = $_GET['q'];
	$username = $_GET['username'];
	// $username = "Petekern";
switch($requestAction)
{
	case 'profile':
		$db = new Database();
		$dba = $db->getProfileDetail($username);
		$dataArray = array();
		$dataArray["result"] = "success";
		$dataArray['lastName']	 = $dba['lastName'];
		$dataArray['firstName']	 = $dba['firstName'];
		$dataArray['address']	 = $dba['address'];
		$dataArray['phone']		 = $dba['phone'];
		$dataArray['email']		 = $dba['email'];
		$jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);
	break;

	case 'update' :

		if (isset($_POST['userLastName']) || isset($_POST['userFirstName'])
			|| isset($_POST['userAddress'])|| isset($_POST['userPhone'])|| isset($_POST['userEmail'])){
	        $userLastName    	 = $_POST['userLastName'];
	        $userFirstName  	 = $_POST['userFirstName'];
	        $userAddress    	 = $_POST['userAddress'];
	        $userPhone     		 = $_POST['userPhone'];
	        $userEmail    		 = $_POST['userEmail'];
	       	$db = new Database();
			$db->updateProfile($userLastName, $userFirstName, $userAddress, $userPhone, $userEmail, $username);
	    }

	break;

	case 'likefeature' :
		if (isset($_POST['videoId']) || isset($_POST['country'])|| isset($_POST['liked'])){
	        $videoNum    = $_POST['videoId'];
	        if(!empty($_POST['country'])){
		        $country = $_POST['country'];
		    }
		    else
		    {	
		    	$country = "unknown";
		    }
	        $liked    	 = $_POST['liked'];
	       	$db 		 = new Database();
			$db->setLikedVideo($videoNum,$country, $liked, $username); 
			$db->setLikedVideoCountry($videoNum,$country, $liked, $username) ;
	    }
	
	case 'getlikefeature' :
		$db = new Database();
		$dba = $db->getLikedVideo($username);
		$dataArray = array();

		$orderArray = array();
		$count =  count($dba['videonumber']);
		for($i=0; $i < $count ; $i++){
			$number = $dba['videonumber'][$i];
			$orderArray[''.$number.''] = $dba['struct'][$i];
		};
		$jsonData = json_encode($orderArray, JSON_PRETTY_PRINT);
		
	break;

	case 'updatevote' :
		$db = new Database();
		if (isset($_POST['videoId']) || isset($_POST['country'])|| isset($_POST['liked'])){
	        $videoNum    = $_POST['videoId'];
	        $liked    	 = $_POST['liked'];
	        if(!empty($_POST['country'])){
		        $country = $_POST['country'];
		    }
		    else
		    {	
		    	$country = "unknown";
		    }

	       	$db 		 = new Database();
	       	echo $country;
	       	echo $videoNum; 
	       	echo $liked; 
	       	echo $username;
			$db->updateVote($videoNum, $liked, $username);  
			$db->updateVoteWorldwide($videoNum, $liked,$country);
	    }
	break;

	case 'loginprofiles' :
		$db = new Database();
		$dba = $db->getAllUsers();
		$orderArray = array();
		$count =  count($dba['username']);
		for($i=0; $i < $count ; $i++){
			$user = $dba['username'][$i];
			$orderArray[''.$user.''] = $dba['password'][$i];
		};
		$jsonData = json_encode($orderArray, JSON_PRETTY_PRINT);
	break;

	case 'displayWorlwideVote' :
		$db = new Database();
		
		$allVideo = $db->getAllVideo();
		// $result  = array();
		$orderArray = array();
		$count =  count($allVideo);
		// print_r($allVideo);
		for($i=0; $i < $count ; $i++){
			// echo $allVideo[0];
			$result = $db->getWorldWideVotes($allVideo[$i]);
			$orderArray [ ''.$allVideo[$i].''] = $result;
		};

		$jsonData = json_encode($orderArray, JSON_PRETTY_PRINT);
	
	break;


}
 
  	if(!empty($jsonData)){
		header('Content-Type: application/json');
		echo($jsonData);
	}
?>