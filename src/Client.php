<?php

namespace Payconiq;

use Payconiq\Support\Exceptions\CreateTransactionFailedException;
use Payconiq\Support\Exceptions\CreatePaymentFailedException;
use Payconiq\Support\Exceptions\GetPaymentDetailsFailedException;
use Payconiq\Support\Exceptions\RetrieveTransactionFailedException;

class Client
{

	protected $merchantId;
	protected $accessToken;
	protected $endpoint = 'https://api.payconiq.com/v3';

	/**
	 * Construct
	 *
	 * @param  string $merchantId  The merchant ID registered with Payconiq.
	 * @param  string $accessToken  Used to secure request between merchant backend and Payconiq backend.
	 * 
	 * @return void
	 */
	public function __construct($merchantId = null, $accessToken = null)
	{
		$this->merchantId = $merchantId;
		$this->accessToken = $accessToken;
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
	 * @param  string $merchantId  The merchant ID registered with Payconiq.
	 *
	 * @return self
	 */
	public function setMerchantId($merchantId)
	{
		$this->merchantId = $merchantId;

		return $this;
	}

	/**
	 * Set the access token
	 *
	 * @param  string $accessToken  Used to secure request between merchant backend and Payconiq backend.
	 *
	 * @return self
	 */
	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;

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
	 * 
	 * @deprecated Use createPayment instead
	 * @see createPayment
	 */
	public function createTransaction($amount, $currency, $callbackUrl)
	{
		$response = $this->curl('POST', $this->getEndpoint('/payments'), $this->constructHeaders(), [
			'amount' => $amount,
			'currency' => $currency,
			'callbackUrl' => $callbackUrl,
		]);

		if (empty($response['transactionId']))
			throw new CreateTransactionFailedException($response['message']);

		return $response['transactionId'];
	}

	/**
	 * Create a new payment
	 * 
	 * @param  float $amount  Payment amount in cents
	 * @param  string $currency  Payment currency code in IOS 4217 format
	 * @param  string $reference External payment reference used to reference the Payconiq payment in the calling party's system
	 * @param  string $callbackUrl  A url to which the merchant or partner will be notified of a payment
	 * 
	 * @return string  paymentId
	 * @throws CreatePaymentFailedException  If the response has no transactionid
	 */
	public function createPayment($amount, $currency = 'EUR', $reference, $callbackUrl)
	{
		$response = $this->curl('POST', $this->getEndpoint('/payments'), $this->constructHeaders(), [
			'amount' => $amount,
			'currency' => $currency,
			'reference' => $reference,
			'callbackUrl' => $callbackUrl,
		]);

		if (empty($response['paymentId']))
			throw new CreatePaymentFailedException($response['message']);

		return $response['paymentId'];
	}

	/**
	 * Retrieve an existing payment
	 *
	 * @param  string $transactionId  The transaction id provided by Payconiq
	 *
	 * @return  array  Response object by Payconiq
	 * 
	 * @deprecated Use getPaymentDetails instead
	 * @see getPaymentDetails
	 */
	public function retrieveTransaction($transactionId)
	{
		$response = $this->curl('GET', $this->getEndpoint('/payments/' . $transactionId), $this->constructHeaders());

		if (empty($response['paymentId']))
			throw new RetrieveTransactionFailedException($response['message']);

		return $response;
	}

	/**
	 * Get payment details of an existing payment
	 *
	 * @param  string $paymentId  The unique Payconiq identifier of a payment as provided by the create payment service
	 *
	 * @return  array  Response object by Payconiq
	 */
	public function getPaymentDetails($paymentId)
	{
		$response = $this->curl('GET', $this->getEndpoint('/payments/' . $paymentId), $this->constructHeaders());

		if (empty($response['paymentId']))
			throw new GetPaymentDetailsFailedException($response['message']);

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
			'Authorization: ' . $this->accessToken,
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
	private function cURL($method, $url, $headers = [], $parameters = [])
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
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

		return json_decode($body, true);
	}
}
