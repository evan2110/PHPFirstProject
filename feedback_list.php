<?php
    require './components/header.php';
    echo "<h1>List of feedbacks here</h1>";
    $currPage = $_GET['page'] ?? 1;
    if($currPage == 1){
        $sql = "SELECT id, name, email, body, date FROM FEEDBACK LIMIT 3 OFFSET 0";
    }else{
        $currPage += 2;
        $sql = "SELECT id, name, email, body, date FROM FEEDBACK LIMIT 3 OFFSET $currPage";
    }
    if($connection != null){
        try{
            $statement = $connection->prepare($sql);
            $statement->execute(); //execute cau lenh sql tren
            $result = $statement->setFetchMode(PDO::FETCH_ASSOC); //doc du lieu ra. :: la goi den thuoc tinh static
            $feedbacks = $statement->fetchAll(); //tra ra danh sach ban ghi
            $feedbacksNum = count($feedbacks);
            $feedbacksNumPage = ceil($feedbacksNum / 3);
            foreach($feedbacks as $feedback){
                $id = $feedback['id'] ?? 0;
                $name = $feedback['name'] ?? ''; //?? la neu khong co thi gan gia tri rong
                $email = $feedback['email'] ?? '';
                $body = $feedback['body'] ?? '';
                $date = $feedback['date'] ?? '';
                echo "$name, $email, $body, $date  <a href='{$_SERVER['PHP_SELF']}?id=$id&mode=edit'> <i class='fa-solid fa-pen-to-square'></i> </a> <a href='{$_SERVER['PHP_SELF']}?id=$id&mode=delete'> <i id='icon-delete' class='fa-solid fa-trash'></i> </a> <br>";
            }
                echo "
                <nav aria-label='Page navigation example'>
                <ul class='pagination'>
                <li class='page-item'><a class='page-link' href='#'>Previous</a></li> ";
                for($i = 1; $i<=$feedbacksNumPage; $i++){
                    echo "<li class='page-item'><a class='page-link' href='{$_SERVER['PHP_SELF']}?page=$i'>$i</a></li>";
                }
                echo "
                <li class='page-item'><a class='page-link' href='#'>Next</a></li>
            </ul>
            </nav>

            <form action='your_page.php' method='get'>
            <label for='so_item'>Chọn số lượng item hiển thị:</label>
            <select name='so_item' id='so_item'>
                <option value='5'>5</option>
                <option value='10'>10</option>
                <option value='20'>20</option>
                <option value='50'>50</option>
            </select>
            <input type='submit' value='Xác nhận'>
            </form>
                ";
        }catch(PDOException $e){
            echo "Cannot query data.Error: " .$e->getMessage();
        }
    }

    if(!empty($_GET['mode'])){
        if($_GET['mode'] == 'delete'){
            $id = $_GET['id'];
            $sqlDelete = "DELETE FROM FEEDBACK WHERE id = ?";
            try{
                $statement = $connection->prepare($sqlDelete);
                $statement->bindParam(1, $id);
                $statement->execute();
                //echo "Deleted feedback";
                header("Location: feedback_list.php");
            }catch(PDOException $e){
                echo "Cannot delete feedback " .$e->getMessage();
            }
        }

        if($_GET['mode'] == 'edit'){
            $id = $_GET['id'];
            Header("Location: index.php?mode=edit&id=$id"); 
        }
    }

    
    
    include 'components/footer.php';
  
?>