<?php
if ($_POST) { // если передан массив POST
    $parts = htmlspecialchars($_POST["parts"]); // пишем данные в переменные и экранируем спецсимволы
    $phone = htmlspecialchars($_POST["phone2"]);
    $email = 'kairullin@yandex.ru';
    $json = array(); // подготовим массив ответа
    if (!$phone) { // если хоть одно поле оказалось пустым
        $json['error'] = 'Вы заполнили не все поля!'; // пишем ошибку в массив
        echo json_encode($json); // выводим массив ответа 
        die(); // умираем
    }

    function mime_header_encode($str, $data_charset, $send_charset) { // функция преобразования заголовков в верную кодировку
        if($data_charset != $send_charset)
        $str=iconv($data_charset,$send_charset.'//IGNORE',$str);
        return ('=?'.$send_charset.'?B?'.base64_encode($str).'?=');
    }
    /* суперкласс для отправки письма в нужной кодировке */
    class TEmail {
    public $from_email;
    public $from_name;
    public $to_email;
    public $to_name;
    public $subject;
    public $data_charset='UTF-8';
    public $send_charset='windows-1251';
    public $body='';
    public $type='text/plain';

    function send(){
        $dc=$this->data_charset;
        $sc=$this->send_charset;
        $enc_to=mime_header_encode($this->to_name,$dc,$sc).' <'.$this->to_email.'>';
        $enc_subject=mime_header_encode($this->subject,$dc,$sc);
        $enc_from=mime_header_encode($this->from_name,$dc,$sc).' <'.$this->from_email.'>';
        $enc_body=$dc==$sc?$this->body:iconv($dc,$sc.'//IGNORE',$this->body);
        $headers='';
        $headers.="Mime-Version: 1.0\r\n";
        $headers.="Content-type: ".$this->type."; charset=".$sc."\r\n";
        $headers.="From: ".$enc_from."\r\n";
        return mail($enc_to,$enc_subject,$enc_body,$headers);
    }

    }
    $string="Parts: ";
	$string.=$parts."; Phone: ".$phone;
	//$string=iconv('UTF-8','WINDOWS-1251',$string);
    $emailgo= new TEmail; // инициализируем супер класс отправки
    $emailgo->from_email= 'Автозапчасти'; // от кого
    $emailgo->from_name= 'Order';
    $emailgo->to_email= $email; // кому
    $emailgo->to_name= 'Исполнителю';
    $emailgo->subject= 'Новый заказ!'; // тема
    $emailgo->body=$string;  // сообщение
    $emailgo->send(); // отправляем

    $json['error'] = 0; // ошибок не было

    echo json_encode($json); // выводим массив ответа
} else { // если массив POST не был передан
    echo 'GET LOST!'; // высылаем
}
$host="localhost";
    $user="autoparts";
    $pass="parts178";
    $db_name="autoparts";
	$parts1=$_POST['parts'];
	$parts1=iconv('UTF-8','WINDOWS-1251',$parts1);
	$date=date("m.d.y");
	$time=date("H:i");
    $link = mysql_connect($host,$user,$pass);
    mysql_select_db($db_name,$link);
	if (isset($_POST["phone2"])) { // Вставляем данные, подставляя их в запрос
    $sql = mysql_query("INSERT INTO `leads` (`Запчасти`, `Телефон`, `Дата`, `Время`) 
                        VALUES ('$parts1','".$_POST['phone2']."', '$date', '$time')");
	}
iconv_set_encoding("internal_encoding", "WINDOWS-1251");
iconv_set_encoding("output_encoding", "WINDOWS-1251");
mysql_close();
?>