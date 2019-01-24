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
        //pangkas atas bawah yg gk perlu
        $source = substr($source, strpos($source, 'NIM'));
        $source = substr($source, 0, strpos($source, 'Ringkasan'));
        
        $profil = substr($source, 0, strpos($source, 'SEMESTER'));
        $jadwal = substr($source, strpos($source, 'fieldset'));

        $profil = explode('">', $profil);
        $nim = substr($profil[1], 0, strpos($profil[1], '<'));
        $prodi = substr($profil[4], 0, strpos($profil[4], '<'));
        $nama = substr($profil[6], 0, strpos($profil[6], '<'));
        
        $jadwal = preg_split('/<\/td>[\n\s]*<td>[\n\s]*<\/td>[\n\s]*<td>/', $jadwal);
        $amount = sizeof($jadwal) - 1;
        
        $matkul = array();

        for ($i=0; $i < $amount; $i++) {
            $piece = $jadwal[$i+1]; 
            $namaMatkul = substr($piece, 0, strpos($piece, '<'));
            //buang kode matkul
            $namaMatkul = substr(strstr($namaMatkul, ' '), 1);

            $piece = strstr($piece, 'Kuliah');
            $waktu = substr($piece, 0, strpos($piece, 'UTS'));
            $waktu = explode('sp; ', $waktu);

            $WaktuList = array();
            //ekstra kuli maks
            for ($j=1; $j < sizeof($waktu); $j++) {
                $waktu[$j] = substr($waktu[$j], 0, strpos($waktu[$j], ' <'));
                $hari = substr($waktu[$j], 0, strpos($waktu[$j], ','));
                $jam = substr($waktu[$j], strpos($waktu[$j], ',') + 2, 11);
                $ruang = strstr($waktu[$j], '(');
                $ruang = substr($ruang, 1, strpos($ruang, ')') - 1);
                $WaktuList[$j] = array([
                    'hari' => $hari,
                    'jam' => $jam,
                    'ruang' => $ruang
                ]);
            }

            $matkul[$i] = [
                'nama' => $namaMatkul,
                'waktu' => $WaktuList
            ];

            echo htmlspecialchars($namaMatkul) . '<br>';
            var_dump($WaktuList);
        }
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