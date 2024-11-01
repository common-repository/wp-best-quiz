<?php
if(isset($_GET['qid']))
{
    global $wpdb;
    $qid = $_GET['qid'];
  
    $prefix = $wpdb->prefix;
    $wpdb->query( $wpdb->prepare( 
	    "delete from {$prefix}question_table where quiz_id= %d",$qid) );
  
}
class editQuiz
{
	function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->listing();
	}
	function listing()
	{
		$prefix = $this->db->prefix;
		
		$res = $this->db->get_results( 
		$this->db->prepare("select * from {$prefix}question_table"));
		
		echo '<form name="" action="" method="GET">

		<table border="1" width="95%" class="tab wp-list-table widefat fixed posts">';
		echo '<caption style="font-weight: 600;font-size: 16px;padding-bottom: 5px;">Question listing</caption>';
		echo '<colgroup>
		<col class="col1">
		<col class="col2">
		<col class="col3">
		<col class="col4">
		<col class="col5">
		</colgroup>';
		echo '<thead>';
		echo '<tr>
		<th width="10%">
		Sl. 
		</th>
		<th width="30%">
		Question
		</th>
		<th width="10%">
		Answer
		</th>
		<th width="10%">
		Category
		</th>
		<th width="20%">
		&nbsp;
		</th>
		<th width="20%">
		&nbsp;
		</td>
		</tr>';
		echo '</thead>';
		foreach($res as $result) { 
		echo '<tr><td align="center">
		'.$result->quiz_id.'
		</td>
		<td align="left">
		'.$result->question.'
		</td>
		
		<td align="left">
		'.$result->answer.'
		</td>
		
		<td align="left">'.$this->getCatName($result->category).'</td>

		<td align="center">
		<a href="admin.php?page=quiz&qid='. $result->quiz_id.'">Edit</a>

		</td>
		<td align="center">
		<input type="button" onclick="return delet('.$result->quiz_id.');" value="Delete" />
		
		
		</td>
		</tr>';
		}
		echo '</table>
		</form>';
		
	}
	
	function getCatName($catid)
	{
	    
	    $prefix = $this->db->prefix;
	    $cat_name = $this->db->get_results( $this->db->prepare( 
	    "
	    select quiz_category from {$prefix}question_category_table
	    where quiz_category_id= %d
	    ", 
	    $catid

	    ) );
	    
	    foreach($cat_name as $cat_name_results)
	    {
		return $cat_name_results->quiz_category;
	    }
	}
}

if(class_exists(editQuiz))
{
	global $list ;
	$list = new editQuiz();
}

include_once('quiz_scripts.php');

?>
