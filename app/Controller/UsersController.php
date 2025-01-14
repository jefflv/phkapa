<?php

/**
 * Users controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  PHKAPA
 * @version  V1
 * @author   Paulo Homem <contact@phalkaline.eu>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://phkapa.net
 */
class UsersController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Users';

    /**
     * Components
     *
     * @var array
     * @access public
     */
    public $components = array('Acl');

    /**
     * Admin index
     *
     * @return void
     * @access public
     */
    public function admin_index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    /**
     * Admin view
     *
     * @param integer $id
     * @return void
     * @access public
     */
    public function admin_view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    /**
     * Admin add
     *
     * @return void
     * @access public
     */
    public function admin_add() {
        if (!empty($this->request->data)) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Saved with success.'), 'flash_message_info');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Could not be saved. Please, try again.'), 'flash_message_error');
            }
        }
    }

    /**
     * Admin edit
     *
     * @param integer $id
     * @return void
     * @access public
     */
    public function admin_edit($id = null) {
        if ($id == null ) {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {

            // On user edits must also change alias on AROS/ACOS
            // Restrict alias ( Aro alias field must be unique ) 

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Saved with success.'), 'flash_message_info');
                // Update aro/request access alias ('name')
                // Force login data if user is same
                if ($this->Auth->user('id') == $id) {
                    $this->Auth->login($this->request->data['User']);
                }

                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Could not be saved. Please, try again.'), 'flash_message_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
        if (!empty($this->Acl->Aro->validationErrors)) {
            $this->User->validationErrors['name'][] = $this->Acl->Aro->validationErrors['alias'][0];
        }
    }

    /**
     * Edit User Profile
     *
     * @return void
     * @access public
     */
    public function edit_profile() {
        if (!$this->Auth->loggedIn() || Configure::read('Application.mode') == 'demo') {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(Router::url('/', true));
        }

        $id = $this->Auth->user('id');

        if (!empty($this->request->data)) {

            // On user edits must also change alias on AROS/ACOS
            // Restrict alias ( Aro alias field must be unique ) 

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Saved with success.'), 'flash_message_info');
                // Update aro/request access alias ('name')
                // Force login data if user is same
                if ($this->Auth->user('id') == $id) {
                    $this->Auth->login($this->request->data['User']);
                }

                $this->redirect(array('action' => 'edit_profile'));
            } else {
                $this->Session->setFlash(__('Could not be saved. Please, try again.'), 'flash_message_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
        if (!empty($this->Acl->Aro->validationErrors)) {
            $this->User->validationErrors['name'][] = $this->Acl->Aro->validationErrors['alias'][0];
        }
    }

    /**
     * Admin delete
     *
     * @param integer $id
     * @return void
     * @access public
     */
    public function admin_delete($id = null) {
        if ($id == null || $id == 1) {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->delete($id)) {
            $this->Session->setFlash(__('Deleted with success.'), 'flash_message_info');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Could not be deleted.'), 'flash_message_error');
        $this->redirect(array('action' => 'index'));
    }

    /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     *
     * @return void
     * @access public
     */
    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                if ($this->data['User']['language'] != '') {
                    $this->Session->write('User.language', $this->data['User']['language']);
                }
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Login failed. Invalid username or password.'), 'flash_message_error', array(), 'auth');
            }
        }
    }

    /**
     *  The AuthComponent provides the needed functionality
     *  for logout, so you can leave this function blank.
     *
     * @return void
     * @access public
     */
    public function logout() {
        $this->Session->delete('User.language');
        $this->redirect($this->Auth->logout());
    }

    /**
     * secure_password
     *
     * @param integer $id
     * @return void
     * @access private
     */
    private function secure_password() {

        $this->User->read(null, 1);
       
        if (empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }




        if (!empty($this->request->data)) {

            // On user edits must also change alias on AROS/ACOS
            // Restrict alias ( Aro alias field must be unique ) 

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Saved with success.'), 'flash_message_info');
                // Update aro/request access alias ('name')
                // Force login data if user is same
                if ($this->Auth->user('id') == $id) {
                    $this->Auth->login($this->request->data['User']);
                }

                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Could not be saved. Please, try again.'), 'flash_message_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
        if (!empty($this->Acl->Aro->validationErrors)) {
            $this->User->validationErrors['name'][] = $this->Acl->Aro->validationErrors['alias'][0];
        }
    }

}

?>