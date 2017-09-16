<?php namespace controllers;

use \models\LoginRequest;
use models\NjitLoginResponse;

class LoginController extends Controller {

    public function POST() {
	    try {
	        if ($this->context->getContentType() != "application/json") {
	            throw new \Exception("Invalid content type, expected application/json");
            }

            $requestBody = json_decode(file_get_contents('php://input'), true);
            $request = LoginRequest::fromAssociativeArray($requestBody);
            $njitloginResponse = $this->loginToNJIT($request);

            $response = array(
                "njitLogin" => $njitloginResponse->message->getValue()
            );

            echo json_encode($response);
        } catch (\Exception $exception) {
	        echo json_encode(["message" => $exception->getMessage()]);
        }

	}

	private function loginToNJIT($request) {
        $session = curl_init();

        curl_setopt_array($session, array(
            CURLOPT_URL => "https://cp4.njit.edu/cp/home/login",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => "user=" . $request->username->getValue() . "&pass=" . $request->password->getValue() . "&uuid=0xACA021"
        ));

        $response = curl_exec($session);
        $err = curl_errno($session);

        curl_close($session);

        if ($err) {
            throw new \Exception(curl_strerror($err));
        }

        return NjitLoginResponse::fromXML($response);
    }
}
