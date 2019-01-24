<?php
    /**
     * Untuk mempermudah snippet ke wordpress, maka semuannya dilakukan dalam satu file
     */

    if(isset($_POST['source'])){
        $source = $_POST['source'];

        if(empty($source)){
            echo 'Page source tidak boleh kosong.';
        }else{
            $data = ParseSource($source);
            //InsertToDatabase($data);
            echo '<table border="1">';
            echo '<tr>';
            echo '<th colspan="2" style="text-align:center;">' . $data['nama'] . '<br>' . $data['nim'] . '<br>' . $data['prodi'] . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th style="text-align:center;">Hari</th>';
            echo '<th style="text-align:center;">Mata Kuliah</th>';
            echo '</tr>';
            $datastring = serialize($data['matkul']);
            if(strpos($datastring, 'Senin') !== false){
                echo '<tr>';
                echo '<td>Senin</td>';
                echo '<td>';
                echo '<br>';
                foreach ($data['matkul'] as $matkul) {
                    foreach ($matkul['waktu'] as $waktu) {
                        if($waktu['hari'] == 'Senin'){
                            echo $matkul['nama'] . '<br>' . $waktu['jam'] . ' | ' . $waktu['ruang'] . '<br><br>';
                        }
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
            if(strpos($datastring, 'Selasa') !== false){
                echo '<tr>';
                echo '<td>Selasa</td>';
                echo '<td>';
                echo '<br>';
                foreach ($data['matkul'] as $matkul) {
                    foreach ($matkul['waktu'] as $waktu) {
                        if($waktu['hari'] == 'Selasa'){
                            echo $matkul['nama'] . '<br>' . $waktu['jam'] . ' | ' . $waktu['ruang'] . '<br><br>';
                        }
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
            if(strpos($datastring, 'Rabu') !== false){
                echo '<tr>';
                echo '<td>Rabu</td>';
                echo '<td>';
                echo '<br>';
                foreach ($data['matkul'] as $matkul) {
                    foreach ($matkul['waktu'] as $waktu) {
                        if($waktu['hari'] == 'Rabu'){
                            echo $matkul['nama'] . '<br>' . $waktu['jam'] . ' | ' . $waktu['ruang'] . '<br><br>';
                        }
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
            if(strpos($datastring, 'Kamis') !== false){
                echo '<tr>';
                echo '<td>Kamis</td>';
                echo '<td>';
                echo '<br>';
                foreach ($data['matkul'] as $matkul) {
                    foreach ($matkul['waktu'] as $waktu) {
                        if($waktu['hari'] == 'Kamis'){
                            echo $matkul['nama'] . '<br>' . $waktu['jam'] . ' | ' . $waktu['ruang'] . '<br><br>';
                        }
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
            if(strpos($datastring, 'Jumat') !== false){
                echo '<tr>';
                echo '<td>Jumat</td>';
                echo '<td>';
                echo '<br>';
                foreach ($data['matkul'] as $matkul) {
                    foreach ($matkul['waktu'] as $waktu) {
                        if($waktu['hari'] == 'Jumat'){
                            echo $matkul['nama'] . '<br>' . $waktu['jam'] . ' | ' . $waktu['ruang'] . '<br><br>';
                        }
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
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
            $namaMatkul = substr($piece, 0, strpos($piece, ' <'));
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
                $WaktuList[$j] = array(
                    'hari' => $hari,
                    'jam' => $jam,
                    'ruang' => $ruang
                );
            }

            $matkul[$i] = [
                'nama' => $namaMatkul,
                'waktu' => $WaktuList
            ];
        }

        return array(
            'nama' => $nama,
            'nim' => $nim,
            'prodi' => $prodi,
            'matkul' => $matkul
        );
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

        $insertSQL = "INSERT INTO jadwalizer (nama, nim, prodi, jadwal) VALUES (:nama, :nim, :prodi, :jadwal)";
		$query = $conn->prepare($insertSQL);
        $query->bindParam(':nama', $data['nama'] );
        $query->bindParam(':nim', $data['nim'] );
        $query->bindParam(':prodi', $data['prodi']);
        $query->bindParam(':jadwal', json_encode($data['matkul']));
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