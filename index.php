<?php
define('HOST','localhost');
define('USER','root');
define('PASSWORD','');
define('DATABASE','basename');

// Класс для упращения работы с mysqli
include_once "SQL.php";

class JSON_API
{
	protected $SQL;

	public function __construct()
	{
		$SQL = new SQL();

		// Получить один отзыв
		/*$_REQUEST['get_review'] = 'item';
		$_REQUEST['id'] = 3;
		$_REQUEST['fields'] = json_encode(array("desc","photos"));*/
		
		// Получить все отзывы
		/*$_REQUEST['get_review'] = 'list';
		$_REQUEST['page'] = 1;
		$_REQUEST['range'] = 5;
		$_REQUEST['order'] = "rate";
		$_REQUEST['order_by'] = "asc";*/

		// Добавление отзыва
		/*$_REQUEST['set_review'] = 'item';
		$_REQUEST['name'] = "nikita";
		$_REQUEST['desc'] = "desc2";
		$_REQUEST['photos'] = "http://www.dom.ru/photo_1.jpg https://www.dom.ru/photo_2.png http://www.dom.ru/photo_3.jpg";
		$_REQUEST['rate'] = "3";*/


		if (isset($_REQUEST['get_review']) && !isset($_REQUEST['set_review']))
		{
			if ($_REQUEST['get_review'] == 'list')
			{
				$this->Get_List_Review();
			}
			else if ($_REQUEST['get_review'] == 'item')
			{
				$this->Get_Item_Review();
			}
		}
		else if (isset($_REQUEST['set_review']) && !isset($_REQUEST['get_review']))
		{
			if ($_REQUEST['set_review'] == 'item')
			{
				$this->Set_Item_Review();
			}
		}
		else
		{
			echo json_encode(array('error' => 'not data'));
		}
	}

	public function Get_List_Review()
	{
		$Filter = '';
		$Table = 'reviews';

		$Result = SQL::Select($Table,'*',$Filter);
		
		if ($Result->num_rows > 0)
		{
			include_once "Pagination.php";
			$Pagination = new Pagination();

			$Pagination->Elements = $Result->num_rows;

			if (!empty($_REQUEST['page']))
			{
				$Pagination->This_Page = (int)$_REQUEST['page'];
			}
			else
			{
				$Pagination->This_Page = 1;
			}
		
			if (!empty($_REQUEST['range']))
			{
				$Pagination->Range = (int)$_REQUEST['range'];
			}
			else
			{
				$Pagination->Range = 5;
			}


			if (!empty($_REQUEST['order']) && $_REQUEST['order'] == 'rate')
			{
				if (!empty($_REQUEST['order_by']) && $_REQUEST['order_by'] == 'asc')
					$ORDER_BY = ' `r_rate` ASC';
				else
					$ORDER_BY = ' `r_rate` DESC';
			}
			else if (!empty($_REQUEST['order']) && $_REQUEST['order'] == 'date_create')
			{
				if (!empty($_REQUEST['order_by']) && $_REQUEST['order_by'] == 'asc')
					$ORDER_BY = ' `r_date_create` ASC';
				else
					$ORDER_BY = ' `r_date_create` DESC';
			}
			else
			{
				$ORDER_BY = '';
			}
			
			$LIMIT = ' '.$Pagination->Range.' OFFSET '.$Pagination->Start().' ';

			echo json_encode(
				array(
					'items' => SQL::Get_Rows_Assoc($Table,'*','',$LIMIT,$ORDER_BY)
				)
			);
		}
		else
		{
			echo json_encode(array('items' => 'null'));
		}
	}

	public function Get_Item_Review()
	{	
		$Filter = '';
		$Table = 'reviews';

		if (!empty($_REQUEST['id']))
		{
			$id = (int) $_REQUEST['id'];
		}
		else
		{
			echo json_encode(array('error' => 'needed id'));
		}

		if (!empty($_REQUEST['fields']))
		{
			$fields = json_decode($_REQUEST['fields']);
		}

		$item = end(SQL::Get_Rows_Assoc($Table,'*','`id` = '.$id));

		$return['name'] = $item['r_name'];
		$return['rate'] = $item['r_rate'];
		$explode = explode(" ",$item['r_photos']);
		$return['main_photo'] = $explode[0];

		if (in_array('desc',$fields))
		{
			$return['desc'] = $item['r_desc'];
		}

		if (in_array('photos',$fields))
		{
			$return['photos'] = $item['r_photos'];
		}

		echo json_encode(
			$return
		);
	}

	public function Set_Item_Review()
	{
		$Table = 'reviews';

		// Если таблицы не существует, создаём с полями.
		if (!SQL::IsTable($Table))
		{
			SQL::CreateTable($Table);

			$fields = array(
				'r_name' => "varchar(50)",
				'r_desc' => "varchar(1000)",
				'r_photos' => "varchar(1000)",
				'r_rate' => "int(1)",
				'r_date_create' => "datetime",
			);

			SQL::Add_Fields($Table, $fields);
		}

		// Длина имени не больше 50 символов
		if (!empty($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
			if (strlen($name) >= 50)
			{
				echo json_encode(array('error validate' => 'name: symbols <= 50 '));
				exit;
			}
		}

		// Длина описания не больше 1000 символов
		if (!empty($_REQUEST['desc']))
		{
			$desc = $_REQUEST['desc'];
			if (strlen($desc) >= 1000)
			{
				echo json_encode(array('error validate' => 'name: symbols <= 1000 '));
				exit;
			}
		}

		// Количество фоток не больше 3
		if (!empty($_REQUEST['photos']))
		{
			$photos = $_REQUEST['photos'];

			preg_match_all("|https?://|",$photos,$out, PREG_PATTERN_ORDER);
			$count = count(end($out));

			if ($count > 3)
			{
				echo json_encode(array('error validate' => 'photos: <= 3'));
				exit;
			}
		}

		// Рейтинг от 1 до 5
		if (!empty($_REQUEST['rate']))
		{
			$rate = (int)$_REQUEST['rate'];
			if ($rate <= 0 || $rate > 5)
			{
				echo json_encode(array('error validate' => 'rate: >= 1 and <= 5'));
				exit;
			}
		}

	
		$Insert = SQL::Insert($Table,"Null,'$name','$desc','$photos',$rate,NOW()");
		
		if ($Insert)
		{
			echo json_encode(array('Create' => $Insert,'ID' => SQL::Insert_ID()));
		}
		else
		{
			echo json_encode(array('Create' => $Insert));
		}
	}
}

$JSON_API = new JSON_API();
?>
