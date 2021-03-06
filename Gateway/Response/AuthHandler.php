<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cenpos\SimpleWebpay\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order;

class AuthHandler implements HandlerInterface
{
    const TXN_ID = 'TXN_ID';
    
    private $subjectReader;

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        
        $newData = array();
        foreach ($payment->getAdditionalInformation() as $key => $value) {
            if((strpos($key, 'payment[webpay') === false)){
                $newData[$key] = $value;
            }
        }
        
      
        $order = $paymentDO->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Magento\Sales\Model\Order\Payment\Transaction')->getCollection();
        
      
        $payment->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $newData);
        $payment->setTransactionId($payment->getAdditionalInformation("ReferenceNumber"));
        $payment->setIsTransactionClosed(false);

       
       
    }
}
