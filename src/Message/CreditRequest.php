<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Credit AfterPay, Focum or Klarna payments
 */
class CreditRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/CreditInvoiceRequest';

    /**
     * @inheritDoc
     */
    protected function generateSignature()
    {
        return sha1($this->getTransactionId() . $this->getMerchantId() . $this->getMerchantKey());
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('transactionId', 'merchantId', 'merchantKey');

        return [
            'trxid' => $this->getTransactionId(),
            'merchantid' => $this->getMerchantId(),
            'sha1' => $this->generateSignature(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function sendData($data)
    {
        $endpoint = $this->endpoint;

        if ($this->getTestMode()) {
            $endpoint .= '?test=true';
        }

        return $this->response = new CreditResponse(
            $this,
            $this->parseXmlResponse(
                $this->httpClient->request(
                    'POST',
                    $endpoint,
                    [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    http_build_query($data)
                )
            )
        );
    }
}
