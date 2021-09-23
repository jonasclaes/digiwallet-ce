<?php

namespace DigiWalletCE;

class ActionTypeEnum
{
    /**
     * Invalid action.
     */
    const NONE = -1;

    /**
     * Pay a donation.
     */
    const PAY = 1;

    /**
     * Return response from DigiWallet.
     */
    const RETURN = 2;

    /**
     * Canceled response from DigiWallet.
     */
    const CANCELED = 3;

    /**
     * Report response from DigiWallet.
     */
    const REPORT = 4;
}