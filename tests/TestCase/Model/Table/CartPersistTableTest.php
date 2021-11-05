<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CartPersistTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CartPersistTable Test Case
 */
class CartPersistTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CartPersistTable
     */
    public $CartPersist;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CartPersist',
        'app.Users',
        'app.Carts',
        'app.Products',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CartPersist') ? [] : ['className' => CartPersistTable::class];
        $this->CartPersist = TableRegistry::getTableLocator()->get('CartPersist', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CartPersist);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
