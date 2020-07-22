  <?php

  class Pagination
  {
    public $Elements; // Количество всех элементов
    public $Range; // Количество элементов на одной странице
    public $This_Page; // Текущая страница 1-n
    private $Pages; // Число страниц
    private $Start_Element; // Первый элемент на странице

    // Определение количества страниц
    public function Pages()
    {
      if (empty($this->Pages))
      {	
        if (($this->Elements >= $this->Range) && ($this->Elements >= 1 && $this->Range >= 1))
      	{
      		$this->Pages = (int) ceil($this->Elements / $this->Range); return $this->Pages;
      	}
      	else
      	{
      		$this->Pages = 1; return 1;
      	}
      }
      else
      {
        return $this->Pages;
      }
    }

    // Определение первого элемента в текущей странице
    public function Start()
    {
    	if ($this->Range >= 1 && $this->This_Page >= 1)
    	{
    		return ($this->This_Page * $this->Range) - $this->Range;
    	}
    	else
    	{
    		return 1;
    	}
    }

    //Выводим пагинацию
    public function My_Pagination($pages,$this_page,$start_path,$end_path)
    {
    }
}
