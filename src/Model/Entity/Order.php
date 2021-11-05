<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $order_id
 * @property int|null $user_id
 * @property int|null $address_id
 * @property string $products
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenDate $delivery_date
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Address $address
 */
class Order extends Entity
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
        'user_id' => true,
        'address_id' => true,
        'products' => true,
        'created' => true,
        'delivery_date' => true,
        'user' => true,
        'address' => true,
    ];
}
