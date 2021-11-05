<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CartProduct Entity
 *
 * @property int $cart_product_id
 * @property int|null $cart_id
 * @property int|null $product_id
 * @property int|null $quantity
 *
 * @property \App\Model\Entity\Cart $cart
 */
class CartProduct extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'cart_id' => true,
        'product_id' => true,
        'quantity' => true,
        'cart' => true,
    ];
}
