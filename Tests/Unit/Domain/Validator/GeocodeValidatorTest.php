<?php
namespace OpsDev\Validator\Tests\Unit\Domain\Validator;
/*                                                                        *
 * This script belongs to the Flow package "OpsDev.Validator".            *
 *                                                                        *
 *                                                                        */
use OpsDev\Validator\Domain\Validator\GeocodeValidator;

/**
 * Testcase for the Geocode Validator
 */
class GeocodeValidatorTest extends \Neos\Flow\Tests\UnitTestCase
{
    /**
     * @var GeocodeValidator
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new GeocodeValidator();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * Assert that the validation result object has at least one error
     *
     * @param \Neos\Error\Messages\Result $result
     * @param string $message
     */
    protected function assertHasErrors(\Neos\Error\Messages\Result $result, $message = '')
    {
        self::assertThat(
            $result->hasErrors(),
            self::isTrue(),
            $message
        );
    }

    /**
     * Assert that the validation result object has no errors
     *
     * @param \Neos\Error\Messages\Result $result
     * @param string $message
     */
    protected function assertNotHasErrors(\Neos\Error\Messages\Result $result, $message = '')
    {
        self::assertThat(
            $result->hasErrors(),
            self::isFalse(),
            $message
        );
    }



    /**
     * Provides invalid domain names with invalid characters
     *
     * @return array
     */
    public function validGeoCodesProvider()
    {
        return array(
            array('47.303447,7.940474'),
            array('37.935533,-77.917451'),
            array('77.500850,17.155148'),
            array('-34.079962,21.861574'),
        );
    }

    /**
     * @test
     * @dataProvider validGeoCodesProvider
     */
    public function isValidReturnsTrueOnValidGeocodes($geocode)
    {
        $this->assertNotHasErrors(
            $this->fixture->validate($geocode)
        );
    }

    /**
     * Provides invalid domain names with invalid characters
     *
     * @return array
     */
    public function invalidGeoCodesProvider()
    {
        return array(
            array('47.303447, 7.940474'), // spaces
            array('47.303447.7.940474'), // dot instead of comma
            array('47.303447 7.940474'), // space instead of comma
            array('91.100000,5.000000'), // out of bounds latitude
            array('-91.100000,5.000000'), // out of bounds latitude
            array('10.000000,181.00000'), // out of bounds longitude
            array('10.000000,-181.00000'), // out of bounds longitude
        );
    }

    /**
     * @test
     * @dataProvider invalidGeoCodesProvider
     */
    public function isValidReturnsFalseOnInvalidGeocodes($geocode)
    {
        $this->assertHasErrors(
            $this->fixture->validate($geocode)
        );
    }
}
