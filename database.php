<?php
    function connectToDB() {
    
        $host = 'localhost';
        $db = 'shopping_cart_cst336_sp_2018';
        $user = 'crescencioDev';
        $pass = 'euBMQ8Y36UFFpS5O';
        $charset = 'utf8mb4';
        
        //checking whether the URL contains "herokuapp" (using Heroku)
        if(strpos($_SERVER['HTTP_HOST'], 'herokuapp') !== false) {
           $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
           $host = $url["host"];
           $db   = substr($url["path"], 1);
           $user = $url["user"];
           $pass = $url["pass"];
        }
        
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $opt);
        
        return $pdo; 
    }
    
    function getMatchingItems($query, $category, $priceFrom, $priceTo, $ordering, $showImages) {
        $db = connectToDB(); 
        
        $imgSQL = $showImages ? ', item.image_url' : ''; 
        
        $sql = "SELECT DISTINCT item.item_id, item.name, item.price $imgSQL FROM item INNER JOIN item_category ON item.item_id = item_category.item_id INNER JOIN category ON item_category.category_id =category.category_id  WHERE 1"; 
        
        if(!empty($query)){
            $sql .= " AND name LIKE '%$query%'";
        }
        
        if (!empty($category)) {
            $sql .= " AND category.category_name = '$category'";
        }
        
        if (!empty($priceFrom)) {
            $sql .= " AND item.price >= '$priceFrom'";
        }
        
        if (!empty($priceTo)) {
            $sql .= " AND item.price >= '$priceTo'";
            
        }
        
        if (!empty($ordering)) {
            if ($ordering == 'product') {
                $columnName = 'item.name'; 
            }
            else {
                $columnName = 'item.price'; 
            }
        $sql .= " ORDER BY $columnName";
        }
        
        $statement = $db->prepare($sql); 
        
        $statement->execute(); 
        
        $items = $statement->fetchAll(); 
        
        return $items; 
    }

    
    function getCategoriesHTML(){
        $db = connectToDB();
        $categoriesHTML = "<option value=''></option>";  // User can opt to not select a category 
    
        $sql = "SELECT category_name FROM category"; 
        
        $statement = $db->prepare($sql); 
        
        $statement->execute(); 
        
        $records = $statement->fetchAll(PDO::FETCH_ASSOC); 
        
        foreach ($records as $record) {
            $category = $record['category_name']; 
            $categoriesHTML .= "<option value='$category'>$category</option>"; 
        }
        
        return $categoriesHTML; 
    }
    
?>