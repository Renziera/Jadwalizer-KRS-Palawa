<?php
    /**
     * Untuk mempermudah snippet ke wordpress, maka semuannya dilakukan dalam satu file
     */

    if(isset($_POST['source'])){
        $source = $_POST['source'];

        if(empty($source)){
            $message = 'Page source tidak boleh kosong.';
        }else{
            ParseSource($source);
        }
    }

    /**
     * Untuk parsing page source
     * Pake regex manual biar greget
     * Mau pake DOM Parser tapi dari palawa gk ada id semua tag nya :(
     */
    function ParseSource($source){
        
        $source = substr($source, strpos($source, 'NIM'));
        echo $source;
    }

    /**
     * Untuk penyimpanan data ke database
     */
    function InsertToDatabase($data){
        $servername = "localhost";
        $username = "renziera_tilepelisa";
        $password = "5%pQhnDs@g8O#Zen";
        $database = "renziera_tilepelisa";

        try{
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            echo "Koneksi database sukses<br>";
        }catch(PDOException $e){

        }

        $insertSQL = "INSERT INTO tilepanku (nama_file, indeks_elisa, tanggal) VALUES (:nama_file, :index_elisa, :tanggal)";
		$query = $conn->prepare($insertSQL);
        $query->bindParam(':nama_file', $nama_file );
        $query->bindParam(':index_elisa', $indeks );
        $query->bindParam(':tanggal', $tanggal );
		$query->execute();
    }
     
?>

<!DOCTYPE html>
     <html>
     <body>
         <form action="" method="post" name="asdf" id="asdf">
             <textarea name="source" id="asdf" cols="30" rows="10"  style="overflow:auto;resize:none" 
                form="asdf" placeholder="Paste page source dari Palawa di sini"></textarea>
             <br>
             <input type="submit" value="Proses">
         </form>
     </body>
     </html>