<?php

namespace App\Services\Balance;

use Carbon\Carbon;

class NovaPayBalanceService implements BalancerServiceInterface
{
    private const ENDPOINT = 'https://business.novapay.ua/Services/ClientAPIService.svc';
    private const NAMESPACE = 'http://tempuri.org/';

    public function getTotalTurnover(string $apiKey): int
    {
        [$login, $password] = explode(':', $apiKey);

        $preAuth = $this->sendSoapRequest('PreUserAuthentication', [
            'request' => [
                'login' => $login,
                'password' => $password,
            ],
        ]);

        $preResult = $preAuth['PreUserAuthenticationResponse']['PreUserAuthenticationResult'] ?? null;
dd($preAuth);
        if (!$preResult) {
            return 0;
        }

        $auth = $this->sendSoapRequest('UserAuthentication', [
            'request' => [
                'request_ref' => '',
                'temp_principal' => $preResult['temp_principal'],
                'code_operation_otp' => $preResult['code_operation_otp'],
                'otp_password' => $otp,
            ],
        ]);

        $authResult = $auth['UserAuthenticationResponse']['UserAuthenticationResult'] ?? null;

        if (!$authResult) {
            return 0;
        }

        $principal = $authResult['principal'] ?? null;

        if (!$principal) {
            return 0;
        }

        $clients = $this->sendSoapRequest('GetClientsList', [
            'request' => [
                'request_ref' => '',
                'principal' => $principal,
            ],
        ]);

        $clientId = $clients['GetClientsListResponse']['GetClientsListResult']['clients']['Client']['id'] ?? null;

        if (!$clientId) {
            return 0;
        }

        $accounts = $this->sendSoapRequest('GetAccountsList', [
            'request' => [
                'request_ref' => '',
                'principal' => $principal,
                'client_id' => $clientId,
            ],
        ]);

        $accountId = $accounts['GetAccountsListResponse']['GetAccountsListResult']['accounts']['Account']['id'] ?? null;

        if (!$accountId) {
            return 0;
        }

        $from = Carbon::yesterday()->format('d.m.Y');
        $to = Carbon::yesterday()->format('d.m.Y');

        $turns = $this->sendSoapRequest('GetAccountTurns', [
            'request' => [
                'request_ref' => '',
                'principal' => $principal,
                'account_id' => $accountId,
                'date_from' => $from,
                'date_to' => $to,
            ],
        ]);

        $total = 0;
        $items = $turns['GetAccountTurnsResponse']['GetAccountTurnsResult']['turns']['Turns'] ?? [];

        if (isset($items['CrncyCredit'])) {
            $total += (float)$items['CrncyCredit'];
        } else {
            foreach ($items as $turn) {
                $total += (float)$turn['CrncyCredit'];
            }
        }

        return (int)round($total);
    }

    private function sendSoapRequest(string $action, array $params): array
    {
        $xmlBody = $this->buildSoapEnvelope($action, $params);

        $headers = [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . self::NAMESPACE . 'IClientAPIService/' . $action . '"',
            'Content-Length: ' . strlen($xmlBody),
        ];

        $ch = curl_init(self::ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [];
        }

        curl_close($ch);

        $xml = simplexml_load_string($response);
        $json = json_decode(json_encode($xml), true);

        return $json['s:Body'] ?? [];
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
