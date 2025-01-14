<?php

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('Notification', 'Model');

/**
 * PHKAPA Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  PHKAPA.app.Controller.Component
 * @version  V1
 * @author   Paulo Homem <contact@phalkaline.eu>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://phkapa.net
 */
class NotifyComponent extends Component {

    /**
     * Other components required by NotifyComponent
     *
     * @var array
     * @access public
     */
    public $components = array('Session');

    /**
     * Parameter data from Controller::$params
     *
     * @var array
     * @access public
     */
    public $params = array();

    /**
     * Controller requesting notification
     *
     * @var Controller
     * @access public
     */
    private $_requester = null;

    /**
     * Model
     *
     * @var Controller
     * @access public
     */
    private $_model = null;

    /**
     * startup
     * called after Controller::beforeFilter()
     * 
     * @param object $controller instance of controller
     * @return void
     * @access public
     */
    public function startup(Controller $controller) {
        $this->_requester = $controller;
        $this->_model = new Notification();
        return;
    }

    /**
     * getNotifications
     * get notifications for notified
     * 
     * @access public
     * @return boolean
     * 
     */
    public function getNotifications($notified_id = null) {
        if ($notified_id == null) {
            return;
        }
        $this->_model->recursive=1;
        return $this->_model->find('all', array('conditions' => array('notified_id' => $notified_id)));
    }

    /**
     * count unread notifications
     * 
     * @access public
     * @return boolean
     * 
     */
    public function countNotifications($notified_id = null) {
        if ($notified_id == null) {
            return;
        }
        return $this->_model->find('count', array('conditions' => array('notified_id' => $notified_id, 'read' => '0')));
    }

    /**
     * addNotification
     * add notification
     * 
     * @access public
     * @return boolean
     * 
     */
    public function addNotification($notification, $email = null) {
        
        if (!empty($notification)) {
            $this->_model->create();
            if ($this->_model->save($notification)) {
                if (!empty($email)) {
                    $this->_emailNotify($this->_model->read(), $email);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * changeStatus
     * change notification status ( unread , read )
     * 
     * @access public
     * @return boolean
     * 
     */
    public function changeStatus($id = null, $status = 0) {
        $this->_model->read(null, $id);
        $this->_model->set('status', $status);
        $this->_model->save();
    }

    public function setAllNotificationsReadForNotified($id) {
        $this->_model->UpdateAll(array('read' => true), array('notified_id' => $id));

        return;
    }

    /**
     * delete
     * delete notification
     * 
     * @access public
     * @return boolean
     * 
     */
    public function delete($id, $notified_id) {
        if (!$id || !$notified_id) {
            //$this->Session->setFlash(__('Invalid request.'), 'flash_message_error');
            return;
        }
        if ($this->_model->deleteAll(array($this->_model->name . '.id' => $id, $this->_model->name . '.notified_id' => $notified_id), false)) {
            //$this->Session->setFlash(__('Deleted with success.'), 'flash_message_info');
            return;
        }
        //$this->Session->setFlash(__( 'Could not be deleted.'), 'flash_message_error');
        return;
    }

    /**
     * _emailNotify
     * Send notification by email
     *
     * @return void
     * @access protected
     */
    protected function _emailNotify($notification, $email) {
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail('default');
        $Email->template('notification','phkapa')->emailFormat('html');
        $Email->viewVars(array('notification' => $notification,'email'=>$email));
        // uncoment these lines to get more control over sender from and subject!!
        //$Email->sender('noreply-contacta@phkapa.net', 'CONTACTA - PHKAPA');
        //$Email->from(array('noreply-contacta@phkapa.net' => 'CONTACTA - PHKAPA'));
        //$Email->subject(__('PHKAPA').' '.__n('Notification', 'Notifications', 1));
        
        $Email->to($email);
        $Email->send();
        
    }

}

?>