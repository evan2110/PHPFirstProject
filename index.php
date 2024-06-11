<!-- require: bat buoc phai co, neu ko co file do se bao loi. Con o footer chi warning -->
<?php require './components/header.php'; 
    $name = $email = $feedback = '';
    $name_error = $email_error = $feedback_error = '';
    $dateCurr = date("Y-m-d H:i:s");

    if(isset($_POST['submit'])){

        if($_POST['id'] == '0'){
            if(empty($_POST['name'])){
                $name_error = '<br> Name is required';
            }else{
                $name = htmlspecialchars($_POST['name']);
            }
            if(empty($_POST['email'])){
                $email_error = '<br> Email is required';
            }else{
                $email = htmlspecialchars($_POST['email']);
            }
            if(empty($_POST['feedback'])){
                $feedback_error = '<br> Feedback is required';
            }else{
                $feedback = htmlspecialchars($_POST['feedback']);
            }
    
            echo $name_error;
            echo $email_error;
            echo $feedback_error;
        
            
        
            $validate_success = empty($name_error) && empty($email_error) && empty($feedback_error);
            if($validate_success){
                $sql = "INSERT INTO FEEDBACK(Name, Email, Body, Date)
                VALUES(?,?,?,?)
                ";
        
                try{
                    $statement = $connection->prepare($sql);
                    $statement->bindParam(1, $name);
                    $statement->bindParam(2, $email);
                    $statement->bindParam(3, $feedback);
                    $statement->bindParam(4, $dateCurr);
                    $statement->execute();
                    //echo "Inserted feedback";
                    header("Location: feedback_list.php");
                }catch(PDOException $e){
                    echo "Cannot insert feedback " .$e->getMessage();
                }
                
            }
        }else{
            $id = htmlspecialchars($_POST['id']);
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $feedback = htmlspecialchars($_POST['feedback']);

            $sql = "UPDATE FEEDBACK SET Name = ?,  Email = ?, Body = ?, Date = ? WHERE id = ?";
                try{
                    $statement = $connection->prepare($sql);
                    $statement->bindParam(1, $name);
                    $statement->bindParam(2, $email);
                    $statement->bindParam(3, $feedback);
                    $statement->bindParam(4, $dateCurr);
                    $statement->bindParam(5, $id);
                    $statement->execute();
                    //echo "Updated feedback";
                    header("Location: feedback_list.php");
                }catch(PDOException $e){
                    echo "Cannot update feedback " .$e->getMessage();
                }
        }
    }

    if(!empty($_GET['mode'])){
        if($_GET['mode'] == 'edit'){
            $id = $_GET['id'];
            $sql = "SELECT id, name, email, body, date FROM FEEDBACK WHERE id = ?";
                try{
                    $statement = $connection->prepare($sql);
                    $statement->bindParam(1, $id);
                    $statement->execute(); 
                    $result = $statement->setFetchMode(PDO::FETCH_ASSOC); //doc du lieu ra. :: la goi den thuoc tinh static
                    $feedback = $statement->fetch(); //tra ra danh sach ban ghi

                    $idUpt =    $feedback['id'] ?? 0;
                    $nameUpt =  $feedback['name'] ?? ''; //?? la neu khong co thi gan gia tri rong
                    $emailUpt = $feedback['email'] ?? '';
                    $bodyUpt =  $feedback['body'] ?? '';
                    $dateUpt =  $feedback['date'] ?? '';


                }catch(PDOException $e){
                    echo "Cannot query data.Error: " .$e->getMessage();
                }
        }
    }

    
?>  

<h1>Enter your feedback</h1>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <input hidden value="<?php echo $idUpt ?? 0; ?>" type="text" name="id" class="form-control" id="floatingInput" placeholder="">
    <div class="form-floating mb-3">
        <input value="<?php echo $nameUpt ?? ''; ?>" type="text" name="name" class="form-control" id="floatingInput" placeholder="What is your name ?">
        <label for="floatingInput">What is your name ?</label>
    </div>
    <div class="form-floating mb-3">
        <input value="<?php echo $emailUpt ?? ''; ?>" type="email" name="email" class="form-control" id="floatingPassword" placeholder="Enter your email">
        <label for="floatingPassword">Enter your email</label>
    </div>
    <div class="form-floating mb-3">
        <textarea name="feedback" class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"><?php echo $bodyUpt ?? ''; ?></textarea>
        <label for="floatingTextarea2">Enter your feedback</label>
    </div>
    <div class="form-floating mb-3">
        <input class="btn btn-primary" type="submit" value="Send" name="submit"/>
    </div>
</form>

<?php include 'components/footer.php'; ?>

