<?php
/**
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;

class ExportProductsContext implements Context
{
    /**
     * @Given /^the product bimbamboum categorized in shoes and clothes$/
     */
    public function theProductBimbamboumCategorizedInShoesAndClothes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^another product boumbambim without any category$/
     */
    public function anotherProductBoumbambimWithoutAnyCategory()
    {
        throw new PendingException();
    }

    /**
     * @When /^I export these products from the API$/
     */
    public function iExportTheseProductsFromTheAPI()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I have the following file:$/
     */
    public function iHaveTheFollowingFile(PyStringNode $string)
    {
        throw new PendingException();
    }
}
