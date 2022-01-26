<?php
class OneBoxID_Client {

    public function auth($logintype, $login, $password = false) {
        $a = [];
        $a['logintype'] = $logintype;
        if ($logintype === 'phone') {
            $a['phone'] = $login;
        } elseif ($logintype === 'email') {
            $a['email'] = $login;
        }

        if (!empty($password)) {
            $a['password'] = $password;
        }

        return $this->_request('auth.php', $a);
    }

    public function logout($userToken, $fullLogout = false) {
        $a = [];

        if ($fullLogout) {
            $a['fulllogout'] = 1;
        }

        return $this->_request('logout.php', $a, $userToken);
    }

    public function userinfo($userToken) {
        return $this->_request('get_userinfo_by_token.php', [], $userToken);
    }

    public function update($userToken,
        $logintype,
        $email,
        $phone,
        $password,
        $name_first,
        $name_last,
        $name_middle,
        $company
    ) {
        $a = [];

        if ($logintype) {
            $a['logintype'] = $logintype;
        }

        if ($email) {
            $a['email'] = $email;
        }

        if ($phone) {
            $a['phone'] = $phone;
        }

        if ($password) {
            $a['password'] = $password;
        }

        if ($name_first) {
            $a['name_first'] = $name_first;
        }

        if ($name_last) {
            $a['name_last'] = $name_last;
        }

        if ($name_middle) {
            $a['name_middle'] = $name_middle;
        }

        if ($company) {
            $a['company'] = $company;
        }

        return $this->_request('logout.php', $a, $userToken);
    }

    public function verify($logintype, $login, $code) {
        $a = [];

        if ($logintype === 'phone') {
            $a['phone'] = $login;
        } elseif ($logintype === 'email') {
            $a['email'] = $login;
        }

        $a['code'] = $code;

        return $this->_request('check_verify_code.php', $a);
    }

    private function _request($urlEndpoint, $paramArray, $userToken = false) {
        $url = 'https://id.1b.app/'.$urlEndpoint;

        if ($this->_printLog) {
            print date('Y-m-d H:i:s')."\n";
            print "Request ".$url."\n";
            if ($paramArray) {
                print_r($paramArray);
            }
        }

        $headerArray = [];
        $headerArray[] = 'devtoken: '.$this->_devToken;
        if ($userToken) {
            $headerArray[] = 'usertoken: '.$userToken;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if ($paramArray) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramArray));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        if (defined('CURLOPT_TCP_FASTOPEN')) {
            curl_setopt($ch, CURLOPT_TCP_FASTOPEN, 1);
        }
        //curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // verbose log
        /*curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);*/

        // exec
        $result = curl_exec($ch);

        $result = trim($result);
        if (!$result) {
            throw new OneBoxID_Exception('Empty responce');
        }

        $json = json_decode($result, true);
        if (!$json) {
            throw new OneBoxID_Exception('Invalid json responce');
        }

        if ($this->_printLog) {
            print "Responce:\n";
            print_r($json);
        }

        $status = @$json['status'];
        if (!isset($json['status'])) {
            throw new OneBoxID_Exception('Invalid API responce, please go to support');
        }

        if ($status == 1) {
            return $json['dataArray'];
        }

        throw new OneBoxID_Exception($json['errorArray']);
    }


    /**
     * Create (init) OneBoxID instance
     *
     * @param string $devToken
     * @param bool $printLog
     * @return OneBoxID_Client
     * @throws OneBoxID_Exception
     */
    public static function Init($devToken, $printLog = false) {
        if (!$devToken) {
            throw new OneBoxID_Exception('Empty devToken');
        }

        self::$_Instance = new self($devToken, $printLog);
        return self::$_Instance;
    }

    /**
     * Get OneBoxID Instance
     *
     * @return OneBoxID_Client
     * @throws OneBoxID_Exception
     */
    public static function Get() {
        if (!self::$_Instance) {
            throw new OneBoxID_Exception('APIv2 not initialized, call Init() fist');
        }

        return self::$_Instance;
    }

    private function __construct($devToken, $printLog = false) {
        $this->_devToken = $devToken;
        $this->_printLog = $printLog;
    }

    private static $_Instance;

    private $_devToken;

    private $_printLog = false;

}