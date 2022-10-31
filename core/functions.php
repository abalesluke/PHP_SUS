<?php 

include('config.php');

// session checker to prevent session hijacking.
//$_SESSION['session_count'] = 3;
function session_check($user_agent, $ip){
	if($user_agent != $_SERVER['HTTP_USER_AGENT']){
		return False;
	}
	else if($ip != $_SERVER['REMOTE_ADDR']){
		return False;
	}
	else{
		return True;
	}
}


//filter input
function secure_input($data){
	$data = trim($data);
	$data = htmlspecialchars($data);
	$data = stripslashes($data);
	return $data;
}


//random uid generator
function generate_uid($length = 9) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


//username checker
function check_user($username){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `users` WHERE username=:username;");
	$sql->bindParam(":username", $username);
	$sql->execute();
	$count = $sql->rowCount();
	if($count > 0){
		return True;
	}
	else{
		return False;
	}
}

function encrypt($data){
	return md5("ninja_linkz:$data");

}

//get_uid
function get_uid($user, $pass){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `users` WHERE username=:username AND password=:password");
	$sql->bindParam(":username",$user);
	$sql->bindParam(":password",$pass);
	$sql->execute();
	$results= $sql->fetchAll(PDO::FETCH_ASSOC);
	foreach ($results as $val){
		$data = $val['uid'];
	}
	return $data;
}


//get user data
function gud($column,$uid){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `users` WHERE uid=:uid");
	$sql->bindParam(":uid",$uid);
	$sql->execute();
	$results= $sql->fetchAll(PDO::FETCH_ASSOC);
	foreach ($results as $val){
		$data = $val[$column];
	}
	return $data;

}

// get link data (GLD)
function gld($column,$link_id){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `links` WHERE link_id=:link_id");
	$sql->bindParam(":link_id",$link_id);
	$sql->execute();
	$results = $sql->fetchAll(PDO::FETCH_ASSOC);
	foreach($results as $val){
		$data = $val[$column];
		break;
	}
	return $data;

}


//update url function
function update_url($link_id, $new_url, $uid){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("UPDATE `links` SET url=:new_url WHERE uid=:uid AND link_id=:link_id");
	$sql->bindParam(":new_url",$new_url);
	$sql->bindParam(":uid",$uid);
	$sql->bindParam(":link_id",$link_id);
	$sql->execute();
}

//login
function login($user,$pass){
	$pass = encrypt($pass);
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `users` WHERE username=:username AND password=:password");
	$sql->bindParam(":username",$user);
	$sql->bindParam(":password",$pass);
	$sql->execute();
	$count = $sql->rowCount();
	if($count > 0){
		$_SESSION['logged'] = True;
		$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['username'] = $user;
		$_SESSION['user_id'] = get_uid($user, $pass);
		$_SESSION['session_count'] = 5;
		return True;
	}else{
		return False;
	}
}


//create
function create($fullname, $email, $user,$pass){
	$pass = encrypt($pass);
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("INSERT INTO `users`(uid,name,email,username,password) VALUES(:uid, :fullname, :email, :username, :password)");
	$sql->bindParam(":uid",generate_uid());
	$sql->bindParam(":fullname",$fullname);
	$sql->bindParam(":email",$email);
	$sql->bindParam(":username",$user);
	$sql->bindParam(":password",$pass);
	$sql->execute();
	$count = $sql->rowCount();
	if($count > 0){
		return True;
	}else{
		return False;
	}
}

//update link views
function ulv($link_id){
	$new_view = gld('views',$link_id);
	$new_view = $new_view+=1;
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("UPDATE `links` set views=:new_view where link_id=:link_id");
	$sql->bindParam(":new_view",$new_view);
	$sql->bindParam(":link_id",$link_id);
	$sql->execute();
}

function generate_linkID($length = 4) {
    $characters = '104369MQFSC8XVTGH4N59WAYL';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


//add/generate link to db
function addLink($url, $uid){
	$link_id = generate_linkID();
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("INSERT INTO `links`(link_id,url,uid) VALUES(:link_id,:url,:uid)");
	$sql->bindParam(":link_id",$link_id);
	$sql->bindParam(":url",$url);
	$sql->bindParam(":uid",$uid);
	$sql->execute();
}

//links checker
function check_link_id($link_id, $uid){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("SELECT * FROM `links` WHERE uid=:uid AND link_id=:link_id;");
	$sql->bindParam(":uid", $uid);
	$sql->bindParam(":link_id", $link_id);
	$sql->execute();
	$count = $sql->rowCount();
	if($count > 0){
		return True;
	}
	else{
		return False;
	}
}

//link deleterz using id and uid
function remove_link($link_id, $uid){
	$conn = $GLOBALS['conn'];
	$sql = $conn->prepare("DELETE FROM `links` WHERE uid=:uid AND link_id=:link_id;");
	$sql->bindParam(":uid", $uid);
	$sql->bindParam(":link_id", $link_id);
	$sql->execute();
}

//function view verifier
function isViewed($ip){
	if($ip == $_SERVER['REMOTE_ADDR']){
		return True;
	}else{return False;}
}

?>