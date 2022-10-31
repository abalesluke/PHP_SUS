<?php 
error_reporting(0); 
include('./core/functions.php');
session_start();

$isUserLogged = False;
if(isset($_SESSION['logged']) == True){
	$user_agent = $_SESSION['user_agent'];
	$user_ip = $_SESSION['user_ip'];
	$status = session_check($user_agent,$user_ip);
	if(($_SESSION['session_count']-=1) <= 0){
		session_regenerate_id();
		$_SESSION['session_count'] = 5;
	}
	if($status){
		$isUserLogged = True;
	}
	if(isset($_GET['logout']) == true){
		session_destroy();
		header('location: '.$_SERVER['PHP_SELF']);
	}
}
if($isUserLogged){
	header('Location: ./dashboard.php');
}

$_SESSION['incorrect_login'] = False;
if(isset($_POST['login'])){
	$user = secure_input($_POST['username']);
	$pass = secure_input($_POST['password']);
	$status = login($user, $pass);
	if($status){
		header('Location: ./dashboard.php');
	}else{
		$_SESSION['incorrect_login'] = True;
	}
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="uft-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="Urlz">
        <meta property="og:description" content="">
        <meta property="og:type" content="">
        <meta property="og:url" content="">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <title>Ninja</title>
    </head>
    <body>
        <div class="d-flex justify-content-center align-items-center login-wrap" id="login-f">
            <form method="post">
            <h2 style="display:block;" class="text-white text-center">Who are you?</h2><hr>
                <div class="login-inp-box">
                    <input type="text" name="username" placeholder="Username" autocomplete="off" autofocus required>
                </div>
                <div class="login-inp-box">
                    <input type="password" name="password" placeholder="Password" id="password" required><a style="cursor:pointer; user-select:none;" onclick="vis();" id="eye" class="material-symbols-outlined">visibility</a>
                </div>
                <input class="btn btn-primary mt-2" type="submit" name="login" value="Login" style="width:100%; "> 
            </form>
        </div>
    </body>
<style>
body{
    height: 100vh;
    background: linear-gradient(357deg, rgba(16,83,122,1) 0%, rgba(33,35,37,1) 100%, rgba(42,135,153,1) 100%);
    background-attachment: fixed;
}

.login-wrap{
    z-index: 1;
    position: absolute;
    width: 100%;
    height: 100%;
}
form{
    background-color: rgb(255,255,255,0.1);
    box-shadow: 2px 2px 3px 1px rgb(0,0,0,0.4);
    padding:50px;
    border-radius: 10px;
    /*border-bottom: 4px solid rgb(0, 0, 0,0.5);*/
    /*box-shadow: 5px 5px 5px 1px grey;*/
}
.login-inp-box{
    border:1px solid rgb(0, 0, 0,0.8);
    /*background-color:rgb(0,0,0,0.3);*/
}
.login-inp-box input{
    display: inline-flex;
    height: 100%;
    width:80%;
    border:none;
    color:white;
    outline:none;
    background-color: transparent;
    padding:10px;
}

.material-symbols-outlined {
    color:black;
    display: inline-flex;
    margin:0;
    text-align: center;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    width:5vh;
    height: 0vh;
    font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 48
}
.material-symbols-outlined:hover{
    color:darkcyan;
}

</style>
<script>
function _0x41ee(_0x2c9ee3,_0x316f22){var _0x186731=_0x1867();return _0x41ee=function(_0x41ee95,_0x416ab4){_0x41ee95=_0x41ee95-0x180;var _0x3d0c2e=_0x186731[_0x41ee95];return _0x3d0c2e;},_0x41ee(_0x2c9ee3,_0x316f22);}function _0x1867(){var _0xb4cee4=['1318074vXNBUK','919999ErXmeY','getElementById','visibility','type','587521inRMnU','445242cIALby','6444KsOJvS','92brsVcc','2hWgGvf','text','17130rpmoZH','innerHTML','363769LgUHKR','password','16HJSiRE','240LaeYXq','eye','165890EhfIwA'];_0x1867=function(){return _0xb4cee4;};return _0x1867();}(function(_0xc7d739,_0x25e522){var _0x163a87=_0x41ee,_0x28c548=_0xc7d739();while(!![]){try{var _0x4ff8e8=parseInt(_0x163a87(0x184))/0x1+parseInt(_0x163a87(0x18c))/0x2*(parseInt(_0x163a87(0x183))/0x3)+-parseInt(_0x163a87(0x18b))/0x4*(parseInt(_0x163a87(0x182))/0x5)+parseInt(_0x163a87(0x189))/0x6+-parseInt(_0x163a87(0x190))/0x7*(-parseInt(_0x163a87(0x192))/0x8)+parseInt(_0x163a87(0x18a))/0x9*(-parseInt(_0x163a87(0x18e))/0xa)+parseInt(_0x163a87(0x188))/0xb*(parseInt(_0x163a87(0x180))/0xc);if(_0x4ff8e8===_0x25e522)break;else _0x28c548['push'](_0x28c548['shift']());}catch(_0x23f21e){_0x28c548['push'](_0x28c548['shift']());}}}(_0x1867,0x966b4));function vis(){var _0x4b0f54=_0x41ee,_0x254e27=document[_0x4b0f54(0x185)](_0x4b0f54(0x181)),_0x326003=document[_0x4b0f54(0x185)](_0x4b0f54(0x191));_0x326003['type']==_0x4b0f54(0x191)?(_0x254e27['innerHTML']='visibility_off',_0x326003[_0x4b0f54(0x187)]=_0x4b0f54(0x18d)):(_0x254e27[_0x4b0f54(0x18f)]=_0x4b0f54(0x186),_0x326003[_0x4b0f54(0x187)]=_0x4b0f54(0x191));}

<?php if($_SESSION['incorrect_login']){echo "Swal.fire('Incorrect username or password!','','error');";}?>

</script>
</html>
