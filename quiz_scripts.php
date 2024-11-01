<!DOCTYPE html>
<html>
<head>
<style>
    #catgry{
	
	margin-top: 25px;
    }
    #catgry_list {
	margin-top: 30px;
    }
</style>
<style>
.col1 { background-color: #21759b;}
.col3 { background-color: #21759b;}

a { color:RED }
.tab {float: left;
      margin: 44px 5px 0px 0px;}
.tab tr th, td { line-height:2}
</style>

		
<script language="javascript">
function check(sel_one_id)
{
   
    var postn = sel_one_id.substr(-1,1);
    var sel_one = document.getElementById('opt'+postn).value;
   
    //document.getElementById('ans').value=sel_one;
    document.getElementById('ans').value=postn;
   
}
</script>


<script language="javascript">
function delet_cat(quiz_id)
{

var resp = confirm('Did you really mean to delete the category?');
if(resp==true)
{
    
    window.location = "admin.php?page=add_categories&qid="+quiz_id;
}
}
</script>

<script language="javascript">
function delet(quiz_id)
{

var resp = confirm('Did you really mean to delete the question?');
if(resp==true)
{
    
    window.location = "admin.php?page=edit_quiz&qid="+quiz_id;
}
}
</script>




	</head>
</html>