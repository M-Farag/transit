<?php

namespace Concept\Transit;

class Transit {

    var $msg;
    public function __construct(string $msg)
    {
        $this->msg = $msg;
    }


    /**
     * Call
     * Perform parallel API calls using CURL Multi
     *
     * @param array $requests
     * @return array $results
     */
    public function call(array $requests) :array
    {

        $channels = array();

		$multi = curl_multi_init();
		foreach ( $requests as $request ) {

			$channel = curl_init();
			curl_setopt( $channel, CURLOPT_URL, "uri" );
			curl_setopt( $channel, CURLOPT_HTTPHEADER, array( "Authorization: Bearer token_if_needed" ) );
			curl_setopt( $channel, CURLOPT_RETURNTRANSFER, true );

			curl_multi_add_handle( $multi, $channel );

			$channels[ "request_id" ] = $channel;
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

}