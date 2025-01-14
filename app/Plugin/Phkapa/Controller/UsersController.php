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
class UsersController extends PhkapaAppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Users';
     
     /**
     * Admin index
     *
     * @return void
     * @access public
     */
     public function admin_index() {
        $allowedUsers=$this->_allowedUsers();
        $this->set(compact('allowedUsers'));
        $this->User->recursive = 0;
        $this->paginate = array('conditions' => array ('User.id' => $allowedUsers));
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
            $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        if (!in_array($id, $this->_allowedUsers())){
            $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        $this->User->recursive=1;
        $this->set('user', $this->User->find('first', array('conditions'=>array('User.id'=>$id))));
    }

    /**
     * Admin add
     *
     * @return void
     * @access public
     */ 
    public function admin_add() {
        $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
        $this->redirect(array('action' => 'index'));
       
    }

    /**
     * Admin edit
     *
     * @param integer $id
     * @return void
     * @access public
     */ 
    public function admin_edit($id = null) {
        
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        if (!in_array($id, $this->_allowedUsers())){
            $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__d('phkapa','Saved with success.'),'flash_message_info');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__d('phkapa','Could not be saved. Please, try again.'),'flash_message_error');
            }
        }
        if (empty($this->request->data)) {
            $this->User->recursive=1;
            $this->request->data = $this->User->find('first', array('conditions'=>array('User.id'=>$id)));
            if (empty($this->request->data)) {
                $this->Session->setFlash(__d('phkapa','Invalid request.'),'flash_message_error');
                $this->redirect(array('action' => 'index'));
            }
        }
        $processes = $this->User->Process->find('list');
        $this->set(compact('processes'));
    }

    /* public function beforeFilter() {
        parent::beforeFilter();
        
    }

     public function beforeRender() {
        parent::beforeRender();
        //$this->_allowedUsers();
        
    }*/
    
    /**
     * List of allowed users with access to phkapa plugin
     *
     * @return array $allowedUsers
     * @access protected
     */ 
    protected function _allowedUsers() {
        $allowedUsers=array();
        $recursiveValue=$this->User->recursive;
        $this->User->recursive = 0;
        $users = $this->User->find('all', array('fields' => array('id', 'name')));
        $this->User->recursive = $recursiveValue;
        foreach ($users as $user):
            if ($this->checkAccess('phkapa',$user['User']['name'])){
                $allowedUsers[]=$user['User']['id'];
            }
            
        endforeach;
        return $allowedUsers;
        
    }

}

?>