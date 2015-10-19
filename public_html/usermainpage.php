<?php
include 'dirstat.php';
include 'userinfo.php';
include 'uploader.php';

session_start();

$userpage = new Page($_SESSION['id'],$_SESSION['username'],$_SESSION['email'],$_SESSION['folder']);
$userpage->display();

class Page {
    //empty var
    public $content;

    private  $userinfo;
    public $path;


    function __construct($id, $username, $email, $folder)
    {
        $this->userinfo = new userinfo($id, $username, $email, $folder);
        $_SESSION['user'] = $this->userinfo;
    }


    /**
     * display the user page
     * i vasiki methodos pou ftaxnei dinamika tin selida
     */
    function display()
    {

        if (!empty(@$_SESSION["id"])) {

            session_start();

            if(!empty($_GET["path"])) {
                $this->path = $_GET["path"];
            }

            echo "<html><head>";
            echo "<title>";
            $this->displayTitle();
            echo "</title>";

            $this->addScriptAndStyles();
            echo "</head><body>";

            if($_SESSION['login'] == false) {
                echo "<link href='../resources/jquery/toastr.min.css' rel='stylesheet' /><script> toastr.success('logged in successfully!');</script>";
            }

            if($_SESSION['createfolder'] == true) {
                echo "<link href='../resources/jquery/toastr.min.css' rel='stylesheet' /><script> toastr.success('folder created !');</script>";
            }

            $_SESSION['login'] = true;
            $_SESSION['createfolder'] = false;

            $this->displayBody();

            echo "</body>";
            echo "</html>";

        } else {
            session_destroy();
            ?>
            <script> window.location.href = "index.php"; </script>
            <?php

        }
    }

    /**
     *
     */
function displayTitle()
{
    ?>
    main page
    <?php
}

    /**
     *
     */
    function addScriptAndStyles() {
    ?>
     <link rel="stylesheet" type="text/css" href="css/usermainpage.css"/>
     <link rel="stylesheet" type="text/css" href="css/login.css"/>
     <script src="../resources/jquery/jquery-2.1.4.min.js"></script>
     <script src="../resources/jquery/toastr.min.js"></script>

    <script>

        var showtablestatus = 0;

        function gotomainpage() {
            window.location.href = "index.php";
        }

        function logOut() {
            $.ajax({
                url: 'logout.php?argument=logOut',
                success: function(data) {
                    window.location.href = data;
                }
            })
        }

        function showTable() {
            if(showtablestatus == 0) {
                var table = document.getElementById("table");
                table.style.display = "table";
                showtablestatus = 1;
            } else if(showtablestatus == 1) {
                var table = document.getElementById("table");
                table.style.display = "none";
                showtablestatus = 0;
            }
        }

        function createFolder() {
            $(document).ready(function(){
                var  userfolder =prompt("enter folder name");
                if(userfolder){
                    $.ajax({
                        url: 'createfolder.php?argument=createfolder&foldername='+userfolder,
                        success: function(res){
                            if(res == "success") {
                                window.location.href = "usermainpage.php";
                            } else {
                                toastr.error("invalid folder name, or try again later","Error");
                            }
                        }
                    });} });
        }



    </script>

    <?php
    }

    /**
     *
     */
   function displayBody() {

?>

    <div style="width: 100%">

        <h1 class="form-container3">Box</h1>


    </div>

       <table id="table" style="display: none">
           <tr>
               <td>

                   <form enctype="multipart/form-data" method="POST" class="form-container">
                       <div class="form-title">Browse file</div>
                       <input type="file" name="uploadedfile" />
                       <br>
                       <div class="submit-container">
                           <input class="submit-button" type="submit" value="upload" />
                           <br>
                       </div>
                   </form>

               </td>
           </tr>
       </table>

       <table align="right">
           <tr>
               <td >
                   <table  style="width: 107px; height: 32px">
                       <tr>
                           <td>
                               <div onclick="logOut();"  style="text-align:center; width: 100px; " style="width: 66px">
                                   <a href='#'  style="color: black;font-weight: bold;  background-image: url('img/usericon.png'); background-repeat: no-repeat"  class="button"> <?php echo $this->userinfo->getUsername().":LogOut" ?> </a>
                               </div>
                           </td>
                       </tr>
                   </table>
               </td>
           </tr>
       </table>

<table style="border: inherit; margin: 10px">
    <tr>
        <td>
            <table style="width: 107px; height: 32px">
                <tr>
                    <td>
                        <div onclick="showTable();" style="text-align:center; width: 150px; margin: 10px;">
                            <a href='#'  class="button"> upload file </a>
                        </div>
                    </td>
                    <td>
                        <div onclick="createFolder();" style="text-align:center; width: 150px;" >
                            <a href='#'  class="button"> create folder </a>
                        </div>
                    </td>
                </tr>
            </table>
        </td>

    </tr>
</table>


<br>

    <table style="width: 99%; height: 350px">
    <tr>
        <td style="width: 35px">
        </td>
        <td style="width: 200px">

            <?php if(!empty($this->path)) {
                dirstat($this->path);

            } else {
                dirstat($this->userinfo->getUserfolder());
            }
            ?>
        </td>
        <td style="width: 21px">&nbsp;</td>
        <td style="width: 100px">
            <?php if(!empty($this->path)) {
                dirstat($this->path);
                $this->path = null;
            } else {
                dirstat2($this->userinfo->getUserfolder());
            }
            ?>
        </td>
        <td style="width: 23px">
        </td>
    </tr>
    </table>


    <?php
    }

}
?>


