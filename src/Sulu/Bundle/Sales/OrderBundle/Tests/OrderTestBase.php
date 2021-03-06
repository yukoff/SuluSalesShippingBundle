<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\Sales\OrderBundle\Tests;

use Doctrine\ORM\EntityManager;
use Sulu\Bundle\ContactBundle\Entity\AccountInterface;
use Sulu\Bundle\ContactBundle\Entity\Address;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\Sales\CoreBundle\Item\ItemFactoryInterface;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;

class OrderTestBase extends SuluTestCase
{
    protected $locale = 'en';

    protected static $orderStatusEntityName = 'SuluSalesOrderBundle:OrderStatus';

    /**
     * @var OrderDataSetup
     */
    protected $data;
    
    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        $this->em = $this->db('ORM')->getOm();
        $this->purgeDatabase();
        $this->setUpTestData();
        $this->client = $this->createAuthenticatedClient();
        $this->em->flush();
    }

    protected function setUpTestData()
    {
        $this->data = new OrderDataSetup($this->em, $this->getItemFactory());
    }

    /**
     * compares an order-address response with its origin entities
     */
    protected function checkOrderAddress($orderAddress, Address $address, Contact $contact, AccountInterface $account = null) {
        // contact
        $this->assertEquals($contact->getFirstName(), $orderAddress->firstName);
        $this->assertEquals($contact->getLastName(), $orderAddress->lastName);
        if ($contact->getTitle() !== null) {
            $this->assertEquals($contact->getTitle()->getTitle(), $orderAddress->title);
        }

        // address
        $this->assertEqualsIfExists($address->getStreet(), $orderAddress, 'street');
        $this->assertEqualsIfExists($address->getAddition(), $orderAddress, 'addition');
        $this->assertEqualsIfExists($address->getNumber(), $orderAddress, 'number');
        $this->assertEqualsIfExists($address->getCity(), $orderAddress, 'city');
        $this->assertEqualsIfExists($address->getZip(), $orderAddress, 'zip');
        $this->assertEqualsIfExists($address->getCountry()->getName(), $orderAddress, 'country');
        $this->assertEqualsIfExists($address->getPostboxNumber(), $orderAddress, 'postboxNumber');
        $this->assertEqualsIfExists($address->getPostboxCity(), $orderAddress, 'postboxCity');
        $this->assertEqualsIfExists($address->getPostboxPostcode(), $orderAddress, 'postboxPostcode');

        // account
        if ($account) {
            $this->assertEqualsIfExists($account->getName(), $orderAddress, 'accountName');
            $this->assertEqualsIfExists($account->getUid(), $orderAddress, 'uid');
        }
    }

    /**
     * asserts equality if object's attribute exist
     */
    protected function assertEqualsIfExists($firstValue, $secondObject, $value) {
        if ($firstValue !== null) {
            $this->assertEquals($firstValue, $secondObject->$value);
        }
    }

    /**
     * @return ItemFactoryInterface
     */
    protected function getItemFactory()
    {
        return $this->getContainer()->get('sulu_sales_core.item_factory');
    }
}
