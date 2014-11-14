<?php
session_start();
error_reporting(E_ALL);
include_once("config.php");
include_once("debug_functions.php");
include_once("lib/GitHubAPI.class.php");

$github = new GitHubAPI();
if(!$github->checkToken())
{
	if(isset($_GET['code']))
	{
		if($github->sendTokenRequest($_GET['code']))
		{
			header("Location: index.php");
			exit;
		}
		else
		{
			echo $github->error;
		}
	}
	else
	{
		$github->sendAuthRequest('repo,gist');
	}
}
$commits = $github->getRepoCommits('joyent', 'node');
//pre($commits);
?>
<table border="0" cellpadding="5" cellspacing="0" width="80%" align="center">
<tr><th>Author</th><th>Message</th><th>Sha</th></tr>
<?php foreach($commits as $com): 
	if(isset($com->commit->backcolor))
	{
		$style="background-color:".$com->commit->backcolor;
	}
	else
	{
		$style="";
	}
?>
	<tr>
		<td style="<?php echo $style; ?>"><?php echo $com->commit->author->name; ?></td>
		<td style="<?php echo $style; ?>"><?php echo $com->commit->message; ?></td>
		<td style="<?php echo $style; ?>"><?php echo $com->sha; ?></td>
	</tr>
<?php endforeach; ?>
</table>
