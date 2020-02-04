<?php
namespace OpsDev\Validator\Domain\Validator;
/*                                                                        *
 * This script belongs to the TYPO3 Flow package "OpsDev.Validator".      *
 *                                                                        *
 *                                                                        */

class GeocodeValidator extends \Neos\Flow\Validation\Validator\AbstractValidator {
    /**
     * Checks a given Domain Name to be syntactically correct regarding the naming rules
     * in the relevant RFCs.
     *
     * @param string $geocode the geocode to validate
     * @return bool the result of the validation, TRUE if all is fine, FALSE otherwise
     */
    protected function isValid($geocode) {


        // check syntax of the geocode
        if (preg_match('/(-?[0-9]{1,2}\.[0-9]{1,8})(,){1}(-?[0-9]{1,3}\.[0-9]{1,8}){1}/', $geocode) === 1) {

            $parts = explode(',', $geocode);
            $latitude = $parts[0];
            $longitude = $parts[1];

            if ($latitude < -90 || $latitude > 90) {
                $this->addError('latitude out of range', 1580811143);
                $hasErrors = true;
                return false;
            }

            if ($longitude < -180 || $longitude > 180) {
                $this->addError('longitude out of range', 1580811211);
                $hasErrors = true;
                return false;
            }

            // seems fine
            return true;
        } else {
            $this->addError('invalid syntax', 1580810722);
            $hasErrors = true;
            return false;
        }
    }
}
