<?php
/*
Plugin Name: Quiz
Plugin URI: itobuz.com/c/plugin/quiz.zip
Description: A simple plugin for displaying category(if any) based questions along with it's options i.e. MCQ
Author: Subina
Version: 1.0
*/

class Quiz {
	
	 // Constructor
        function __construct()
        {
            $this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

            // Activate for New Installs
            register_activation_hook( $this->plugin_name, array( &$this, 'activate' ) );
	    
			// Add the admin menu
            add_action( 'admin_menu', array( &$this, 'add_menu' ) );

        }
		
	function activate()
        {
            $this->create_quiz_table();
			$this->create_category_table();
			
        }
		 
	function direct_route()
	{
	    switch($_GET['page']) {
	    case 'quiz' :
				    include_once(dirname(__FILE__).'/quiz_new.php');
				    break;
	    case 'edit_quiz' : 
				    include_once(dirname(__FILE__).'/edit_quiz.php');
				    break;


	    case 'add_categories' : 
				    include_once(dirname(__FILE__).'/add_categories.php');
				    break;
	    }
	}
	
	  // Add the admin menu
	function add_menu() {
	    // Accessible to Authors, Editors, and Admins
	    add_menu_page( 'Quiz', 'Quiz', 'publish_posts', 'quiz',  array( &$this, 'direct_route' ) );
	    add_submenu_page( 'quiz', 'Edit Quiz', 'Edit Quiz', 'publish_pages', 'edit_quiz', array( &$this, 'direct_route') );
	    add_submenu_page ( 'quiz', 'Add Categories', 'Add Categories', 'publish_pages', 'add_categories', array( &$this, 'direct_route') );

		    } 
	 
	
	function create_quiz_table()
	{
	
		global $wpdb;
		$dbname = $wpdb->prefix."question_table";
		$dbname1 = $wpdb->prefix."user_question_answers";
		$create_sql = "CREATE TABLE IF NOT EXISTS $dbname(
		quiz_id INT NOT NULL AUTO_INCREMENT,
		question VARCHAR(500) NOT NULL,
		option1 varchar(500) NOT NULL,
		option2 varchar(500) NOT NULL,
		option3 varchar(500) NOT NULL,
		option4 varchar(500) NOT NULL,
		answer VARCHAR(500) NOT NULL,
		category INT NOT NULL,
		has_been_published tinyint(1) NOT NULL DEFAULT '0',
		publish_Date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		create_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		last_updated_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (quiz_id ))";
		
		$wpdb->query($create_sql);
		
		$create_sql1 = "CREATE TABLE IF NOT EXISTS $dbname1 (
		`qid` INT NOT NULL AUTO_INCREMENT ,
		`user_id` VARCHAR( 225 ) NOT NULL ,
		`ques_id` INT NOT NULL ,
		`ans` VARCHAR( 225 ) NOT NULL ,
		`date_time` DATETIME NOT NULL ,
		`ip` VARCHAR( 225 ) NOT NULL ,
		PRIMARY KEY (  `qid` )
		) ENGINE = INNODB";
		
		$wpdb->query($create_sql1);
	}
	
	function create_category_table()
	{
	    global $wpdb;
	    $dbname = $wpdb->prefix."question_category_table";
	    $create_sql = "CREATE TABLE IF NOT EXISTS $dbname(
		    quiz_category_id INT NOT NULL AUTO_INCREMENT,
		    quiz_category VARCHAR(500) NOT NULL,
		    quiz_category_questions_count INT NOT NULL,
		    PRIMARY KEY (quiz_category_id ))";

		    $wpdb->query($create_sql);
	}
	
	
	
	
}

		
    if ( class_exists( 'Quiz' ) ) {
    global $Quiz;
    $Quiz = new Quiz();
}


