<?php

namespace App\Libraries;

use Illuminate\Http\Request;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ThunderServiceCaller 
{
	public function __construct() 
	{
		$this->iptables 	= 	[
									'document.index'	=> 'http://128.199.145.173/documents',
									'document.store'	=> 'http://128.199.145.173/documents',
									'document.delete'	=> 'http://128.199.145.173/documents',
									'template.index'	=> 'http://128.199.145.173/templates',
									'template.store'	=> 'http://128.199.145.173/templates',
									'template.delete'	=> 'http://128.199.145.173/templates',
									'lock.index'		=> 'http://128.199.145.173/locks',
									'lock.store'		=> 'http://128.199.145.173/locks',
									'lock.delete'		=> 'http://128.199.145.173/locks',
									'user.index'		=> 'http://128.199.145.173/users',
								];
		$this->portables 	= 	[
									'document.index'	=> '9200',
									'document.store'	=> '9200',
									'document.delete'	=> '9200',
									'template.index'	=> '9200',
									'template.store'	=> '9200',
									'template.delete'	=> '9200',
									'lock.index'		=> '9201',
									'lock.store'		=> '9201',
									'lock.delete'		=> '9201',
									'user.index'		=> '9200',
								];
	}

	function transform_ip_table($topic)
	{
		$explode_topic 		= explode('.', $topic);

		unset($explode_topic[0]);

		return implode('.', $explode_topic);
	}

	/**
	 * fungsi untuk mq index caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $search
	 * @param  	\Illuminate\Http\Request $request
	 * @param   $topic
	 * @return 	array JSend
	 * 
	 */
	public function index_caller(array $search, Request $request, $topic) 
	{
		$iptable 		= $this->transform_ip_table($topic);

		$per_page 		= (!is_null($request->input('per_page')) ? $request->input('per_page') : 20);
		$page 			= (!is_null($request->input('page')) ? max(1, $request->input('page')) : 1);

		$search['skip']	= max(0, ($page - 1)) * $per_page;
		$search['take']	= $per_page;

		//url-ify the data for the POST
		$fields_string	= http_build_query($search);

		$url			= $this->iptables[$iptable].'?'.$fields_string;

		//open connection
		$header[]		= "Authorization: ".$request->input('thundertoken');

		$curl			= curl_init();

		curl_setopt_array($curl, array(
							  CURLOPT_PORT 				=> $this->portables[$iptable],
							  CURLOPT_URL 				=> $url,
							  CURLOPT_RETURNTRANSFER 	=> true,
							  CURLOPT_ENCODING 			=> "",
							  CURLOPT_MAXREDIRS 		=> 10,
							  CURLOPT_TIMEOUT 			=> 30,
							  CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST 	=> "GET",
							  CURLOPT_HTTPHEADER 		=> $header,
						));

		$result			= curl_exec($curl);
		
		curl_close($curl);

		$response 		= json_decode($result, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['page_info']	= ['total_data' => $response['data']['count'], 'pagination' => ['current_page' => $page, 'start_number' => (($page -1)* $per_page)+1, 'per_page' => $per_page]];

			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq edit caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $search
	 * @param  	\Illuminate\Http\Request $request
	 * @param   $topic
	 * @return 	array JSend
	 * 
	 */
	public function edit_caller(array $search, Request $request, $topic) 
	{
		$iptable 		= $this->transform_ip_table($topic);

		$per_page 		= (!is_null($request->input('per_page')) ? $request->input('per_page') : 20);
		$page 			= (!is_null($request->input('page')) ? max(1, $request->input('page')) : 1);

		$search['skip']	= max(0, ($page - 1)) * $per_page;
		$search['take']	= $per_page;

		//url-ify the data for the POST
		$fields_string	= http_build_query($search);

		$url			= $this->iptables[$iptable].'?'.$fields_string;

		//open connection
		$header[]		= "Authorization: ".$request->input('thundertoken');

		$curl			= curl_init();

		curl_setopt_array($curl, array(
							  CURLOPT_PORT 				=> $this->portables[$iptable],
							  CURLOPT_URL 				=> $url,
							  CURLOPT_RETURNTRANSFER 	=> true,
							  CURLOPT_ENCODING 			=> "",
							  CURLOPT_MAXREDIRS 		=> 10,
							  CURLOPT_TIMEOUT 			=> 30,
							  CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST 	=> "GET",
							  CURLOPT_HTTPHEADER 		=> $header,
						));

		$result			= curl_exec($curl);
		
		curl_close($curl);

		$response 		= json_decode($result, true);

		if(str_is($response['status'], 'success'))
		{
			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq store caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $param
	 * @param  	\Illuminate\Http\Request $request
	 * @param   $topic
	 * @return 	array JSend
	 * 
	 */
	public function store_caller(array $param, Request $request, $topic) 
	{
		$iptable 		= $this->transform_ip_table($topic);

		//url-ify the data for the POST
		$fields_string	= http_build_query($param);

		$url			= $this->iptables[$iptable];

		//open connection
		$header[]		= "Authorization: ".$request->input('thundertoken');

		$curl			= curl_init();

		curl_setopt_array($curl, array(
							  CURLOPT_PORT 				=> $this->portables[$iptable],
							  CURLOPT_URL 				=> $url,
							  CURLOPT_RETURNTRANSFER 	=> true,
							  CURLOPT_ENCODING 			=> "",
							  CURLOPT_MAXREDIRS 		=> 10,
							  CURLOPT_TIMEOUT 			=> 30,
							  CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST 	=> "POST",
							  CURLOPT_POSTFIELDS 		=> $fields_string,
							  CURLOPT_HTTPHEADER 		=> $header,
						));

		$result			= curl_exec($curl);
		
		curl_close($curl);

		$response 		= json_decode($result, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data'] 	= [$response['data']];
			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq delete caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $param
	 * @param  	\Illuminate\Http\Request $request
	 * @param   $topic
	 * @return 	array JSend
	 * 
	 */
	public function delete_caller(array $param, Request $request, $topic) 
	{
		$iptable 		= $this->transform_ip_table($topic);

		//url-ify the data for the POST
		$fields_string	= http_build_query($param);

		$url			= $this->iptables[$iptable];

		//open connection
		$header[]		= "Authorization: ".$request->input('thundertoken');

		$curl			= curl_init();

		curl_setopt_array($curl, array(
							  CURLOPT_PORT 				=> $this->portables[$iptable],
							  CURLOPT_URL 				=> $url,
							  CURLOPT_RETURNTRANSFER 	=> true,
							  CURLOPT_ENCODING 			=> "",
							  CURLOPT_MAXREDIRS 		=> 10,
							  CURLOPT_TIMEOUT 			=> 30,
							  CURLOPT_HTTP_VERSION 		=> CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST 	=> "DELETE",
							  CURLOPT_POSTFIELDS 		=> $fields_string,
							  CURLOPT_HTTPHEADER 		=> $header,
						));

		$result			= curl_exec($curl);
		
		$response 		= json_decode($result, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data'] 	= [$response['data']];
			unset($response['data']['count']);
		}

		return $response;
	}
};
