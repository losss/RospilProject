<?php

class Communicator {

    public static function sendEmail($to, $message, $from, $subj, $from_str='') {
        $headers = "From: ".($from_str?$from_str:$from);
        $headers .= "\r\nContent-Type: text/html;";
        $headers .= "\r\nReturn-Path: $from";
        $headers .= "\r\nReceived: $from";

        try {
        	$result = @mail($to, $subj, $message, $headers);
			//$result = @mail($to, $subj, $message, $headers, '-f'.$from); // was working with registration but not working with ongoing notifications
			// $result = @mail($to, $subj, $message, $headers);
        } catch (Exception $e) {
            // do nothing
            Logger::log(Logger::WARNING, "CANNOT SEND '$subj' email to '$to'");
        }
        Logger::log(Logger::WARNING, "Sent '$subj' email to '$to', [$result]");

        return $result;
    }

    public static function sendSms(NotificationMedia $smsMedia, $smsTemplate, $templateParams, $from, $subj) {        
        $result = null;
        $provider = $smsMedia->getProvider();
        if ($provider) {
            $providerDescr = Settings::$MOBILE_PROVIDERS[$provider];
            if ($providerDescr) {
                $to = $smsMedia->getContactInfo();
                $to = self::getNormalizedPhoneNumber($to);
                $to = $to.'@'.$providerDescr['email'];

                $template = new Template();
                $message = $template->render($smsTemplate, $templateParams);

                $headers = "From: $from";
                $headers .= "\r\nContent-Type: ".Settings::$VALIDATION_EMAIL_TYPE;
                $headers .= "\r\nReturn-Path: $from";
                $headers .= "\r\nReceived: $from";

                $result = @mail($to, $subj, $message, $headers, '-f'.$from);
                Logger::log(Logger::WARNING, "Sent '$subj' sms to '$to'");
            }
        }

        return $result;
    }

    public static function getNormalizedPhoneNumber($number) {
        $number = trim($number);
        $number = preg_replace('/[^\d]/', '', $number);
        if (strlen($number) > Settings::$PHONE_NUMBER_LENGTH) {
            $number = substr($number, -Settings::$PHONE_NUMBER_LENGTH, Settings::$PHONE_NUMBER_LENGTH);
        }

        return $number;
    }

    public static function validatePhone($number) {
        $result = true;
        $num = preg_replace('/[+()\-\s]/', '', $number);
        $len = strlen($num);
        if ($len != 10) {
            if ($len != 11 || $num[0] != '1') {
                $result = false;
            }
        }
        if ($result) {
            if (preg_match('/[^\d]/', $num)) {
                $result = false;
            }
        }

        return $result;
    }


}
?>