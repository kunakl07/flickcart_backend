<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Addresses Controller
 *
 * @property \App\Model\Table\AddressesTable $Addresses
 *
 * @method \App\Model\Entity\Address[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AddressesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){
        $uid = $this->Auth->user('user_id');
        $addresses = $this->Addresses->findByUserId($uid);

        $this->set([
            'addresses' => $addresses,
            '_serialize' => ['addresses']
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Address id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id){   
        $address = $this->Addresses->findByAddressId($id)->firstOrFail();
        $this->set([
            'address' => $address->address,
            '_serialize' => ['address']
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $address = $this->Addresses->newEntity();
            $uid = $this->Auth->user('user_id');
            $text = $this->request->getData('address');
            $address = $this->Addresses->patchEntity($address, [
                'user_id' => $uid,
                'address' => $text
            ]);
            $this->Addresses->save($address);
            $this->set([
                'address' => $address,
                'code' => 200,
                '_serialize' => ['address', 'code']
            ]);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Address id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $address = $this->Addresses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $address = $this->Addresses->patchEntity($address, $this->request->getData());
            if ($this->Addresses->save($address)) {
                $this->Flash->success(__('The address has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The address could not be saved. Please, try again.'));
        }
        $users = $this->Addresses->Users->find('list', ['limit' => 200]);
        $this->set(compact('address', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Address id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $address = $this->Addresses->get($id);
        if ($this->Addresses->delete($address)) {
            $this->Flash->success(__('The address has been deleted.'));
        } else {
            $this->Flash->error(__('The address could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
