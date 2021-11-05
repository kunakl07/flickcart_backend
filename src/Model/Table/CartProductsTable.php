<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CartProducts Model
 *
 * @property \App\Model\Table\CartsTable&\Cake\ORM\Association\BelongsTo $Carts
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\BelongsTo $Products
 *
 * @method \App\Model\Entity\CartProduct get($primaryKey, $options = [])
 * @method \App\Model\Entity\CartProduct newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CartProduct[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CartProduct|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CartProduct saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CartProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CartProduct[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CartProduct findOrCreate($search, callable $callback = null, $options = [])
 */
class CartProductsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cart_products');
        $this->setDisplayField('cart_product_id');
        $this->setPrimaryKey('cart_product_id');

        $this->belongsTo('Carts', [
            'foreignKey' => 'cart_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('cart_product_id')
            ->allowEmptyString('cart_product_id', null, 'create');

        $validator
            ->integer('quantity')
            ->allowEmptyString('quantity');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['cart_id'], 'Carts'));
        return $rules;
    }
}
