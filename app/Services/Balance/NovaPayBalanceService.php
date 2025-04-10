<?php

namespace App\Services\Balance;

use SimpleXMLElement;

class NovaPayBalanceService implements BalancerServiceInterface
{
    private const ENDPOINT = 'https://business.novapay.ua/Services/ClientAPIService.svc';
    private const NAMESPACE = 'http://tempuri.org/';

    public function getTotalTurnover(string $apiKey): int
    {
//        [$login, $password] = explode(':', $apiKey);

//        $preAuth = $this->sendSoapRequest('PreUserAuthentication', [
//            'request' => [
//                'login' => $login,
//                'password' => $password,
//            ],
//        ]);
//        $preAuthResponse = $preAuth->children('http://tempuri.org/')->PreUserAuthenticationResponse;
//        $preResult = $preAuthResponse->PreUserAuthenticationResult;
//
//        $tempPrincipal = (string)$preResult->temp_principal;
//        $codeOperationOtp = (string)$preResult->code_operation_otp;
//
//        dd([
//            $tempPrincipal,
//            $codeOperationOtp,
//        ]);

        $this->sendSoapRequest('RefreshUserAuthentication', [
            'request' => [
                'principal' => $apiKey,
            ],
        ]);

        $response = $this->sendSoapRequest('GetClientsList', [
            'request' => [
                'principal' => $apiKey,
            ],
        ]);
        $preResponse = $response->children('http://tempuri.org/')->GetClientsListResponse;
        $preResult = $preResponse->GetClientsListResult;
        $firstClientId = (string)$preResult->clients->Clients->id;

        $response = $this->sendSoapRequest('GetAccountsList', [
            'request' => [
                'principal' => $apiKey,
                'client_id' => $firstClientId,
            ],
        ]);
        $preResponse = $response->children('http://tempuri.org/')->GetAccountsListResponse;
        $preResult = $preResponse->GetAccountsListResult;
        $firstAccountId = (string)$preResult->accounts->Accounts->id;

        $response = $this->sendSoapRequest('GetAccountRest', [
            'request' => [
                'principal' => $apiKey,
                'account_id' => $firstAccountId,
            ],
        ]);
        $preResponse = $response->children('http://tempuri.org/')->GetAccountRestResponse;
        $preResult = $preResponse->GetAccountRestResult;

        return (int) $preResult->confirmed_balance;
    }

    private function sendSoapRequest(string $action, array $params): SimpleXMLElement
    {
        $xmlBody = $this->buildSoapEnvelope($action, $params);

        $ch = curl_init(self::ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . self::NAMESPACE . 'IClientAPIService/' . $action . '"',
            'Content-Length: ' . strlen($xmlBody),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [];
        }

        curl_close($ch);

        $xml = simplexml_load_string($response);

        $namespaces = $xml->getNamespaces(true);
        return $xml->children($namespaces['s'])->Body;
    }

    private function buildSoapEnvelope(string $action, array $params): string
    {
        $requestXml = '';

        foreach ($params['request'] as $key => $value) {
            $requestXml .= "<tem:$key>$value</tem:$key>";
        }

        return <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
  <soapenv:Header/>
  <soapenv:Body>
    <tem:$action>
      <tem:request>
        $requestXml
      </tem:request>
    </tem:$action>
  </soapenv:Body>
</soapenv:Envelope>
XML;
    }
}
