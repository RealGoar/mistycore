<?php

namespace Jacob\Orix\task\asynctask;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class DiscordMessage extends AsyncTask {
	
	/** @var String */
	protected string $webhook;
	
	/** @var String */
	protected string $username;
	
	/** @var String */
	protected string $message;
	
	/**
	 * DiscordMessage Constructor.
	 * @param String $webhook
	 * @param String $username
	 * @param String $message
	 */
	public function __construct(string $webhook, string $message, string $username){
		$this->webhook = $webhook;
		$this->message = $message;
		$this->username = $username;
	}
	
	/**
	 * @return void
	 */
	public function onRun() : void {
		$discord = curl_init();
		curl_setopt($discord, CURLOPT_URL, $this->webhook);
		curl_setopt($discord, CURLOPT_POSTFIELDS, json_encode(["content" => $this->message, "username" => $this->username]));
		curl_setopt($discord, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		curl_setopt($discord, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($discord, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($discord);
	}
}