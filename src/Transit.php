<?php

namespace Concept\Transit;

class Transit {

    public function __construct()
    {
    }


    /**
     * Call
     * Perform parallel API calls using CURL Multi
     *
     * TODO
     * - Requests validations
     * - Enhance processing the requests
     * - Add documentation
     * - Add get results data
     * 
     * @param array $requests
     * @return array $results
     */
    public function call(array $requests) :array
    {
		//Validation
		$validated_requests = $this->validated_requests($requests);

        $channels = array();

		$multi = curl_multi_init();
		foreach ( $validated_requests as $request ) {

			$channel = curl_init();
			curl_setopt( $channel, CURLOPT_URL, $request['uri'] );
			if (isset($request['api_key']))
			{
				curl_setopt( $channel, CURLOPT_HTTPHEADER, array( "Authorization: Bearer {$request['api_key']}" ) );
			}
			curl_setopt( $channel, CURLOPT_RETURNTRANSFER, true );

			if( isset($request['method']) && isset($request['body']) && $request['method'] == 'POST')
			{
				curl_setopt($channel, CURLOPT_POST, true);
				curl_setopt($channel, CURLOPT_POSTFIELDS, $request['body']);
			}

			curl_multi_add_handle( $multi, $channel );

			$channels[] = $channel;
		}

			/**
			 * Do Parallel API calls using CURL_MULTI
			 */
			$active = null;
		do {
			$mrc = curl_multi_exec( $multi, $active );
		} while ( $mrc == CURLM_CALL_MULTI_PERFORM );

		while ( $active && $mrc == CURLM_OK ) {
			if ( curl_multi_select( $multi ) == -1 ) {
				continue;
			}

			do {
				$mrc = curl_multi_exec( $multi, $active );
			} while ( $mrc == CURLM_CALL_MULTI_PERFORM );
		}

		foreach ( $channels as $channel ) {
			// Getting the results of each request
			$raw_message = curl_multi_getcontent( $channel );
			// Processing the API call results and storing it to the $data list
			$data[] =  json_decode( $raw_message, true );
			curl_multi_remove_handle( $multi, $channel );
		}

		curl_multi_close( $multi );

        return $data;
    }

	/**
	 * Validate that the requests array contains the main request components
	 * 
	 * Required params
	 * - uri: The destination uri that you want to send the api request to
	 * - method: The type of the http method (get,post,put,delete)
	 * - headers: The requests headers array
	 * 
	 * Optional params
	 * - body: The body array
	 *
	 * @param array $requests
	 * @return array $validated
	 */
	private function validated_requests(array $requests):array
	{
		$validated = [];
		foreach($requests as $request) {
			// Check keys
			if(! array_key_exists('uri',$request)){
				continue;				
			}

			if( ! array_key_exists('method',$request)){
				continue;
			}

			$validated[] = $request;
		}

		return $validated;
	}

}