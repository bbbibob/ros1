<?PHP

/////// ������
//require_once("/roscomsos/roscomsos.php");
//$Roscomsos=new Roscomsos();
//$check_gos_ip=$Roscomsos->check_ip($_SERVER['REMOTE_ADDR']); // ���� true, �� IP �������� ����������� ��������� ������� ����������, ����� ��� �������� ��� ���� ��� ������ (�������������) �������
//


class Roscomsos {

    public $time_update='5'; // ��������� ������ ������ 5 ����, ����� ��������� � 10 � 30
  //  public $url_update='http://roscenzura.com/roscomsos/'; // ���� ��� �� ������, �� ���������� ������ �� ����������
    public $file_ip='gosip.data'; // ������ ������ ������
    public $file_short='gosip_short.data'; // ����������� ������ ������ � ������� ����� ������� ��������� IP ��� ��������������� ��������
	public $file_log='gosiplog.txt'; // ���������������, ���� �� ������ ���������� IP ����������
	public $d; // ���������� �������
	public $ip;
	public $url_update='';
	
	public function __construct() 
	{
        $this->d = $_SERVER['DOCUMENT_ROOT'].'/roscomsos/'; // �� �������� ��������� ����� 0777 �� �����
    }
	
	public function update_data()
	{
	  $file=$this->d.$this->file_ip;
	  
	  if ($this->url_update=='') return;

	  if (!is_file($file) or (filemtime($file)+ $this->time_update * 24 * 3600 < time()) )
	  {
	    file_put_contents($file, file_get_contents($this->url_update.$this->file_ip));
		file_put_contents($this->d.$this->file_short, file_get_contents($this->url_update.$this->file_short));
		
	    if (!is_file($file)) die('���������� ����� ��� ������ �� ����� ' . $this->d);
	  }
	}
	
	// ���� IP � ���������, ��������, ����� 155.39.133.161-155.39.133.174
	public function find_range($range)
	{
	 $range=explode('-', $range);
	 if (!isset($range[1])) return false;

	 return ( ip2long($this->ip)>=ip2long($range[0]) && ip2long($this->ip)<=ip2long($range[1]) );
	}
	
	// ���� IP � �������, ��������, 151.224.182.0/23
	public function find_subnet($range)
	{
	  list ($subnet, $bits) = explode('/', $range);
	 
	  $ip = ip2long($this->ip);
	  $subnet = ip2long($subnet);
	 
	  $mask = -1 << (32 - $bits);
	  $subnet &= $mask;
	 
	  return ($ip & $mask) == $subnet;
	}
	
	// ��������� IP � ��� �����
	public function check_ip($ip=false, $log=true)
	{
	  if ($ip) $this->ip=$ip;
	  
	  if ($this->ip != long2ip(ip2long($this->ip))) return; // ���� IP6, ��������
	  
 	  if (!is_file($this->d.$this->file_short)) $this->update_data(); else if( date("d") % $this->time_update == 0) $this->update_data(); // ���� ���� ������ �� ������ ��� ����� �� ���������� - ���������
	  
	  
	  $bytes=explode('.', $this->ip);
	  
	 // var_dump(file_get_contents($this->d.$this->file_short));
	  
	  if (strpos(file_get_contents($this->d.$this->file_short), '|'.$bytes[0].'.'.$bytes[1].'|' )===false) return false; // ���� ������ ��� ����� � ������ ������, ����������� ����������� IP ������������� �����


	  $file=file_get_contents($this->d.$this->file_ip);
	  	  
	  if (strpos($file, '|'.$bytes[0].'.'.$bytes[1].'.'.$bytes[2].'.')!==false) return $this->logGosIp($log);  // ������� ����� ����������: ���� ��� ����� ���������, �����������
	  
	  
	  $find_m='||'.$bytes[0].'.'.$bytes[1].'||';
	   
	  // ����� �������� ������ � ������ �� ������� ����� �������, ����� �� ��������� �� ����� ������
	  $file=substr($file, strpos($file, $find_m)+strlen($find_m) );  
	  $file=substr($file, 0, strpos($file, '||') );
	  
	  // ��������� ��������� IP � ���������
	  $ip_c=explode('|', $file); $check=false;
	  foreach ($ip_c as $i=>$gip)
	  {
	  	if (strpos($gip, '/')) $check=$this->find_subnet($gip); else $check=$this->find_range($gip);	
		
		if ($check==true) return $this->logGosIp($log);

	  } 
	}

	// ��������� IP ����������
	public function logGosIp($log=false)
	{
		if ($log) file_put_contents($this->d.$this->file_log, $this->ip.chr(13).chr(10), FILE_APPEND);
		
		return true;
	}

}
?>