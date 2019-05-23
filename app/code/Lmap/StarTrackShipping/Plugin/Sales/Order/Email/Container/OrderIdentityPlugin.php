<?php

namespace Lmap\StarTrackShipping\Plugin\Sales\Order\Email\Container;
 // Plugin created to send email to customer after successful order payment.
 // Since third party payment engine is used hence magento doesn't send automatic confirmation emails.
 // Hence this plugin is created just incase no duplicate emails are sent.
 // Reference: https://magecomp.com/blog/send-order-confirmation-email-after-successful-payment-magento-2/
class OrderIdentityPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundIsEnabled(\Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject, callable $proceed)
    {
        $returnValue = $proceed();

        $forceOrderMailSentOnSuccess = $this->checkoutSession->getForceOrderMailSentOnSuccess();
        if(isset($forceOrderMailSentOnSuccess) && $forceOrderMailSentOnSuccess)
        {
            if($returnValue)
                $returnValue = false;
            else
            $returnValue = true;

            $this->checkoutSession->unsForceOrderMailSentOnSuccess();
        }

        return $returnValue;
    }
}