add_shortcode('my_quiz', 'func_get_quiz');
function func_get_quiz($atts)
{
    if($atts)
    $cats = explode(',',$atts['category']);
    
    global $wpdb;
    if(isset($_POST['submit']))
    {
	$on_submit = 1;
	
	$loop_count = $_POST['loop_count'];
	
	for($start=1,$l=1;$start<$loop_count;$start++,$l++)
	{
	    
	    $questn.$l = $_POST['qstn_num'.$start];
	    
	    $ans.$l = $_POST['answr'.$start];

	    $questn = $_POST['qstn_num'.$start];
	    $questn_nos[] = $_POST['qstn_num'.$start];
	    $prefix = $wpdb->prefix;
	    
	    $remote_addr = $_SERVER['REMOTE_ADDR'];
	   
	    $insid = $wpdb->query( $wpdb->prepare( 
	    "
		INSERT INTO {$prefix}user_question_answers
		( user_id, ques_id, ans, date_time, ip )
		VALUES ( %d, %d, %d, %s, %s )
	    ", 
	    array(
		    get_current_user_id( ), 
		    $questn, 
		    $ans.$l,
		    date('Y-m-d h:i:s'),
		    $remote_addr
	    ) 
	    ) );
	    
	    
	}
	//print_r($questn_nos);
	$i = $num = $j = 1;
    ############ my testing ################################
    echo "<div>Here are the answers :- </div>";
    echo "<div>
	<form name='form1' id='form1' method='post' action='' >
	<table width='' border='1'>";
	echo "<colgroup>
	<col class='col1' style='width:5%'>
	<col class='col2'>
	<col class='col3'>
	<col class='col4'>
	<col class='col5'>
	</colgroup>";
    for($cnt_start=0;$cnt_start<count($questn_nos);$cnt_start++)
    {
	$qstn_no = $questn_nos[$cnt_start];
	
	$prefix = $wpdb->prefix;
	$quiz_ans = $wpdb->get_results( $wpdb->prepare( 
	    "
	    select * from {$prefix}question_table
	    where quiz_id= %d
	    ", 
	    $qstn_no

	    ) );
	    
	
	
	foreach($quiz_ans as $quiz_ques_ans) {
	
    echo "<tr>
	 <td>";
    echo "<strong>".$i++."</strong>";
    echo "</td>
	<td colspan='3'>
	$quiz_ques_ans->question
	</td>
	</tr>";

    echo "<tr>
	<td>&nbsp;</td>
	<td>";
    $k = "1#$j";
    echo "<span style='margin-right:14px'>1.</span><label id='optn$k'>$quiz_ques_ans->option1</label>
	</td>";
    
   
   $prefix = $wpdb->prefix;
   $this_ques_ans_count = $wpdb->get_results( $wpdb->prepare( 
	    "
	    select count(qid) as this_ques_num_ans from {$prefix}user_question_answers
	    where ques_id= %d
	    ", 
	    $quiz_ques_ans->quiz_id

	    ) );

   $temp_this_ques_ans_count = json_encode($this_ques_ans_count);
   $arr_this_ques_ans_count = json_decode($temp_this_ques_ans_count,true);
   $arr_this_ques_ans_count = $arr_this_ques_ans_count[0];
 
    $percnt = (this_choice_ans($quiz_ques_ans->quiz_id,1)/$arr_this_ques_ans_count['this_ques_num_ans'])*100;
//    echo "upare ".this_choice_ans($quiz_ques_ans->quiz_id,1)."<br>";
//    echo "niche ".$arr_this_ques_ans_count['this_ques_num_ans']."<br>";
//    echo "percent ".$percnt."<br>";
    echo "<td>";
    echo "<meter value=".round($percnt)." min='0' max='100' low='0' high='100' optimum='47' style='width:250px' >".$arr['num_ans']." of 100</meter>"."<span style='padding-left:5px'>". round($percnt)."%</span>";
    echo "</td>   
	    </tr>";
    $k = "2#$j";
   echo "<tr>
	<td>&nbsp;</td>
	<td><span style='margin-right:14px'>2.</span><label id='optn$k'>$quiz_ques_ans->option2</label>
	</td>";
   
       
   $percnt = (this_choice_ans($quiz_ques_ans->quiz_id,2)/$arr_this_ques_ans_count['this_ques_num_ans'])*100;
   echo "<td>";
   echo "<meter value=".round($percnt)." min='0' max='100' low='0' high='100' optimum='47' style='width:250px' >".$arr['num_ans']." of 100</meter>"."<span style='padding-left:5px'>". round($percnt)."%</span>";
   echo	"</td>   
	</tr>";
   $k = "3#$j";
   echo	"<tr>
	<td>&nbsp;</td>
	<td><span style='margin-right:14px'>3.</span><label id='optn$k'>$quiz_ques_ans->option3</label>
	</td>
	<td>";

   
   $percnt = (this_choice_ans($quiz_ques_ans->quiz_id,3)/$arr_this_ques_ans_count['this_ques_num_ans'])*100;
   
   echo "<meter value=".round($percnt)." min='0' max='100' low='0' high='100' optimum='47' style='width:250px' >".$arr['num_ans']." of 100</meter>"."<span style='padding-left:5px'>". round($percnt)."%</span>";
   echo	"</td>
	</tr>";
    $k = "4#$j";
  echo	"<tr>
	<td>&nbsp;</td>
	<td><span style='margin-right:14px'>4.</span><label id='optn$k'>$quiz_ques_ans->option4</label>
	</td>
	<td>";
  
    
   $percnt = (this_choice_ans($quiz_ques_ans->quiz_id,4)/$arr_this_ques_ans_count['this_ques_num_ans'])*100;
  
  echo "<meter value=".round($percnt)." min='0' max='100' low='0' high='100' optimum='47' style='width:250px' >".$arr['num_ans']." of 100</meter>"."<span style='padding-left:5px'>". round($percnt)."%</span>";
  echo	"</td>  
	</tr>";
	
    echo "<tr>
	<td>Ans.</td>
	<td colspan='5'>";
    $answr_numb = "answr".$num++;
    echo "<input type='text' name=$answr_numb id=$answr_numb value='$quiz_ques_ans->answer' style='width:153px' />
	  
	</td>
	</tr>";
	$j++;
	}
     
   }
    
    echo "</table></form>
</div>";
   
    
    
    ################ EO MY TESTING #########################
    }
    
    
    if(!$on_submit)
    {
	
    $i = $num = $j = 1;
    echo "<div>Here are your questions :- </div>";
    echo "<div>
	<form name='form' id='form' method='post' action='' >
	<table width='' border='1'>";
	echo "<colgroup>
	<col class='col1' style='width:5%'>
	<col class='col2'>
	<col class='col3'>
	<col class='col4'>
	<col class='col5'>
	</colgroup>";
    if(count($cats))
    {
	$loop_var = count($cats);
    }
    else
    {
	$loop_var = 1;
    }
    for($cat_count=0;$cat_count<$loop_var;$cat_count++) {
    if(count($cats))
    {
    //$quiz_questns = $wpdb->get_results("select * from wp_question_table where category='".get_cat_num($cats[$cat_count])."'");
    
    $quiz_questns = $wpdb->get_results( $wpdb->prepare( 
	    "
	    select * from wp_question_table
	    where category= %d
	    ", 
	    get_cat_num($cats[$cat_count])

	    ) );
	    
    
    
    }
    else
    {
	
    $quiz_questns = $wpdb->get_results("select * from wp_question_table");
    }
    
    
     foreach($quiz_questns as $quiz_questn) {
	 
    echo "<tr>
	 <td>";
    echo "<strong>".$i++."</strong><input type='hidden' name='qstn_num$j' id='qstn_num$j' value='".$quiz_questn->quiz_id."' /><span>.</span> ";
    echo "</td>
	<td colspan='3'>".
	$quiz_questn->question
	   
	."</td>
	</tr>";

    echo "<tr>
	<td>&nbsp;</td>
	<td width='40%'>";
    $k = "1#$j";
    echo "<input type='radio' name='select_option$j' id='right_opt$k' onclick='select_this(\"$k\")' /><label id='optn$k'>$quiz_questn->option1</label>
	</td>";
	
    echo "</tr>";
    
    $k = "2#$j";
   echo "<tr>
	<td>&nbsp;</td>
	<td><input type='radio' name='select_option$j' id='right_opt$k' onclick='select_this(\"$k\")' /><label id='optn$k'>$quiz_questn->option2</label>
	</td>";
  
    echo "</tr>";
   $k = "3#$j";
   echo	"<tr>
	<td>&nbsp;</td>
	<td><input type='radio' name='select_option$j' id='right_opt$k' onclick='select_this(\"$k\")' /><label id='optn$k'>$quiz_questn->option3</label>
	</td>";
  
   echo "</tr>";
    $k = "4#$j";
  echo	"<tr>
	<td>&nbsp;</td>
	<td><input type='radio' name='select_option$j' id='right_opt$k' onclick='select_this(\"$k\")' /><label id='optn$k'>$quiz_questn->option4</label>
	</td>";
  
  echo "</tr>";
    echo "<tr>
	<td>Ans.</td>
	<td colspan='5'>";
    $answr_numb = "answr".$num++;
    echo "<input type='text' name=$answr_numb id=$answr_numb />
	</td>
	</tr>";
	$j++;
	
     }
     echo "<input type='hidden' name='loop_count' id='loop_count' value='".$j."' />";
     
	}
    echo "<tr><td colspan='5' align='center'><input type='submit' name='submit' id='submit' /></td></tr>";
    echo "</table></form>
</div>";
}

selct();
}
function get_cat_num($cat_name)
{

    global $wpdb;
    //$cat_id_det = $wpdb->get_results("select quiz_category_id from wp_question_category_table where quiz_category='".$cat_name."'");
    $prefix = $wpdb->prefix;
    $cat_id_det = $wpdb->get_results( $wpdb->prepare( 
	    "
	    select quiz_category_id from {$prefix}question_category_table
	    where quiz_category= %d
	    ", 
	    $cat_name

	    ) );
    
    $temp = json_encode($cat_id_det);
    $arr = json_decode($temp,true);
    $arr = $arr[0];
    return $arr['quiz_category_id'];

}


function this_choice_ans($questn_id,$ans_num)
{
    
   global $wpdb; 
  
   $prefix = $wpdb->prefix;
   $ans_count = $wpdb->get_results( $wpdb->prepare( 
	    "
	    select count(qid) as num_ans from {$prefix}user_question_answers
	    where ques_id= %d and ans= %d
	    ", 
	    $questn_id,
	    $ans_num		

	    ) );

//	    print "here<pre>";
//	    print_r($ans_count);
   $temp = json_encode($ans_count);
   $arr = json_decode($temp,true);
   
   $arr = $arr[0];
 
   return $arr['num_ans'];
}


function selct()
{

?>

<script language="javascript">
    
    function select_this(obj)
    {
	var hash_pos = obj.indexOf("#");
	
	var ans_id = obj.substr(hash_pos+1);
	
	var numb = obj.split("#");
	
	document.getElementById('answr'+ans_id).value = numb[0];
    }
</script>

<?php } ?>