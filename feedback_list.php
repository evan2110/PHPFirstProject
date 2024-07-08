<?php
    require './components/header.php';
    echo "<h1>List of feedbacks here</h1>";
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 3;
    $search = isset($_GET['search']) ? $_GET['search'] : "";
    $currPage = $_GET['page'] ?? 1;

    $sql = "SELECT id, name, email, body, date FROM FEEDBACK ";
    $sqlAll = "SELECT count(*) FROM FEEDBACK";

    if($search != ""){
        $sql .= " WHERE name LIKE '%$search%'";
        $sqlAll .= " WHERE name LIKE '%$search%'";
    }
    if($currPage == 1){
         $sql .= " LIMIT $limit OFFSET 0";
    }else{
        $offset = ($currPage - 1) * $limit;
        $sql .= " LIMIT $limit OFFSET $offset";
    }
    
    if($connection != null){
        try{
            $statement = $connection->prepare($sqlAll);
            $statement->execute(); //execute cau lenh sql tren
            $feedbacksNum = $statement->fetchColumn(); //// Lấy giá trị số lượng bản ghi
            $feedbacksNumPage = ceil((int)$feedbacksNum / $limit);

            $statement2 = $connection->prepare($sql);
            $statement2->execute(); //execute cau lenh sql tren
            $result2 = $statement2->setFetchMode(PDO::FETCH_ASSOC); //doc du lieu ra. :: la goi den thuoc tinh static
            $feedbacks2 = $statement2->fetchAll(); //tra ra danh sach ban ghi
            $feedbacksNum2 = count($feedbacks2);


            echo "
            <form action='{$_SERVER['PHP_SELF']}' method='get'>
            <div class='input-group rounded mb-3'>
                    <input  value='". ($search != "" ? $search : '') .  "'name='search' type='search' class='form-control rounded' placeholder='Search' aria-label='Search' aria-describedby='search-addon' />
                    <input hidden type='text' name='limit' value=$limit>
                    <span class='input-group-text border-0' id='search-addon'>
                        <button type='submit'><i class='fas fa-search'></i></button> 
                    </span>
                </div>
            </form>
            ";
            foreach($feedbacks2 as $feedback){
                $id = $feedback['id'] ?? 0;
                $name = $feedback['name'] ?? ''; //?? la neu khong co thi gan gia tri rong
                $email = $feedback['email'] ?? '';
                $body = $feedback['body'] ?? '';
                $date = $feedback['date'] ?? '';
                echo "$name, $email, $body, $date  <a href='{$_SERVER['PHP_SELF']}?id=$id&mode=edit'> <i class='fa-solid fa-pen-to-square'></i> </a> <a href='{$_SERVER['PHP_SELF']}?id=$id&mode=delete'> <i id='icon-delete' class='fa-solid fa-trash'></i> </a> <br>";
            }
                echo "
                <nav aria-label='Page navigation example'>
                <ul class='pagination'> ";
            if($currPage > 1){
                echo "<li class='page-item'><a class='page-link' href='" . $_SERVER['PHP_SELF'] . "?page=" . ($currPage - 1) .  "&limit=$limit&search=$search'>Previous</a></li>";
            }
            for($i = 1; $i<=$feedbacksNumPage; $i++){
                echo "<li class='page-item'><a class='page-link' href='{$_SERVER['PHP_SELF']}?page=$i&limit=$limit&search=$search'>$i</a></li>";


            }
            if($currPage < $feedbacksNumPage){
                echo "<li class='page-item'><a class='page-link' href='" . $_SERVER['PHP_SELF'] . "?page=" . ($currPage + 1) . "&limit=$limit&search=$search'>Next</a></li>";
            }
                echo "
            </ul>
            </nav>

            <form action='{$_SERVER['PHP_SELF']}' method='get'>
            <label for='so_item'>Chọn số lượng item hiển thị:</label>
            <input hidden type='text' name='search' value=$search>
            <select name='limit' id='so_item'>
                 <option value='3'" . ($limit == 3 ? ' selected' : '') . ">3</option>
                 <option value='5'" . ($limit == 5 ? ' selected' : '') . ">5</option>
                 <option value='7'" . ($limit == 7 ? ' selected' : '') . ">7</option>
                 <option value='9'" . ($limit == 9 ? ' selected' : '') . ">9</option>
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