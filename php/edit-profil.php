<?php
    session_start();
    include_once "config.php";
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
    if(mysqli_num_rows($sql) > 0){
      $row = mysqli_fetch_assoc($sql);
    }
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0 and $row['email'] !== $email){
                echo "$email - This email already exist!";
            }
            else{
                if(isset($_FILES['image'])){
                    $img_name = $_FILES['image']['name'];
                    $img_type = $_FILES['image']['type'];
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $img_explode = explode('.',$img_name);
                    $img_ext = end($img_explode);
    
                    $extensions = ["jpeg", "png", "jpg"];
                    if(in_array($img_ext, $extensions) === true){
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time();
                            $new_img_name = $time.$img_name;
                           if(move_uploaded_file($tmp_name,"images/".$new_img_name)){
                                $encrypt_pass = md5($password);
                                $update_query = mysqli_query($conn, " UPDATE users set fname = '{$fname}',lname = '{$lname}',
                                email = '{$email}', password = '{$encrypt_pass}', img = '{$new_img_name}' WHERE unique_id = {$_SESSION['unique_id']}");
                               if($update_query){
                                $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
                                if(mysqli_num_rows($select_sql2) > 0){
                                    $result = mysqli_fetch_assoc($select_sql2);
                                    echo "success";
                                }else{
                                    echo "This email address not Exist!";
                                }
                                }else{
                                    echo "Something went wrong. Please try again!";
                                }
                            }
                        }else{
                                                 echo "Please upload an image file - jpeg, png, jpg";
                           }
                    }else{
                        $encrypt_pass = md5($password);
                        $update_query = mysqli_query($conn, " UPDATE users set fname = '{$fname}',lname = '{$lname}',
                         email = '{$email}', password = '{$encrypt_pass}' WHERE unique_id = {$_SESSION['unique_id']}");
                            if($update_query){
                                $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
                                if(mysqli_num_rows($select_sql2) > 0){
                                    $result = mysqli_fetch_assoc($select_sql2);
                                    echo "success";
                                }else{
                                    echo "This email address not Exist!";
                                }
                            }else{
                                echo "Something went wrong. Please try again!";
                            }
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
        echo "All input fields are required!";
    }
?>