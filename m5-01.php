
       
<?php
// 4-1 データベース接属
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード名';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//　データ定義
//ini_set('display_errors', 0);//エラー非表示
$date = date("Y/m/d/ H:i:s"); //日付
$edit_val= '';  //　編集の際のデータ格納
$edit_name = ''; //名前
$edit_comment = ''; //コメント
$edit_password = ''; //パスワード
$edit = $_POST["edit"]; //編集ボタン
$submit = $_POST["submit"]; //送信ボタン
$name = $_POST['name'];  //名前受信
$comment = $_POST['text'];  //コメント受信
$mypass = $_POST['password'];  //パスワード受信
$delete = $_POST['delete'];   //削除したい番号
$edit_number = $_POST['edit_number']; //編集したい番号


// 4-2 テーブル登録 テーブル test1
$sql = "CREATE TABLE IF NOT EXISTS mission"
   ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date date," //年月日の取得
    . "mypass varchar(50)"
    .");";
    $stmt = $pdo->query($sql);
      
        if(isset($edit)) { //編集ボタンを押したとき
        $sql = 'SELECT * FROM mission';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
            foreach($results as $row){
                if($row['id'] == $edit_number && $row['mypass'] == $mypass){ //入力された編集番号のデータを探す
                    $edit_val = $row['id']; //変数に代入
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                    $edit_password = $row['mypass'];
                    echo "パスワード一致";
                    echo "<br>";
                    break;
                }else{   //パスワード一致しないとき
                    if(!strlen($mypass)) {
                    echo "<br>";
                    echo "パスワードを入力してください";
                    } elseif(!strlen($edit_number)){
                      echo "<br>";
                      echo "編集番号を入力してください";
                    } 
                    else {
                        echo "<br>";
                        echo "パスワードが一致しません";
                        echo "<br>";
                    }
                }
            }
        } 
        
        
        
        elseif(isset($submit)){ //送信ボタン押したとき
            //書き込みデータ作成（テキストに書き込まれたデータで）
            $name_insert = $name;
            $comment_insert = $comment;
            $date_insert = $date;
            $mypass_insert = $mypass;
            echo "書き込みデータ作成";
            echo "<br>";
            if(!empty($_POST["edit_post"])){ //編集番号があれば
                $sql = 'SELECT * FROM mission';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){ 
                    if($row['id'] == $_POST["edit_post"]) { //編集番号のとき上書き
                        $id_edit = $row['id'];// id取得
                        $name_edit = $name;
                        $comment_edit = $comment;
                        $mypass_edit = $mypass;
                        $sql = 'UPDATE mission SET name=:name,comment=:comment,date=:date,mypass=:mypass WHERE id=:id_edit';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name_edit, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment_edit, PDO::PARAM_STR);
                        $stmt->bindParam(':id_edit', $id_edit, PDO::PARAM_INT);
                        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':mypass', $mypass_edit, PDO::PARAM_STR);
                        $stmt->execute();
                         echo "上書き";
                        echo "<br>";
                    }
                }
            }
             else { //編集番号がないとき
             if (empty($comment && $name && $mypass)){//それぞれからのとき
             echo "<br>";
             echo "それぞれ文字を入力してください";
             echo "<br>";
             }else { //それぞれの値が入っている時
            echo "新規投稿";
            echo "<br>";
            $name_insert = $name;
            $comment_insert = $comment;
            $date_insert = $date;
            $mypass_insert = $password;
            $sql = $pdo -> prepare("INSERT INTO mission (name, comment, date, mypass) VALUES (:name, :comment, :date, :mypass)");
            $sql -> bindParam(':name', $name_insert, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment_insert, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date_insert, PDO::PARAM_STR);
            $sql -> bindParam(':mypass', $mypass, PDO::PARAM_STR);
            $sql -> execute();
             }
             }
        }
       
       
       
   //削除
         if(strlen($delete AND $mypass)){ //$deleteに記入されているとき
            $del_id = $delete;
            $sql = 'SELECT * FROM mission';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
            if($row['id'] == $del_id && $row['mypass'] == $mypass){ //削除のデータとパスワードが一致した時
                echo "パスワード一致";
                echo "<br>";
                echo "削除しました";
             $sql = 'delete from mission where id=:del_id'; 
             $stmt = $pdo->prepare($sql);
             $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
             $stmt->execute();
                
            }
            }
            } 
         
?>
    <!DOCTYPE html>
<html lang="ja">
    <head>
        <title>m5-1.php</title>
        <meta charaset="UTF-8">
    </head>
    <body>
        <form action="" method="post">
            <input type="hidden" name="edit_post" value="<?php echo $edit_val; ?>">
            <input type="text" name="name" placeholder="名前" value="<?php echo $edit_name; ?>">
            <br>
            <input type="text" name="text" placeholder="コメント" value="<?php echo $edit_comment; ?>">
            <br>
            <input type="password" name="password" placeholder="パスワード" value="<?php echo $edit_password; ?>">
            <br>
            <input type="submit" name="submit" value="送信">
            <br>
        </form>
        <form action="" method="post">
            <input type="text" name="delete" placeholder="削除対象番号">
            <br>
            <input type="password" name="password" placeholder="パスワード">
            <br>
            <input type="submit" value="削除">
        </form>
        <form action="" method="post">
            <input type="text" name="edit_number" placeholder="編集対象番号">
            <br>
            <input type="password" name="password" placeholder="パスワード">
             <br>
            <input type="submit" name="edit" value="編集">
        </form>
        
        
        
        <?php
        // 4-6 データ表示
        $sql = 'SELECT * FROM mission';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'  ';
        echo $row['name'].' | ';
        echo $row['comment'].' | ';
        echo $row['date'].' | ';
        echo $row['mypass'].'<br>';
        echo "<hr>";
        }
        ?>
        </body>
        </html>
        