<?php
/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'testcia',
 *		accessLevel = 'all',
 *		description = 'Relay commit messages into IRC channel',
 *		help        = 'cia.txt'
 *	)
 */
class CIAController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $ircRelayController;
	
	/** @Inject */
	public $socketManager;
	
	/** @Logger */
	public $logger;
	
	private $apisocket = null;

	/** @Setup */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("testcia")
	 * @Matches("/^testcia (.+)$/i")
	 */
	public function testCIACommand($message, $channel, $sender, $sendto, $args) {
		$input = $args[1];
		
		$curl = new MyCurl("http://127.0.0.1:9200");
		$curl->setPost($input);
		$curl->createCurl();
		$contents = $curl->__toString();
		$sendto->reply("Test sent.");
	}

	/**
	 * @Event("connect")
	 * @Description("Start to listen for incoming commit notifications")
	 * @DefaultStatus("0")
	 */
	public function openApiSocket() {
		// bind to any address
		$address = '0.0.0.0';

		$port = 9200;

		// Create a TCP Stream socket
		$this->apisocket = stream_socket_server("tcp://$address:$port", $errno, $errstr);
		if ($this->apisocket) {
			$this->logger->log('DEBUG', 'CIA socket bound successfully');
			stream_set_blocking($this->apisocket, 0);
			
			$socketNotifier = new SocketNotifier($this->apisocket, SocketNotifier::ACTIVITY_READ, array($this, 'processIncomingCommit'));
			$this->socketManager->addSocketNotifier($socketNotifier);
		} else {
			$this->logger->log('ERROR', "$errstr ($errno)");
		}
	}
	
	public function processIncomingCommit($type) {
		/* Accept incoming requests and handle them as child processes */
		$client = @stream_socket_accept($this->apisocket);
		if ($client !== false) {
			while (!feof($client)) {
				$data .= fread($client, 8192);
			}
		}
		$obj = $this->http_parse_headers($data);
		print_r($obj);
		//$this->ircRelayController->sendMessageToIRC($data);
	}
	
	// taken from (with modifcations): http://php.net/manual/en/function.http-parse-headers.php
	public function http_parse_headers($header) {
		list($params, $payload) = explode("\r\n\r\n", $header, 2);
		
		$retVal = array();
		$retVal['Payload'] = $payload;
        
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $params));
        forEach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if (isset($retVal[$match[1]])) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }
}

