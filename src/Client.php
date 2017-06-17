<?php

namespace Payconiq;

class Client
{
	
	protected $merchant_id;
	protected $access_token;
	protected $endpoint = [
		'live' => 'https://api.payconiq.com/v2/transactions',
		'test' => 'https://dev.payconiq.com/v2/transactions'
	];
	
	/**
	 * Construct
	 *
	 * @param  string $merchent_id  The merchant ID registered with Payconiq.
	 * @param  string $access_token  Used to secure request between merchant backend and Payconiq backend.
	 * 
	 * @return void
	 */
	public function __construct($merchant_id=null, $access_token=null)
	{
		$this->merchant_id = $merchant_id;
		$this->access_token = $access_token;
	}	

	/**
	 * Set the merchant id
	 *
	 * @param  string $merchent_id  The merchant ID registered with Payconiq.
	 *
	 * @return void
	 */
	public function setMerchantId($merchant_id)
	{
		$this->merchant_id = $merchant_id;
	}

	/**
	 * Set the access token
	 *
	 * @param  string $access_token  Used to secure request between merchant backend and Payconiq backend.
	 *
	 * @return void
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	/**
	 * Create a new transaction
	 * 
	 * @param  string $amount  Transaction amount in cents
	 * @param  string $currency  Amount currency
	 * @param  string $callbackURL  Callback where payconiq needs to send confirmation status
	 * 
	 * @return string transactionid
	 */
	public function createTransaction($amount, $currency, $callbackURL)
	{

	}

	/**
	 * Webhook
	 *
	 * TIMEDOUT: If a user for some reason didn't pay after 2 minutes.
	 * CANCELED: user can just cancel a transaction after scanning it.
	 * FAILED: something went wrong during the payment process (wrong PIN provided by a user).
	 * SUCCEEDED: A transaction was confirmed by the user
	 * 
	 * @return json
	 */
	public function webhook()
	{

	}

	/**
	 * Validate the response
	 */
	private function validateResponse($code)
	{
		// 200, 4OO, 500
	}

	/**
    * cURL request
    */
    private function cURL($type,$method,$headers,$params=[])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $method);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,20);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($type));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->bindPostFields($params));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // Service unavailable
        $code = curl_getinfo($curl,CURLINFO_HTTP_CODE) > 0 ? curl_getinfo($curl,CURLINFO_HTTP_CODE) : 503;
        $body = substr($response, $header_size);
        curl_close($curl);

        return [
            'code' => $code,
            'body' => $this->decryptData($body),
        ];
    }

}