<?php
 	$servername = "mysql.dur.ac.uk";
        $username = "";
        $password = "";
        $dbname = "Pxtbk56_graph2";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT  `contributions` . * 
FROM  `contributions`";     
	$result = $conn->query($sql);
        $week1 = 0;
	$week2 = 0;
	$week3 = 0;
	$week4 = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
		$temp1 = $row["date"];
		if((time()-(60*60*24*7)) < strtotime($temp1)){
                	$week1 += 1;
		}
		if(  ((time()-(60*60*24*14)) < strtotime($temp1)) and ((time()-(60*60*24*7)) > strtotime($temp1))    ){
                	$week2 += 1;
		}
		if(((time()-(60*60*24*21)) < strtotime($temp1))and ((time()-(60*60*24*14)) > strtotime($temp1)) ){
                	$week3 += 1;
		}
		if(((time()-(60*60*24*28)) < strtotime($temp1)) and ((time()-(60*60*24*14)) > strtotime($temp1)) ){
                	$week4 += 1;
		}
		
		}

        }
        else {
            echo "The query was submitted but there are 0 results for that question.Thank you!";
        }
	$topicArray = array();
	$array1 = array("week"=>"1","score"=>"$week1");
	$array2 = array("week"=>"2","score"=>"$week2");
	$array3 = array("week"=>"3","score"=>"$week3");
	$array4 = array("week"=>"4","score"=>"$week4");
	array_push($topicArray,$array1);
	array_push($topicArray,$array2);
	array_push($topicArray,$array3);
	array_push($topicArray,$array4);
	echo json_encode($topicArray);
        $conn->close();

?>
