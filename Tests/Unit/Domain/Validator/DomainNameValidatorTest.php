<?php
namespace OpsDev\Validator\Tests\Unit\Domain\Validator;
/*                                                                        *
 * This script belongs to the Flow package "OpsDev.Validator".            *
 *                                                                        *
 *                                                                        */
use OpsDev\Validator\Domain\Validator\DomainNameValidator;
/**
 * Testcase for the Domain Name Validator
 */
class DomainNameValidatorTest extends \Neos\Flow\Tests\UnitTestCase
{
    /**
     * @var DomainNameValidator
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new DomainNameValidator();
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
     * @test
     */
    public function isValidReturnsTrueOnValidDomainName()
    {
        $this->assertNotHasErrors(
            $this->fixture->validate('foobar.ch')
        );
    }

    /**
     * @test
     */
    public function isValidReturnsTrueOnValidDomainNameWithUmlauts()
    {
        $this->assertNotHasErrors(
            $this->fixture->validate('vögeli.ch')
        );
    }

    /**
     * @test
     */
    public function isValidRaisesErrorOnDomainNameWithoutADot()
    {
        $this->assertHasErrors(
            $this->fixture->validate('foobar')
        );
    }

    /**
     * @test
     */
    public function isValidRaisesErrorOnTooLongDomainName()
    {
        $this->assertHasErrors(
            $this->fixture->validate('foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobar' .
                'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobar' .
                'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobar' .
                'foobarfoobarfoobarfoobarfoobarfoobarfoobarfoobarfoobar.com')
        );
    }

    /**
     * @test
     * @dataProvider domainNamesWithInvalidCharactersProvider
     */
    public function isValidRaisesErrorOnInvalidCharacters($domainName)
    {
        $this->assertHasErrors(
            $this->fixture->validate($domainName)
        );
    }

    /**
     * Provides invalid domain names with invalid characters
     *
     * @return array
     */
    public function domainNamesWithInvalidCharactersProvider()
    {
        return array(
            array('foo_bar.ch'),
            array('foo%bar.ch'),
            array('foo&bar.ch'),
            array('foo*bar.ch'),
            array('foo+bar.ch'),
            array('foo,bar.ch')
        );
    }

    /**
     * @test
     */
    public function isValidRaisesErrorOnDomainWithTooManyLabels()
    {
        // 128 labels
        $this->assertHasErrors(
            $this->fixture->validate('a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x')
        );
    }

    /**
     * @test
     * @dataProvider invalidDomainNamesWithTooLongLabelsProvider
     */
    public function isValidRaisesErrorOnDomainNameWithTooLongLabel($domainName)
    {
        $this->assertHasErrors(
            $this->fixture->validate($domainName)
        );
    }

    /**
     * Provides invalid domain names that contain too long labels
     *
     * @return array
     */
    public function invalidDomainNamesWithTooLongLabelsProvider()
    {
        return array(
            // 64 characters
            array('foo.aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
            // 64 characters
            array('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.foo'),
            // 64 characters
            array('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.foo.bar')
        );
    }

    /**
     * @test
     */
    public function isValidRaisesErrorOnDomainNameConsistingOfOnlyNumbers()
    {
        $this->assertHasErrors(
            $this->fixture->validate('12345.net')
        );
    }

    /**
     * @test
     * @dataProvider invalidDomainNamesWithDashesProvider
     */
    public function isValidRaisesErrorOnDomainNameWithDashesOnTheBeginOrEndOfALabel($domainName)
    {
        $this->assertHasErrors(
            $this->fixture->validate($domainName)
        );
    }

    /**
     * Provides invalid domain names dashes at the begin/end of labels
     *
     * @return array
     */
    public function invalidDomainNamesWithDashesProvider()
    {
        return array(
            array('-foobar.ch'),
            array('foobar-.ch'),
            array('-.foobar.ch'),
            array('-foo.bar.ch'),
            array('foo-.bar.ch')
        );
    }

    /**
     * @test
     * @dataProvider validDomainNamesWithDifferentAllowedTopLevelDomains
     */
    public function isValidReturnsTrueOnValidDomainNameFromListOfAllowedTopLevelDomains()
    {
        $this->assertNotHasErrors(
            $this->fixture->validate('opsdev.ch')
        );
    }

    /**
     * Provides valid domain names with some known and some unknown toplevel domains
     *
     * @return array
     */
    public function validDomainNamesWithDifferentAllowedTopLevelDomains()
    {
        return array(
            array('foobar.ch'),
            array('foobar.CH'),
            array('foobar.com'),
            array('foobar.abudhabi'),
            array('foobar.zone'),
            array('foobar.vermögensberater'),
        );
    }

    /**
     * @test
     */
    public function isValidRaisesErrorOnDomainNameWithInexistentTopLevelDomain()
    {
        $this->assertHasErrors(
            $this->fixture->validate('foo.bürk')
        );
    }




}
