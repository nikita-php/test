<?php
class SQL
{
	protected static $Connect;

	public static $insert_id;

	public function __construct()
	{
		$this->Connect();
	}

	public function Connect()
	{
		self::$Connect = mysqli_connect(HOST,USER,PASSWORD,DATABASE);
		if (self::$Connect)
		{
			mysqli_query(self::$Connect,"SET NAMES 'UTF8'");
		}
		else
		{
			exit;
		}
	}

	public static function IsTable($Name)
	{
		if (self::Query("SHOW TABLES LIKE '".$Name."'")->num_rows > 0)
			return true;
		else 
			return false;
	}

	public static function CreateTable($Name)
	{
		return self::Query("CREATE TABLE ".$Name." (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY)");
	}

	public static function Add_Fields($Name,$Array = array())
	{
		// id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
		$count = count($Array);
		$i = 0;

		$Query = "ALTER TABLE ".$Name." ";
		foreach ($Array as $key => $value)
		{
			if ($i < $count-1)
			{
				$Query .= " ADD ".$key." ".$value.",";
			}
			else
			{
				$Query .= " ADD ".$key." ".$value;
			}
			$i++;
		}

		return self::Query($Query);
	}

	public static function &Query($Query)
	{
		return mysqli_query(self::$Connect,$Query);
	}

	public static function MySQL_Error()
	{
		return mysqli_error(self::$Connect);
	}

	public static function &Select($Table,$Select = "*",$Where = "",$LIMIT = "",$ORDER_BY = "")
	{		
		return self::Query("SELECT ".$Select." FROM `".$Table.'`'.($Where ? " WHERE ".$Where : "").($ORDER_BY ? " ORDER BY ".$ORDER_BY : "").($LIMIT ? " LIMIT ".$LIMIT : ""));
	}

	public static function Insert($Table,$Insert)
	{
		return self::Query("INSERT INTO `".$Table."` VALUES (".$Insert.")");
	}

	public static function Insert_ID()
	{
		return mysqli_insert_id(self::$Connect);
	}

	public static function Update($Table,$Update,$Where)
	{
		return self::Query("UPDATE `".$Table."` SET ".$Update." WHERE ".$Where);
	}

	public static function Num_Rows(&$Result)
	{
		return $Result->num_rows;
	}

	public static function IsRows(&$Result)
	{
		return self::Num_Rows($Result) > 0;
	}

	public static function &Fetch_Object(&$Result)
	{
		return mysqli_fetch_object($Result);
	}

	public static function &Fetch_Assoc(&$Result)
	{
		return mysqli_fetch_assoc($Result);
	}

	public static function &Fetch_Array(&$Result)
	{
		return mysqli_fetch_array($Result);
	}

	public static function &Rows_Object(&$Result)
	{
		$Rows = array();
		if (self::Num_Rows($Result) > 0)
		{
			while($Row = self::Fetch_Object($Result))
			{
				$Rows[] = $Row;
			}
		}
		return $Rows;
	}

	public static function &Rows_Assoc(&$Result)
	{
		$Rows = array();
		if (self::Num_Rows($Result) > 0)
		{
			while($Row = self::Fetch_Assoc($Result))
			{
				$Rows[] = $Row;
			}
		}
		return $Rows;
	}

	public static function &Rows_Array(&$Result)
	{
		$Rows = array();
		if (self::Num_Rows($Result) > 0)
		{
			while($Row = self::Fetch_Array($Result))
			{
				$Rows[] = $Row;
			}
		}
		return $Rows;
	}

	public static function &Rows(&$Result,$Type = 'Assoc')
	{
		if ($Type == 'Assoc')
		{
			return self::Rows_Assoc($Result);
		}
		else if ($Type == 'Object')
		{
			return self::Rows_Object($Result);
		}
		else
		{
			return self::Rows_Array($Result);
		}
	}

	public static function &Get_Rows_Array($Table,$Select = "*",$Where = "",$LIMIT = "",$ORDER_BY = "")
	{
		return self::Rows_Array(self::Select($Table,$Select,$Where,$LIMIT,$ORDER_BY));
	}

	public static function &Get_Rows_Assoc($Table,$Select = "*",$Where = "",$LIMIT = "", $ORDER_BY = "")
	{
		return self::Rows_Assoc(self::Select($Table,$Select,$Where,$LIMIT,$ORDER_BY));
	}

	public static function &Get_Rows_Object($Table,$Select = "*",$Where = "",$LIMIT = "", $ORDER_BY = "")
	{
		return self::Rows_Object(self::Select($Table,$Select,$Where,$LIMIT,$ORDER_BY));
	}
} 
?>
