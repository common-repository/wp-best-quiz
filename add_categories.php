<?php
if(isset($_GET['qid']))
{
    global $wpdb;
    $qid = $_GET['qid'];
    //$delete_sql = "delete from wp_question_category_table where quiz_category_id=$qid";
    //$wpdb->query($delete_sql);
    
    $prefix = $wpdb->prefix;
    $wpdb->query( $wpdb->prepare( 
	    "delete from {$prefix}question_category_table where quiz_category_id= %d",$qid) );
  
}


class Quiz_Categories {
    function __construct()
    {
	global $wpdb;
	$this->db = $wpdb;
	$this->show();
	$this->listing();
    }
	 
    function show()
    {
	$cid = $_GET['cid'];
	if(isset($cid))
	{
	    /*$edit_res = $this->db->get_results( 
			$this->db->prepare("SELECT * FROM wp_question_category_table where quiz_category_id='".$cid."'"));*/
	    
	    $prefix = $this->db->prefix;
	    $edit_res = $this->db->get_results( $this->db->prepare( 
	    "SELECT * FROM {$prefix}question_category_table where quiz_category_id=%d",$cid) );
	    
			foreach($edit_res as $editres)
			{
			   $cat_quest = $editres->quiz_category;
			    
			}
	}
	if(isset($_POST['submit']))
	{
	   if(isset($cid))
	   {
	       /*$this->db->query( 
			$this->db->prepare("UPDATE wp_question_category_table set quiz_category='".$_POST['qcategory']."' where quiz_category_id='".$cid."'"));*/
	       $prefix = $this->db->prefix;
	       $this->db->query( $this->db->prepare( 
	       "UPDATE {$prefix}question_category_table set quiz_category=%s where quiz_category_id=%d",$_POST['qcategory'],$cid) );
	       
	       
			$this->redirect();
	   }
	   else
	   {
	   $this->insert_category();
	   }
	}
	else
	{
	echo '<form name="catgry" id="catgry" method="post" align="center">';
	
	echo '<table border="1" width="80%" class="wp-list-table widefat fixed posts">';
	
	echo '<tr>
	    <td style="width:217px">Enter your category name</td>
	    <td><textarea name="qcategory" id="qcategory" style="width:400px">'.$cat_quest.'</textarea></td>
	    </tr>';
	echo '<tr>
	    <td align="right"><input type="submit" name="submit" value="Insert" /></td>
	    </tr>';
	echo '</table>';
	echo '</form>';
	}
	
    }
    function insert_category() {
	echo $qcategory = $_POST['qcategory'];
	
	/*$insid = $this->db->query( 
		$this->db->prepare("INSERT INTO wp_question_category_table (
		`quiz_category` ,
		`quiz_category_questions_count`
		)
		VALUES (
		'".$qcategory."', '0')"));*/
	
	echo $prefix = $this->db->prefix;
	
	$insid = $this->db->query( $this->db->prepare( 
	"
		INSERT INTO {$prefix}question_category_table
		( quiz_category, quiz_category_questions_count )
		VALUES ( %s, %d )
	", 
        array(
		$qcategory, 
		'0'
	) 
) );
	
	
	
	?>
		<script language="javascript">
		window.location='<?php echo home_url(); ?>/wp-admin/admin.php?page=add_categories';
		</script>
		<?php
		
    }
    function listing()
    {
	$res = $this->db->get_results( 
		$this->db->prepare('select * from wp_question_category_table'));
	echo '<form name="catgry_list" id="catgry_list">';
	echo '<table width="80%" border="1" class="wp-list-table widefat fixed posts">';
	echo '<thead>
	    <th style="padding-left:110px">ID</th>
	    <th>Category Name</th>
	    <th style="padding-left:59px">Question Count</th>
	    <th>&nbsp;</th>
	    <th>&nbsp;</th>
	    </thead>';
	foreach($res as $result) { 
		echo '<tr><td align="center">
		'.$result->quiz_category_id.'
		</td>
		<td align="left">
		'.$result->quiz_category.'
		</td>';
		
		/*$count_ques = $this->db->get_row(
			$this->db->prepare('select count(quiz_id) as count_num from wp_question_table where category='.$result->quiz_category_id));*/
		
		
		$prefix = $wpdb->prefix;
		$edit_res = $this->db->get_row( $this->db->prepare( 
		"SELECT count(quiz_id) as count_num from {$prefix}question_table where category=%d",$result->quiz_category_id) );
		
		echo "<br>";
		echo '<td align="center">
		'.$count_ques->count_num.'
		</td>
		
		<td align="center">
		<a href="admin.php?page=add_categories&cid='. $result->quiz_category_id.'">Edit</a>

		</td>
		<td align="center">
		<input type="button" onclick="return delet_cat('.$result->quiz_category_id.');" value="Delete" />
		
		
		</td>
		</tr>';
		}
	
	echo '</table>';
	echo '</form>';
    }
    
    function redirect()
	{
		
		unset($_POST);
		//unset($_POST['ans']);
		
		ob_flush();
		//wp_remote_retrieve_body( wp_remote_get('http://localhost/subina_wordpress/wp-admin/admin.php?page=quiz') );
		
		//wp_redirect('http://localhost/subina_wordpress/wp-admin/admin.php?page=quiz');
		?>
		<script language="javascript">
		window.location='<?php echo home_url(); ?>/wp-admin/admin.php?page=add_categories';
		</script>
		<?php
	}
    
    
}

if(class_exists(Quiz_Categories))
{
    global $quiz_cat;
    $quiz_cat = new Quiz_Categories;
}

include_once('quiz_scripts.php');

?>

