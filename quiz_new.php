<?php

class Quiz_Questions{
	
	 function __construct()
	 {
		global $wpdb;
		$this->db = $wpdb;
		$this->show();
	 }
	 
	
	function show()
	{
		$qid = $_GET['qid'];
		
		$category_res = $this->db->get_results($this->db->prepare("select * from wp_question_category_table"));
		
		if(isset($qid))
		{
			/*$edit_res = $this->db->get_results( 
			$this->db->prepare("SELECT * FROM wp_question_table where quiz_id='".$qid."'"));*/
			
			$prefix = $this->db->prefix;
			$edit_res = $this->db->get_results( $this->db->prepare( 
			"SELECT * from {$prefix}question_table where quiz_id=%d",$qid) );

			foreach($edit_res as $editres)
			{
			    $quest = $editres->question;
			    $optn1 = $editres->option1;
			    $optn2 = $editres->option2;
			    $optn3 = $editres->option3;
			    $optn4 = $editres->option4;
			    $answ = $editres->answer;
			    $catgry = $editres->category;
			}
		}
		if(isset($_POST['submit']))
		{
			
			if(isset($qid))
			{
			
			/*$this->db->query( 
			$this->db->prepare("UPDATE wp_question_table set question='".$_POST['question']."' , option1='".$_POST['opt1']."', option2='".$_POST['opt2']."', option3='".$_POST['opt3']."', option4='".$_POST['opt4']."', answer='".$_POST['ans']."', category='".$_POST['category']."', last_updated_date='".date('Y-m-d h:i:s')."' where quiz_id='".$qid."'"));*/
			
			$prefix = $this->db->prefix;
			$this->db->query( $this->db->prepare( 
			"UPDATE {$prefix}question_table set question=%s , option1=%s, option2=%s, option3=%s, option4=%s, answer=%d, category=%d, last_updated_date=%s where quiz_id=%d",
			$_POST['question'],
			$_POST['opt1'],
			$_POST['opt2'],
			$_POST['opt3'],
			$_POST['opt4'],
			$_POST['ans'],
			$_POST['category'],
			date('Y-m-d h:i:s'),
			$qid) );
			
			
			$this->redirect();
			}
			else
			{
			    $this->insertdb();
			    
			}
			
		}
		else
		{ 
		echo '<h1>Create A New Question</h1>';
		echo '<form name="" action="" method="POST">';
		echo '<table width="80%" border="1"  class="wp-list-table widefat fixed posts">';
		echo '<tr>
		<td>Question </td><td colspan="3"><textarea type="text" name="question" id="question" value="" style="width: 736px;">'.$quest.'</textarea></td>
		</tr>';
		
		echo '<tr><td>';
		echo $rad1 = '<input type="radio" name="select_opt" id="select_opt1" onclick="return check(this.id)"';
		if($answ==1)
		{
		    echo $rad1 .=' checked ';
		}
		else
		{
		    echo $rad1 .='';
		}
		echo $rad1 .='/><input type="text" name="opt1" id="opt1" placeholder="option 1" value="'.$optn1.'" />';
		echo ' </td>';
		echo '<td>';
		echo $rad2 ='<input type="radio" name="select_opt" id="select_opt2" onclick="return check(this.id)"';
		if($answ==2)
		{
		    echo $rad2 .=' checked ';
		}
		else
		{
		    echo $rad2 .='';
		}
		echo $rad2 .='/><input type="text" name="opt2" id="opt2" placeholder="option 2" value="'.$optn2.'" />';
		echo '</td>';
		echo '<td>';
		echo $rad3 = '<input type="radio" name="select_opt" id="select_opt3" onclick="return check(this.id)"';
		if($answ==3)
		{
		    echo $rad3 .=' checked ';
		}
		else
		{
		    echo $rad3 .='';
		}
		echo $rad3 .= '/><input type="text" name="opt3" id="opt3" placeholder="option 3" value="'.$optn3.'" /> ';
		echo '</td>';
		
		echo '<td>';
		echo $rad4 = '<input type="radio" name="select_opt" id="select_opt4" onclick="return check(this.id)" ';
		if($answ==4)
		{
		    echo $rad4 .=' checked ';
		}
		else
		{
		    echo $rad4 .='';
		}
		echo $rad4 .= '/><input type="text" name="opt4" id="opt4" placeholder="option 4" value="'.$optn4.'" /> ';
		echo '</td></tr>';
		
		echo '<tr>
		<td colspan="4"><input type="text" name="ans" id="ans" value="'.$answ.'" placeholder="correct option" /></td>
	</tr>';
		
		echo '<tr>';
		echo '<td><span style="padding-right:5px;">Select the category</span></td>';  
		
		echo '<td><select name="category">';
		foreach($category_res as $cat_res)
		{
		if($cat_res->quiz_category_id==$catgry)
		{
		    
		    $op ='selected="selected"';
		}
		else
		{
		     $op ='';
		}
		echo '<option value='.$cat_res->quiz_category_id.' '.$op.' >'.$cat_res->quiz_category.'</option>';
		}
		echo '</select></td>';    
		
		echo '<tr><td colspan="4" align="center"><input type="submit" name="submit" id="submit" /></td></tr>';
		echo '</table>
</form>';
		 } 
		
	
	}
	
	
	
	function insertdb()
	{
		$question = $_POST['question'];
		$opt1 = $_POST['opt1'];
		$opt2 = $_POST['opt2'];
		$opt3 = $_POST['opt3'];
		$opt4 = $_POST['opt4'];
		$ans = $_POST['ans'];
		$category = $_POST['category'];
		
		/*$insid = $this->db->query( 
		$this->db->prepare("INSERT INTO `wp_question_table` (
		`question` ,
		`option1` ,
		`option2` ,
		`option3` ,
		`option4` ,
		`answer` ,
		`category` ,
		`has_been_published` ,
		`publish_Date` ,
		`create_date` ,
		`last_updated_date`
		)
		VALUES (
		'".$question."',  '".$opt1."',  '".$opt2."',  '".$opt3."',  '".$opt4."',  '".$ans."',  '".$category."',  '0',  '0000-00-00 00:00:00',  '".date('Y-m-d h:i:s')."',  '0000-00-00 00:00:00'
		)"));*/
		
		$prefix = $this->db->prefix;
		
		$insid = $this->db->query( $this->db->prepare( 
		"
			INSERT INTO {$prefix}question_table
			( question, option1, option2, option3, option4, answer, category, has_been_published, publish_Date, create_date, last_updated_date)
			VALUES ( %s, %s, %s, %s, %s, %d, %d, %d, %s, %s, %s )
		", 
		array(
			$question, 
			$opt1, 
			$opt2,
			$opt3,
		        $opt4,
			$ans,
		        $category,
			'0',
			'0000-00-00 00:00:00',
			date('Y-m-d h:i:s'),
			'0000-00-00 00:00:00'
		) 
	) );
		
		
		
		$this->redirect();

	}
	
	function redirect()
	{
		
		unset($_POST);
		
		?>
		<script language="javascript">
		window.location='<?php echo home_url(); ?>/wp-admin/admin.php?page=edit_quiz';
		</script>
		<?php
	}
	
}


if(class_exists(Quiz_Questions))
{
	global $ques;
	$ques = new Quiz_Questions();
}

include_once('quiz_scripts.php');

?>

