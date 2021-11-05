<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductsViewedTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductsViewedTable Test Case
 */
class ProductsViewedTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductsViewedTable
     */
    public $ProductsViewed;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ProductsViewed',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('ProductsViewed') ? [] : ['className' => ProductsViewedTable::class];
        $this->ProductsViewed = TableRegistry::getTableLocator()->get('ProductsViewed', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ProductsViewed);

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
