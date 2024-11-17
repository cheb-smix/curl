<?php

namespace chebsmix\helpers\curl;

class Curl
{
    const URLENCCTYPE = "application/x-www-form-urlencoded";
    const JSONCTYPE = "application/json";

    private $curl;
    private $url;
    private $requestBody = [];
    private $requestParams = [];
    private $contentType;
    private $headers = [];
    private $method = "GET";
    private $availableMethods = ["GET", "POST", "PATCH", "PUT", "DELETE", "JSON"];
    private $responseType = "json";
    private $response;
    private $responseInfo;
    private $statusCode;
    private $verboseData;
    private $debugMode = false;

    private $initFlag = false;
    
    public function __construct(string $method = "GET")
    {
        $this->setMethod($method);
        $this->requestParams[CURLOPT_RETURNTRANSFER] = true;
    }

    public function setUrl(string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    public function setRequestBody(array $body) : self
    {
        $this->requestBody = $body;
        return $this;
    }

    public function setRequestParams(array $params) : self
    {
        $this->requestParams = array_merge($this->requestParams, $params);
        return $this;
    }

    public function setMethod(string $method) : self
    {
        if (in_array($method, $this->availableMethods)) {
            $this->method = $method;
        }

        return $this;
    }

    public function setContentType(string $contentType) : self
    {
        if (in_array($contentType, [self::URLENCCTYPE, self::JSONCTYPE])) {
            $this->contentType = $contentType;
        }

        return $this;
    }

    public function setHeaders(array $headers) : self
    {
        $this->headers = array_merge($this->headers, $headers);

        if (!$this->contentType) {
            foreach ($headers as $key => $header) {
                if (($key == "Content-Type" && $header == self::JSONCTYPE) || $header == "Content-Type: " . self::JSONCTYPE) {
                    $this->setContentType(self::JSONCTYPE);
                } elseif (($key == "Content-Type" && $header == self::URLENCCTYPE) || $header == "Content-Type: " . self::URLENCCTYPE) {
                    $this->setContentType(self::URLENCCTYPE);
                }
            }
        }

        return $this;
    }

    public function setResponseType(string $type) : self
    {
        if (in_array($type, ["text", "json"])) {
            $this->responseType = $type;
        }
        return $this;
    }

    public function init() : self
    {
        if ($this->initFlag) {
            return $this;
        }

        if ($this->debugMode) {
            ob_start();
            $out = fopen('php://output', 'w');
            $this->requestParams[CURLOPT_VERBOSE] = true;
            $this->requestParams[CURLOPT_STDERR] = $out;
        }

        $this->curl = curl_init();
        $this->initFlag = true;

        if ($this->method == "JSON") {
            if (!$this->contentType) {
                $this->contentType = self::JSONCTYPE;
            }
            $this->method = "POST";
        }

        if ($this->method == "GET") {
            $this->contentType = null;
        } elseif ($this->method == "DELETE") {
            $this->contentType = null;
            $this->requestParams[CURLOPT_CUSTOMREQUEST] = $this->method;
        } else {
            if ($this->requestBody) {
                if ($this->contentType == self::JSONCTYPE) {
                    $this->requestParams[CURLOPT_POSTFIELDS] = json_encode($this->requestBody);
                } else {
                    $this->requestParams[CURLOPT_POSTFIELDS] = http_build_query($this->requestBody);
                    if ($this->contentType == self::URLENCCTYPE) {
                        $this->requestParams[CURLOPT_POSTFIELDS] = urlencode($this->requestParams[CURLOPT_POSTFIELDS]);
                    }
                }
            }

            if ($this->method == "POST") {
                $this->requestParams[CURLOPT_POST] = true;
            } else {
                $this->requestParams[CURLOPT_CUSTOMREQUEST] = $this->method;
            }
        }

        // if ($this->contentType && !isset($this->headers["Content-Type"])) {
        //     $this->headers["Content-Type"] = $this->contentType;
        // }

        curl_setopt($this->curl, CURLOPT_URL, $this->url);

        foreach ($this->requestParams as $key => $value) {
            curl_setopt($this->curl, $key, $value);
        }

        if ($this->headers) {
            foreach ($this->headers as $key => &$value) {
                if (!is_numeric($key)) {
                    $value = "$key: $value";
                }
            }

            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        $this->response = curl_exec($this->curl);
        $this->responseInfo = curl_getinfo($this->curl);
        $this->statusCode = $this->responseInfo["http_code"];

        curl_close($this->curl);

        if ($this->debugMode) {
            fclose($out);
            $this->verboseData = ob_get_clean();
        }

        if ($this->responseType == "json" && !isset($this->requestParams[CURLOPT_HEADER])) {
            try {
                $this->response = json_decode($this->response, true);
            } catch (\Exception $e) {

            }
        }

        return $this;
    }

    public function getResponse()
    {
        return $this->init()->response;
    }

    public function getHeaders()
    {
        $this->requestParams[CURLOPT_HEADER] = true;
        $this->requestParams[CURLOPT_NOBODY] = true;
        return substr($this->getResponse(), 0, $this->responseInfo["header_size"]);
    }

    public function buildCli()
    {
        $this->init();
        $cli = 'curl --location --request ';
        if (isset($this->requestParams[CURLOPT_NOBODY])) {
            $cli .= '--head ';
        } else {
            $cli .= $this->method . ' ';
        }
        
        $cli .= '\'' . $this->url . '\' ';

        foreach ($this->headers as $header) {
            $cli .= '--header \'' . $header . '\' ';
        }
        if ($this->requestBody) {
            if ($this->contentType == self::JSONCTYPE) {
                $cli .= '--data \'' . json_encode($this->requestBody) . '\' ';
            } else {
                foreach ($this->requestBody as $key => $value) {
                    if ($this->contentType == self::URLENCCTYPE) {
                        $cli .= '--data-urlencode \'' . $key . '=' . urlencode($value) . '\' ';
                    } else {
                        $cli .= '--form \'' . $key . '="' . $value . '"\' ';
                    }
                }
            }
        }
        return $cli;
    }

    public function parseCli(string $cli) : self
    {
        $flags = [
            "--head",
            "--request",
            "--location",
            "--header",
            "--data",
            "--data-urlencode",
            "--form",
        ];

        $parsedCli = [];

        foreach ($flags as $key) {
            if (preg_match_all("/$key '?([^']+)'?/", $cli, $matches)) {
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $parsedCli[] = [$key, trim($matches[1][$i])];
                }
                $cli = str_replace($matches[0], '', $cli);
            }
        }

        foreach ($parsedCli as $param) {
            switch ($param[0]) {
                case "--head":
                    $this->requestParams[CURLOPT_HEADER] = true;
                    $this->requestParams[CURLOPT_NOBODY] = true;
                    continue;
                case "--request":
                    $this->setMethod($param[1]);
                    continue;
                case "--location":
                    $this->setUrl($param[1]);
                    continue;
                case "--header":
                    $this->setHeaders([$param[1]]);
                    continue;
                case "--data":
                    if ($this->contentType == self::JSONCTYPE) {
                        $this->setRequestBody(json_decode($param[1], true));
                    } else {
                        $data = explode("=", urldecode($param[1]));
                        $this->requestBody[$data[0]] = $data[1];
                    }
                    continue;
                case "--data-urlencode":
                    $data = explode("=", urldecode($param[1]));
                    $this->requestBody[$data[0]] = $data[1];
                    continue;
                case "--form":
                    $data = explode("=", $param[1]);
                    $this->requestBody[$data[0]] = $data[1];
                    continue;
            }
        }

        if ($this->requestBody && $this->method == "GET") {
            $this->setMethod("POST");
        }

        return $this;
    }

    public function buildObject()
    {
        $this->init();

        $object = "\$curl = (new " . self::class . "())->setUrl('" . $this->url . "')";

        if ($this->headers) {
            $object .= "->setHeaders(['" . implode("', '", $this->headers) . "'])";
        }

        if ($this->requestBody) {
            $object .= "->setRequestBody([" . implode(", ", array_map(function ($key, $value) {
                return "'$key' => '$value'";
            }, array_keys($this->requestBody), array_values($this->requestBody))) . "])";
        }

        if (isset($this->requestParams[CURLOPT_NOBODY])) {
            $object .= "->getHeaders()";
        } else {
            $object .= "->" . strtolower($this->method) . "();";
        }

        return $object;
    }

    public function info()
    {
        $this->requestParams[CURLINFO_HEADER_OUT] = true;
        return $this->init()->responseInfo;
    }

    public function code()
    {
        return $this->init()->statusCode;
    }

    public function isOkay()
    {
        return $this->code() === 200;
    }

    public function debug()
    {
        $this->debugMode = true;
        return $this->init()->verboseData;
    }

    public function get()
    {
        $this->setMethod("GET");
        return $this->getResponse();
    }

    public function post()
    {
        $this->setMethod("POST");
        return $this->getResponse();
    }

    public function patch()
    {
        $this->setMethod("PATCH");
        return $this->getResponse();
    }

    public function put()
    {
        $this->setMethod("PUT");
        return $this->getResponse();
    }

    public function delete()
    {
        $this->setMethod("DELETE");
        return $this->getResponse();
    }

    public function json()
    {
        $this->setMethod("JSON");
        return $this->getResponse();
    }
}