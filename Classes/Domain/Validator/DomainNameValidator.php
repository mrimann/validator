<?php
namespace OpsDev\Validator\Domain\Validator;
/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Roketi.Panel".          *
 *                                                                        *
 *                                                                        */
use TrueBV\Punycode;

class DomainNameValidator extends \Neos\Flow\Validation\Validator\AbstractValidator {
    /**
     * The IDNA / Punycode representation of the domain name to check
     *
     * @var string
     */
    protected $domainNameToCheck;

    /**
     * Checks a given Domain Name to be syntactically correct regarding the naming rules
     * in the relevant RFCs.
     *
     * @param string $domainName the domain name to validate
     * @return bool the result of the validation, TRUE if all is fine, FALSE otherwise
     */
    protected function isValid($domainName) {
        // check for the length and other evil stuff first, must happen *before* converting to IDNA string
        // (because the conversion would throw an exception on too long domain names)
        if ($this->checkLength($domainName) != true
            || $this->checkForValidLengthOfSingleLabels($domainName) != true
            || $this->checkForValidNumberOfLabels($domainName) != true) {
            $this->addError('invalid domain name', 1409169479);
            return false;
        }

        // encode the domain name to an IDNA punycode domain name
        $punycoder = new Punycode();
        try {
            $this->domainNameToCheck = $punycoder->encode($domainName);
        } catch (\Exception $e) {

        }

        if (
            $this->checkForValidNumberOfLabels($domainName)
            && $this->checkForValidLengthOfSingleLabels($domainName)
            && $this->checkLength($domainName)
            && $this->checkForAtLeastOneDot()
            && $this->checkForAllowedCharacters()
            && $this->checkForNumberOnlyLabels()
            && $this->checkForDashesAtBeginEndOfLabels()
        ) {
            // all seems to be fine
            return true;
        } else {
            $this->addError('invalid domain name', 1409169479);
            $hasErrors = true;
        }
    }

    /**
     * Checks if there are more than 127 parts within that domain name, more are
     * not allowed.
     *
     * @see RFC1035
     * @param string
     * @return bool
     */
    private function checkForValidNumberOfLabels($domainName) {
        $labels = explode('.', $domainName);
        if (count($labels) > 127) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Checks if there is any of the segments longer than 63 characters, more are not allowed.
     *
     * @see RFC1035
     * @param $domainName
     * @return bool
     */
    private function checkForValidLengthOfSingleLabels($domainName) {
        $labels = explode('.', $domainName);
        foreach ($labels as $label) {
            if (strlen($label) > 63) {
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * Check if there are invalid characters within the domain name. Allowed characters are:
     * - a-z
     * - A-Z
     * - 0-9
     * - the hyphen "-"
     *
     * @return bool
     */
    private function checkForAllowedCharacters() {
        return (preg_match(
                '/^([a-zA-Z0-9\-\.])+$/',
                $this->domainNameToCheck
            ) === 1);
    }
    /**
     * Checks if there are only numbers within a domain name label
     *
     * @return bool
     */
    private function checkForNumberOnlyLabels() {
        $labels = explode('.', $this->domainNameToCheck);
        foreach ($labels as $label) {
            if (preg_match('/^([0-9])+$/', $label) === 1) {
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * Checks whether there is a label that either starts or ends with a "-" which
     * is not allowed per definition.
     *
     * @see RFC1035
     * @return boolean
     */
    private function checkForDashesAtBeginEndOfLabels() {
        $labels = explode('.', $this->domainNameToCheck);
        foreach ($labels as $label) {
            if ((preg_match('/^\-/', $label) === 1)
                || (preg_match('/\-$/', $label) === 1)) {
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * Checks if there's at least one single dot within the given domain name.
     *
     * @return bool
     */
    private	function checkForAtLeastOneDot() {
        return (strpos(
                $this->domainNameToCheck,
                '.'
            ) > 0);
    }
    /**
     * Checks if the overall length is not more than 253 characters long
     *
     * @see RFC1035
     * @param string the domain name to check
     * @return bool
     */
    private function checkLength($domainName) {
        return (strlen($domainName) <= 253);
    }
}