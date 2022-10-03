<?php

require_once ("model.php");

class Sender extends model {

    var $host;
    var $port;
    /*
     * Username that is to be used for submission
     */
    var $strUserName;
    /*
     * password that is to be used along with username
     */
    var $strPassword;
    /*
     * Sender Id to be used for submitting the message
     */
    var $strSender;
    /*
     * Message content that is to be transmitted
     */
    var $strMessage;
    /*
     * Mobile No is to be transmitted.
     */
    var $strMobile;
    /*
     * What type of the message that is to be sent
     * <ul>
     * <li>0:means plain text/li>
     * <li>1:means flash</li>
     * <li>2:means Unicode (Message content should be in Hex)</li>
     * <li>6:means Unicode Flash (Message content should be in Hex)</li>
     * </ul>
     */
    var $strMessageType;
    /*
     * Require DLR or not
     * <ul>
     * <li>0:means DLR is not Required</li>
     * <li>1:means DLR is Requiredli>
     * </ul>
     */
    var $strDlr;

    private function sms__unicode($message) {
        $hex1 = '';
        if (function_exists('iconv')) {
            $latin = @iconv('UTF-8', 'ISO-8859-1', $message);
            if (strcmp($latin, $message)) {
                $arr = unpack('H*hex', @iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['hex']);
            }
            if ($hex1 == '') {
                $hex2 = '';
                $hex = '';
                for ($i = 0; $i < strlen($message); $i++) {
                    $hex = dechex(ord($message[$i]));
                    $len = strlen($hex);
                    $add = 4 - $len;
                    if ($len < 4) {
                        for ($j = 0; $j < $add; $j++) {
                            $hex = "0" . $hex;
                        }
                    }
                    $hex2.=$hex;
                }
                return $hex2;
            } else {
                return $hex1;
            }
        } else {
            print 'iconv Function Not Exists !';
        }
    }

//Constructor..
    public function Sender($username, $password, $message, $mobile, $sender = null) {
        parent::__construct();
        $this->host = "121.241.242.114";
        $this->port = "8080";
        $this->strUserName = $username;
        $this->strPassword = $password;
        $this->strSender = ($sender != null) ? $sender : $this->proprietaire;
        $this->strMessage = $message; //URL Encode The Message..
        $this->strMobile = $mobile;
        $this->strMessageType = 0;
        $this->strDlr = 0;
    }

    public function Submit() {
        if ($this->strMessageType == "2" ||
                $this->strMessageType == "6") {
            //Call The Function Of String To HEX.
            $this->strMessage = $this->sms__unicode($this->strMessage);
            try {
                //Smpp http Url to send sms.
                $live_url = "http://" . $this->host . ":" . $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "password=" . $this->strPassword . "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile . "&source=" . $this->strSender . "&message=" . $this->strMessage . "";
               
                $parse_url = file($live_url);
            } catch (Exception $e) {
                echo 'Message:' . $e->getMessage();
            }
        }
        else
            $this->strMessage = urlencode($this->strMessage);
        try {
            //Smpp http Url to send sms.
            $live_url = "http://" . $this->host . ":" . $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "&password=" . $this->strPassword . "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile . "&source=" . $this->strSender . "&message=" . $this->strMessage . "";
            $parse_url = file($live_url);
              
        } catch (Exception $e) {
            echo 'Message:' . $e->getMessage();
        }
    }

}

?>