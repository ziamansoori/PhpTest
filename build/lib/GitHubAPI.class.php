<?php
class GitHubAPI
{
	var $auth_url, $token_url, $basic_url, $client_id, $client_secret, $back_url, $error;
	public function __construct()
	{
		$this->auth_url = "https://github.com/login/oauth/authorize";
		$this->token_url = "https://github.com/login/oauth/access_token";
		$this->basic_url = "https://api.github.com/";
		$this->client_id = CLIENT_ID;
		$this->client_secret = CLIENT_SECRET;
		$this->back_url = BACK_URL;
	}
	
	function sendRequest($url, $post=false, $post_data="")
	{
		$http_header = array("Accept: application/vnd.github.v3+json", "User-Agent: Tessting");
		if($this->checkToken())
		{
			$http_header[] = "Authorization:token ".$this->getToken();
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
		curl_setopt ($ch, CURLOPT_CAINFO, CACERT);
		
		if($post)
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_POST,1);
		}
	
		$result = curl_exec($ch);
		return json_decode($result);
	}
	
	public function checkToken()
	{
		if(isset($_SESSION['access_token']))
			return true;
		else
			return false;
	}
	
	function getToken()
	{
		if(isset($_SESSION['access_token']))
			return $_SESSION['access_token'];
		else
			return false;
	}
	
	function setToken($access_token)
	{
		$_SESSION['access_token'] = $access_token;
	}
	
	public function sendAuthRequest($status)
	{
		$url = $this->auth_url."?client_id=".$this->client_id."&redirect_uri=".$this->back_url."&scope=".$status;
		header("Location: ".$url);
		exit;
	}
	
	public function sendTokenRequest($code)
	{
		$post_data = "client_id".$this->client_id."&client_secret=".$this->client_secret."&code=".$code;
		$res = $this->sendRequest($this->token_url, true, $post_data);
		if(isset($res['access_token']))
		{
			$this->setToken($res['access_token']);
			return true;
		}
		else
		{
			$this->error = $res['message'];
			return false;
		}
	}
	
	public function isBlue($sha)
	{
		if(preg_match("/[0-9]$/",$sha))
			return true;
		else
			return false;
	}
	
	public function getRepoCommits($owner, $repo)
	{
		if($this->checkToken())
		{
			$url = $this->basic_url."repos/".$owner."/".$repo."/commits";
			$result = $this->sendRequest($url);
			for($i=0; $i<count($result); $i++)
			{
				if($this->isBlue($result[$i]->sha))
				{
					$result[$i]->commit->backcolor = "#E6F1F6";
				}
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
}
?>