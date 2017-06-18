<?php

namespace Payconiq;

use Payconiq\Support\Exceptions\CreateTransactionFailedException;
use Payconiq\Support\Exceptions\RetrieveTransactionFailedException;

class Client
{
	
	protected $merchant_id;
	protected $access_token;
	protected $endpoint = 'https://api.payconiq.com/v2';
	
	/**
	 * Construct
	 *
	 * @param  string $merchent_id  The merchant ID registered with Payconiq.
	 * @param  string $access_token  Used to secure request between merchant backend and Payconiq backend.
	 * 
	 * @return void
	 */
	public function __construct($merchant_id = null, $access_token = null)
	{
		$this->merchant_id = $merchant_id ?: config('payconiq.merchant_id');
		$this->access_token = $access_token ?: config('payconiq.access_token');
	}

	/**
	 * Set the endpoint
	 *
	 * @param  string $url  The endpoint of the Payconiq API.
	 *
	 * @return self
	 */
	public function setEndpoint($url)
	{
		$this->endpoint = $url;

		return $this;
	}

	/**
	 * Set the merchant id
	 *
	 * @param  string $merchent_id  The merchant ID registered with Payconiq.
	 *
	 * @return self
	 */
	public function setMerchantId($merchant_id)
	{
		$this->merchant_id = $merchant_id;

		return $this;
	}

	/**
	 * Set the access token
	 *
	 * @param  string $access_token  Used to secure request between merchant backend and Payconiq backend.
	 *
	 * @return self
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;

		return $this;
	}

	/**
	 * Create a new transaction
	 * 
	 * @param  float $amount  Transaction amount in cents
	 * @param  string $currency  Amount currency
	 * @param  string $callbackUrl  Callback where payconiq needs to send confirmation status
	 * 
	 * @return string  transaction_id
	 * @throws CreateTransactionFailedException  If the response has no transactionid
	 */
	public function createTransaction($amount, $currency, $callbackUrl)
	{
		$response = $this->curl('POST', $this->getEndpoint('/transactions'), $this->constructHeaders(), [
			'amount' => $amount,
			'currency' => $currency,
			'callbackUrl' => $callbackUrl,
		]);

		if(empty($response['transactionId']))
			throw new CreateTransactionFailedException($response['message']);

		return $response['transactionId'];
	}

	/**
	 * Retrieve an existing transaction
	 *
	 * @param  string $transaction_id  The transaction id provided by Payconiq
	 *
	 * @return  array  Response object by Payconiq
	 */
	public function retrieveTransaction($transaction_id)
	{
		$response = $this->curl('GET', $this->getEndpoint('/transactions/'.$transaction_id), $this->constructHeaders());

		if(empty($response['_id']))
			throw new RetrieveTransactionFailedException($response['message']);

		return $response;
	}

	/**
	 * Get the endpoint for the call
	 *
	 * @param  string $route
	 */
	private function getEndpoint($route = null)
	{
		return $this->endpoint . $route;
	}

	/**
	 * Construct the headers for the cURL call
	 * 
	 * @return array
	 */
	private function constructHeaders()
	{
		return [
			'Content-Type: application/json',
			'Authorization: '.$this->access_token,
		];
	}
	
	/**
    * cURL request
    *
    * @param  string $method
    * @param  string $url
    * @param  array $headers
    * @param  array $parameters
    *
    * @return response
    */
    private function cURL($method, $url, $headers=[], $parameters=[])
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,20);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($parameters));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        curl_close($curl);

        return json_decode($body,true);
    }

}