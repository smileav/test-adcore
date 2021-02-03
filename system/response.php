<?php
/**
* Response class
*/
class Response {
	private $headers = array();

	private $output;

	/**
	 * Constructor
	 *
	 * @param	string	$header
	 *
 	*/
	public function addHeader($header) {
		$this->headers[] = $header;
	}
	


	/**
	 * 
	 *
	 * @return	array
 	*/
	public function getOutput() {
		return $this->output;
	}
	
	/**
	 * 
	 *
	 * @param	string	$output
 	*/	
	public function setOutput($output) {
		$this->output = $output;
	}
	


	public function output() {
		if ($this->output) {

			
			if (!headers_sent()) {
				foreach ($this->headers as $header) {
					header($header, true);
				}
			}
			
			echo $this->output;
		}
	}
}
