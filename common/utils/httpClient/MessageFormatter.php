<?php

namespace common\utils\httpClient;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MessageFormatter extends \GuzzleHttp\MessageFormatter
{
    /**
     * Apache Common Log Format.
     * @link http://httpd.apache.org/docs/2.4/logs.html#common
     * @var string
     */
    const CLF = "{hostname} {req_header_User-Agent} - [{date_common_log}] \"{method} {target} HTTP/{version}\" {code} {res_header_Content-Length}";
    const DEBUG = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{error}";
    const SHORT = '[{ts}] "{method} {target} HTTP/{version}" {code}';

    protected $exclude_req_body_params = ['password', 'token', 'validation'];

    /** @var string Template used to format log messages */
    private $template;

    /**
     * @param string $template Log message template
     */
    public function __construct($template = self::CLF)
    {
        $this->template = $template ?: self::CLF;
        parent::__construct($template);
    }

    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface $request Request that was sent
     * @param ResponseInterface $response Response that was received
     * @param \Exception $error Exception that was received
     *
     * @return string
     */
    public function format(
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $error = null
    ) {
        $cache = [];

        return preg_replace_callback(
            '/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
            function (array $matches) use ($request, $response, $error, &$cache) {
                if (isset($cache[$matches[1]])) {
                    return $cache[$matches[1]];
                }

                $result = '';
                switch ($matches[1]) {
                    case 'request':
                        $result = \GuzzleHttp\Psr7\str($request);
                        break;
                    case 'response':
                        $result = $response ? \GuzzleHttp\Psr7\str($response) : '';
                        break;
                    case 'req_headers':
                        $result = trim($request->getMethod()
                                . ' ' . $request->getRequestTarget())
                            . ' HTTP/' . $request->getProtocolVersion() . "\r\n"
                            . $this->headers($request);
                        break;
                    case 'res_headers':
                        $result = $response ?
                            sprintf(
                                'HTTP/%s %d %s',
                                $response->getProtocolVersion(),
                                $response->getStatusCode(),
                                $response->getReasonPhrase()
                            ) . "\r\n" . $this->headers($response)
                            : 'NULL';
                        break;
                    case 'req_body':
                        $result = json_decode($request->getBody(), true);
                        foreach ($this->exclude_req_body_params as $param) {
                            if (isset($result[$param])) {
                                unset($result[$param]);
                            }
                        }
                        $result = json_encode($result);
                        break;
                    case 'res_body':
                        $result = $response ? $response->getBody() : 'NULL';
                        break;
                    case 'ts':
                    case 'date_iso_8601':
                        $result = gmdate('c');
                        break;
                    case 'date_common_log':
                        $result = date('d/M/Y:H:i:s O');
                        break;
                    case 'method':
                        $result = $request->getMethod();
                        break;
                    case 'version':
                        $result = $request->getProtocolVersion();
                        break;
                    case 'uri':
                    case 'url':
                        $result = $request->getUri();
                        break;
                    case 'target':
                        $result = $request->getRequestTarget();
                        break;
                    case 'req_version':
                        $result = $request->getProtocolVersion();
                        break;
                    case 'res_version':
                        $result = $response
                            ? $response->getProtocolVersion()
                            : 'NULL';
                        break;
                    case 'host':
                        $result = $request->getHeaderLine('Host');
                        break;
                    case 'hostname':
                        $result = gethostname();
                        break;
                    case 'code':
                        $result = $response ? $response->getStatusCode() : 'NULL';
                        break;
                    case 'phrase':
                        $result = $response ? $response->getReasonPhrase() : 'NULL';
                        break;
                    case 'error':
                        $result = $error ? $error->getMessage() : 'NULL';
                        break;
                    default:
                        // handle prefixed dynamic headers
                        if (strpos($matches[1], 'req_header_') === 0) {
                            $result = $request->getHeaderLine(substr($matches[1], 11));
                        } elseif (strpos($matches[1], 'res_header_') === 0) {
                            $result = $response
                                ? $response->getHeaderLine(substr($matches[1], 11))
                                : 'NULL';
                        }
                }

                $cache[$matches[1]] = $result;
                return $result;
            },
            $this->template
        );
    }

    private function headers(MessageInterface $message)
    {
        $result = '';
        foreach ($message->getHeaders() as $name => $values) {
            $result .= $name . ': ' . implode(', ', $values) . "\r\n";
        }

        return trim($result);
    }
}
