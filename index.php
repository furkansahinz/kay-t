<?php 

	
	include ('ayar.php');
	if($_POST){
	$Email 		  = $_POST["Email"];
	$Sifre  	  = password_hash($_POST['Sifre'], PASSWORD_BCRYPT);
	$Ad 		  = $_POST["Ad"];
	$Soyad 		  = $_POST["Soyad"];
	$Tckn 		  = $_POST["Tckn"];
	$Yil 		  = $_POST["Yil"];

	if ($Email=="" || $Ad=="" || $Soyad=="" || $Tckn=="" || $Yil==""){ 
		echo "bos";
	}else{ 

	function tcno_dogrula($bilgiler){

    $gonder = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
    <TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
    <TCKimlikNo>'.$bilgiler["Tckn"].'</TCKimlikNo>
    <Ad>'.$bilgiler["Ad"].'</Ad>
    <Soyad>'.$bilgiler["Soyad"].'</Soyad>
    <DogumYili>'.$bilgiler["Yil"].'</DogumYili>
    </TCKimlikNoDogrula>
    </soap:Body>
    </soap:Envelope>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POST,           true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,    $gonder);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
    'POST /Service/KPSPublic.asmx HTTP/1.1',
    'Host: tckimlik.nvi.gov.tr',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
    'Content-Length: '.strlen($gonder)
    ));
    $gelen = curl_exec($ch);
    curl_close($ch);
    return strip_tags($gelen);
    }

    $bilgiler = array(
        "Ad"    => $Ad,
        "Soyad" => $Soyad,
        "Yil"   => $Yil,
        "Tckn"  => $Tckn
    );

    $sonuc = tcno_dogrula($bilgiler);

    if($sonuc!="true"){
		$mesaj = "<center><h4>Lütfen Doğru TC Kimlik Numarası Giriniz!</center></h4>";
    }else{

	/* veri varmı kontrol et */ 
	$hasan = $veritabani->prepare("SELECT * FROM kullanicilar WHERE Email =:Email");
	$hasan->execute(array('Email'=>$Email));
	$saydirma = $hasan->rowCount();
		if($saydirma >0){
	$mesaj = "<center><h4>Bu Mail Sistemde Mevcut!</center></h4>";
		}else{				
	/* Yoksa Kullanıcılara Yapıştır :D */ 

	global $veritabani;
	$ekle   = $veritabani->prepare("INSERT into kullanicilar set email=?,sifre=?,isim=?,soyisim=?,tckimlik=?,yil=?");
	$insert = $ekle->execute(array($Email,$Sifre,$Ad,$Soyad,$Tckn,$Yil));
	if($insert){
	
		$mesaj = "<center><h4>Kaydınız Başarıyla Oluşturuldu!</center></h4>";
		header("refresh:5;url=./");
		}
		}	
		}  }
		  }

?>



					
					
					

<!DOCTYPE html>
<html>
<head>
<TITLE>HASAN ATİLAN - PHP PDO TC KİMLİK DOĞRULAMALİ KAYİT SİSTEMİ</TITLE>
<meta charset="utf-8">
<link rel="icon" type="image/png" href="icon.png">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#2c3e50">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <link href="tasarim/bootstrap.min.css" rel="stylesheet">
    <link href="tasarim/metisMenu.min.css" rel="stylesheet">
    <link href="tasarim/sb-admin-2.css" rel="stylesheet">
    <link href="tasarim/morris.css" rel="stylesheet">
	<link href="tasarim/ha.css" rel="stylesheet">	
    <link href="tasarim/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Kayıt ol</h3>
                    </div>
					
                    <div class="panel-body">
	<?php if(!empty($mesaj)): ?>
	<p><?= $mesaj ?></p>
	<?php endif; ?>
                       					
                            <fieldset>
							<form action="kayit.php" method="POST">
    	<div class="form-group">
		<input class="form-control" type="text" placeholder="İsim" name="Ad" required>
		</div>
		<div class="form-group">
		<input class="form-control" type="text" placeholder="Soyisim" name="Soyad" required>
		</div>
		<div class="form-group">
		<input class="form-control" type="number" placeholder="TC KİMLİK" name="Tckn" required>
		</div>
		<div class="form-group">
		<input class="form-control" type="number" placeholder="Doğum Yılı" name="Yil" required>
		</div>
		<div class="form-group">
		<input class="form-control" type="text" placeholder="E-posta" name="Email" required>
		</div>
        <div class="form-group">		
		<input class="form-control" type="password" placeholder="Şifre" name="Sifre" required>
        </div>
        <div class="form-group">		
		<input class="form-control" type="password" placeholder="Şifre tekrarı" name="tekrar_sifre" required>
		</div>
		<input class="btn btn-lg btn-success btn-block" type="submit" value="Kayıt ol">
		
		</form>
		<br><a ui-sref="register" class="btn btn-lg btn-warning btn-block" href="giris.php">Giriş Yap</a>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="tasarim/jquery.min.js"></script>
    <script src="tasarim/bootstrap.min.js"></script>
    <script src="tasarim/metisMenu.min.js"></script>
    <script src="tasarim/sb-admin-2.js"></script>

</body>
</html>
