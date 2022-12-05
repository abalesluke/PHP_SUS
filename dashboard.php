<?php 
error_reporting(0);
include("./core/functions.php");
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
	if($_GET['logout'] == 'true'){
		session_destroy();
		header('location: '.$_SERVER['PHP_SELF']);
	}
}
    
if($isUserLogged == False){
    header("Location: ./");
}

if(isset($_POST['generate'])){
    $url = secure_input($_POST['url']);
    $uid = $_SESSION['user_id'];
    addLink($url, $uid);
    header("Location: ".$_SERVER['PHP_SELF']);
}

if(isset($_GET['rm'])){
    $code = $_GET['rm'];
    $uid = $_SESSION['user_id'];
    if(check_link_id($code, $uid)){
        remove_link($code, $uid);
        header("Location: ".$_SERVER['PHP_SELF']);
    }else{
        header("Location: ".$_SERVER['PHP_SELF']);
    }
}

if(isset($_POST['edit'])){
    $code = $_POST['editCode'];
    $new_url = secure_input($_POST['editnv']);
    $uid = $_SESSION['user_id'];
    update_url($code, $new_url,$uid);
    header("Location: ".$_SERVER['PHP_SELF']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <!-- metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- css -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	
    <!-- scripts -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <title>Dashboard</title>
</head>
<body>
    <div class="mini-nav d-sm-block d-lg-none navbar fixed-bottom">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </div>
    <div class="sidebar collapse d-lg-block" id="sidebar">
        <div class="container-fluid" id="sdbar-items">
            <h2 style="user-select:none;" ><marquee scrollamount="3"><i>&lt;/Urlz&gt;</i></marquee></h2>
            <p>By: <a href="https://www.facebook.com/profile.php?id=100085378914881">Anikin Luke</a></p>
            <hr>
            <div class="sd-items">
                <a class="cstm-btn" href="#">Link</a>
                <a class="cstm-btn" href="#">Profile</a>
                <a class="cstm-btn red" href="?logout=confirm">Logout</a>
            </div>
<!--
        <div class="text-center">
            <button class="rounded-circle material-symbols-outlined cstm-btn" data-bs-toggle="collapse" data-bs-target=".sd-items">arrow_upward</button>
        </div>
-->
        </div>
    </div>
    <article class="d-flex justify-content-center align-items-center">
        <div class="linkG-wrapper align-items-center text-center">
            <h3>Ninja Links</h3>
            <div class="link-gen">
                <form method="post">
                    <input name="url" type="text" onkeyup="cinp();" id="genInp" autocomplete="off" placeholder="Enter url to be shorten">
                    <input name="generate" type="submit" id="genBtn" disabled class="dizstyle btn mx-2 btn-sm btn-outline-success" value="Generate">
                </form>
                <!--<p class="text-muted m-2"><i>Short link url: <span><input style="background-color:rgb(0,0,0,0.1);color:green; text-align:center; padding:5px;border-radius:10px;" class="form-control" value="https://<?php echo $_SERVER['HTTP_HOST'];?>/z/short_link_id" readonly></span></i></p>-->
            </div><br>
            <div class="link-track">
                <table class="table table-bordered mt20">
                    <thead>
                        <th class="text-muted">ID</th>
                        <th class="text-muted">Views</th>
                        <!-- <th class="text-muted">Original Url</th> -->
                        <th class="text-muted">Link</th>
                        <th colspan="3" class="text-muted">Controls</th>
                    </thead>
                    <tbody>
<?php
$conn = new mysqli($servername, $username, $password, $dbname);
$uid = $_SESSION['user_id'];
$sql = "SELECT * FROM `links` WHERE uid=$uid";
$query = $conn->query($sql);

$current_dir = basename(__DIR__);
if($current_dir == 'htdocs'){
    $current_dir = '';
}else{
    $current_dir = '/'.$current_dir;
}

while($row = $query->fetch_assoc()){
    $link_id = $row['link_id'];
    $url = $row['url'];

    $shrt_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]!$_SERVER[REQUEST_URI]";
    $shrt_url = explode('!',$shrt_url);
    array_splice($shrt_url,-1);
    $shrt_url = implode("",$shrt_url);
    $shrt_url = $shrt_url.$current_dir.'/z/'.$link_id;
    #$shrt_url = 'https://'.$_SERVER["HTTP_HOST"].'/z/'.$row["link_id"];
    echo '
                        <tr>
                            <td>'.$row["link_id"].'</td>
                            <td>'.$row["views"].'</td>
                            <td><a href="'.$shrt_url.'">'.$shrt_url.'</td>
                            <td><a class="btn btn-sm btn-outline-primary" onclick="editz(\''.$link_id.'\',\''.$url.'\');">Edit</a></td>
                            <td><a class="btn btn-sm btn-outline-success" target="_blank" href="'.$url.'">View</a></td>
                            <td><a class="btn btn-sm btn-outline-danger" href="?rm='.$link_id.'">Delete</a></td>
                        </tr>';
}
?>
                    </tbody>
                </table>
            </div>
        </div>
    </article>
</body>
<style>

.dizstyle{
    color:rgb(255,255,255,0.6)!important;
    border-color: rgb(0, 0, 0,0.5)!important;
    background-color: rgb(0, 0, 0,0.3)!important;
}

.linkG-wrapper{
    overflow: auto;
    border-radius: 10px;
    padding:10px;
    /*background-color:rgb(0, 0, 0,0.8);*/
    box-shadow: 0px 2px 5px 0px grey;
}
.link-gen input[type=text]{
    padding:3px;
    border:1px solid skyblue;
    border-radius: 5px;
    outline:none;
    background-color:transparent;
}
.link-track{
    overflow: auto;
}

article{
    height: 100vh;
}
body{
    margin:0;
    scroll-behavior: smooth;
    height: 100vh;
}
.mini-nav{
    z-index: 5;
    position: fixed;
}
.mini-nav button{
    z-index: 5;
    text-align: center;
    background: linear-gradient(357deg, rgba(16,83,122,1) 0%, rgba(33,35,37,1) 100%, rgba(42,135,153,1) 100%);
}
.sidebar{
    color:white;
    padding:5px;
    width:200px;
    position: fixed;
    background: linear-gradient(357deg, rgba(16,83,122,1) 0%, rgba(33,35,37,1) 100%, rgba(42,135,153,1) 100%);
    height: 100vh;
    border-right: 1px solid grey;
    box-shadow: 2px 0px 5px 0px grey;
}
.sidebar a:hover{
    cursor: pointer;
}
.cstm-btn {
    border-radius: 10px;
    margin:0;
    padding:10px;   
    color:white;
    display:block;
    text-decoration: none;
    border-bottom:1px solid rgb(255, 255, 255,0.2);
}
.cstm-btn:hover{
    transition:.5s;
    letter-spacing: 1px;
    color:greenyellow;
    background-color:rgb(255, 255, 255,0.1);
    border:1px solid rgb(255, 255, 255,0.2);
}
.red:hover{
    color:red!important;
}

.material-symbols-outlined {
    color:white;
    /*
    margin-top:10px;
    color:white;
    border:1px solid rgb(255,255,255,0.2);
    height: 40px;
    width:40px;
    background-color:transparent; */
    font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 48
}
.cstm-btn:hover{
    transition:.5s;
    background-color:rgb(255, 255, 255,0.3);
}

@media (max-width:700px) {
    .sidebar{
        transition: 1s;
        z-index:3;
        width:100%;
        height: fit-content;
    }
}

</style>
<script>

// Obfuscated s_msg function
//function _0x25f5(_0x42c88a,_0x2a059c){var _0x58956c=_0x5895();return _0x25f5=function(_0x25f5e1,_0x53755e){_0x25f5e1=_0x25f5e1-0xc1;var _0x3e77eb=_0x58956c[_0x25f5e1];return _0x3e77eb;},_0x25f5(_0x42c88a,_0x2a059c);}function _0x5895(){var _0x52cbc8=['1935qVRMlH','cancel','586684PoIrXX','913240aQEuUs','location','5787507UWFjZx','6XqMVCN','?logout=true','45170AUpMIk','DismissReason','then','3rCFLux','3930jpIKoJ','71XLBRKp','href','fire','1989436sKUlzb','2536976PgACng'];_0x5895=function(){return _0x52cbc8;};return _0x5895();}(function(_0x44ebb5,_0x55c27e){var _0x6020b7=_0x25f5,_0x4ae644=_0x44ebb5();while(!![]){try{var _0x132dff=-parseInt(_0x6020b7(0xc4))/0x1*(-parseInt(_0x6020b7(0xc3))/0x2)+-parseInt(_0x6020b7(0xc2))/0x3*(parseInt(_0x6020b7(0xc7))/0x4)+-parseInt(_0x6020b7(0xcc))/0x5*(parseInt(_0x6020b7(0xcf))/0x6)+-parseInt(_0x6020b7(0xcb))/0x7+-parseInt(_0x6020b7(0xc8))/0x8+-parseInt(_0x6020b7(0xc9))/0x9*(-parseInt(_0x6020b7(0xd1))/0xa)+parseInt(_0x6020b7(0xce))/0xb;if(_0x132dff===_0x55c27e)break;else _0x4ae644['push'](_0x4ae644['shift']());}catch(_0x3b9cde){_0x4ae644['push'](_0x4ae644['shift']());}}}(_0x5895,0x87b5a));function s_msg(_0xd139af,_0x4587cf,_0x15c1b1){var _0x4db1f9=_0x25f5;Swal[_0x4db1f9(0xc6)]({'title':_0x4587cf,'icon':_0xd139af,'showCancelButton':!![],'confirmButtonText':_0x15c1b1})[_0x4db1f9(0xc1)](_0x2af712=>{var _0x272e81=_0x4db1f9;if(_0x2af712['isConfirmed'])window[_0x272e81(0xcd)][_0x272e81(0xc5)]=_0x272e81(0xd0);else _0x2af712['dismiss']===Swal[_0x272e81(0xd2)][_0x272e81(0xca)]&&(window['location'][_0x272e81(0xc5)]='./');});}

// Obfuscated isUrlValid function
//(function(_0x49abab,_0x5aa34d){var _0x58dd28=_0x2b2b,_0x4f5524=_0x49abab();while(!![]){try{var _0x2d7e57=-parseInt(_0x58dd28(0x14a))/0x1+parseInt(_0x58dd28(0x149))/0x2+-parseInt(_0x58dd28(0x145))/0x3+-parseInt(_0x58dd28(0x146))/0x4+-parseInt(_0x58dd28(0x141))/0x5+parseInt(_0x58dd28(0x147))/0x6*(-parseInt(_0x58dd28(0x142))/0x7)+parseInt(_0x58dd28(0x143))/0x8*(parseInt(_0x58dd28(0x148))/0x9);if(_0x2d7e57===_0x5aa34d)break;else _0x4f5524['push'](_0x4f5524['shift']());}catch(_0x901e0b){_0x4f5524['push'](_0x4f5524['shift']());}}}(_0x1c13,0x84477));function isUrlValid(_0x3ab0ef){var _0x1e3332=_0x2b2b;return/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i[_0x1e3332(0x144)](_0x3ab0ef);}function _0x2b2b(_0x127101,_0x49b69c){var _0x1c1313=_0x1c13();return _0x2b2b=function(_0x2b2b34,_0x50fb6f){_0x2b2b34=_0x2b2b34-0x141;var _0x4fe93a=_0x1c1313[_0x2b2b34];return _0x4fe93a;},_0x2b2b(_0x127101,_0x49b69c);}function _0x1c13(){var _0x44dc64=['test','2051730ipABvI','253268bPnKIy','12LNNhFj','15489297MYzvne','529120AZixRr','267635PKfFjG','1040750FBqUjK','772681tXAvZi','8zEWkOr'];_0x1c13=function(){return _0x44dc64;};return _0x1c13();}

// Obfuscated cinp (check input) function
//(function(_0x203e31,_0x2d08e1){var _0x2029fe=_0x58d7,_0x8b45f7=_0x203e31();while(!![]){try{var _0x154ba9=parseInt(_0x2029fe(0x1bc))/0x1+-parseInt(_0x2029fe(0x1c2))/0x2*(-parseInt(_0x2029fe(0x1c9))/0x3)+-parseInt(_0x2029fe(0x1c8))/0x4+parseInt(_0x2029fe(0x1ca))/0x5*(parseInt(_0x2029fe(0x1c1))/0x6)+-parseInt(_0x2029fe(0x1c6))/0x7+-parseInt(_0x2029fe(0x1c4))/0x8+-parseInt(_0x2029fe(0x1bb))/0x9*(-parseInt(_0x2029fe(0x1bd))/0xa);if(_0x154ba9===_0x2d08e1)break;else _0x8b45f7['push'](_0x8b45f7['shift']());}catch(_0x998e4){_0x8b45f7['push'](_0x8b45f7['shift']());}}}(_0x485a,0x530f0));function cinp(){var _0x1718d9=_0x58d7,_0x4e5b3a=document[_0x1718d9(0x1b8)](_0x1718d9(0x1cb)),_0x3697c7=document[_0x1718d9(0x1b8)](_0x1718d9(0x1c7));isUrlValid(_0x3697c7[_0x1718d9(0x1b9)])?($(_0x4e5b3a)[_0x1718d9(0x1bf)](_0x1718d9(0x1b7)),_0x4e5b3a[_0x1718d9(0x1be)][_0x1718d9(0x1c3)](_0x1718d9(0x1ba))):($(_0x4e5b3a)[_0x1718d9(0x1c5)](_0x1718d9(0x1b7),''),_0x4e5b3a[_0x1718d9(0x1be)][_0x1718d9(0x1c0)](_0x1718d9(0x1ba)));}function _0x58d7(_0x34090b,_0x2ca4ab){var _0x485a10=_0x485a();return _0x58d7=function(_0x58d74f,_0x1cbf01){_0x58d74f=_0x58d74f-0x1b7;var _0xf8d464=_0x485a10[_0x58d74f];return _0xf8d464;},_0x58d7(_0x34090b,_0x2ca4ab);}function _0x485a(){var _0x28d310=['getElementById','value','dizstyle','9486baBqte','450948ygjYjd','5110ytuQtG','classList','removeAttr','add','647118dxcgMV','2aBpxPt','remove','3600776fnZtkP','attr','4715732UAjKAb','genInp','1614952IskTDg','693177pMckLP','30VyOeIn','genBtn','disabled'];_0x485a=function(){return _0x28d310;};return _0x485a();}


function s_msg(icon,title,btnTxt){
    Swal.fire({
        title: title,
        icon: icon,
        showCancelButton:true,
        confirmButtonText: btnTxt,
    }).then((result) =>{
        if(result.isConfirmed){
            window.location.href="?logout=true";
        }
    });
}

function isUrlValid(url){
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

function cinp(){
    var btn = document.getElementById("genBtn");
    var inp = document.getElementById("genInp");
    if(isUrlValid(inp.value)){
        $(btn).removeAttr("disabled");
        btn.classList.remove("dizstyle");
    }else{
        $(btn).attr("disabled",'');
        btn.classList.add("dizstyle");
    }
}

<?php 
if($_GET['logout'] == 'confirm'){echo "s_msg('warning','Do you really want to logout?','Yes');";}
?>

function editz(link_id,url){
    Swal.fire({
        title: 'Edit Link Value',
        html: `
        <form method="post">
            <input hidden name="editCode" value="`+link_id+`">
            <input class="form-control" type="text" name="editnv" value="`+url+`" autocomplete="off">
            <input class="btn btn-success m-3" type="submit" name="edit" value="Save">
            <input class="btn btn-secondary m-3" type="button" onclick="swal.close();" value="Cancel">
        </form>
        `,
        showConfirmButton: false,
        
        });
}

</script>
</html>
