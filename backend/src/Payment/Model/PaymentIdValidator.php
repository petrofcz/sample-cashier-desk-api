<?php
declare(strict_types=1);

namespace App\Payment\Model;

use Ramsey\Uuid\Validator\GenericValidator;

class PaymentIdValidator
{
    protected GenericValidator $uuidValidator;

    /**
     * PaymentIdValidator constructor.
     * @param GenericValidator $uuidValidator
     */
    public function __construct(GenericValidator $uuidValidator)
    {
        $this->uuidValidator = $uuidValidator;
    }

    public function validate($id): bool  {
        if(!is_string($id)) {
            return false;
        }
        return $this->uuidValidator->validate($id);
    }
}