<?php

class ExhibitAccess_IndexController extends Omeka_Controller_AbstractActionController
{
    
    public function indexAction()
    {
        $exhibits = $this->_helper->db->getTable('Exhibit')->findAll();
        $usersForSelect = get_table_options('User');
        
        
        if ($this->getRequest()->isPost()) {
            $this->setUsersForExhibit($_POST);
        }
        
        $this->view->exhibits = $exhibits;
        $this->view->usersForSelect = $usersForSelect;
    }
    
    protected function setUsersForExhibit($post)
    {
        $db = get_db();
        $userTable = $db->getTable('User');
        $accessTable = $db->getTable('ExhibitAccess');
        foreach ($post['exhibits'] as $exhibitId => $userIds) {

            if (empty($userIds)) {
                $users = array();
            } else {
                $userSelect = $userTable->getSelect();
                $userSelect->where("id IN (?)", $userIds);
                $users = $userTable->fetchObjects($userSelect);
            }
            foreach ($users as $user) {
                // don't demote supers or admins
                if ($user->role != 'super' && $user->role != 'admin') {
                    $user->role = 'Exhibit Access Contributor';
                    $user->save();
                }
                $accessRecords = $accessTable->findBy(array('user_id' => $user->id,
                                                            'exhibit_id' => $exhibitId));
                if (empty($accessRecords)) {
                    $accessRecord = new ExhibitAccess();
                    $accessRecord->user_id = $user->id;
                    $accessRecord->exhibit_id = $exhibitId;
                    $accessRecord->save();
                }
            }
            
            //clean out users who have had access revoked
            $select = $accessTable->getSelect();
            $select->where("exhibit_id = ?", $exhibitId);
            if (! empty($userIds)) {
                $select->where("user_id NOT IN (?)", $userIds);
            }
            
            $oldAccessRecords = $accessTable->fetchObjects($select);
            foreach ($oldAccessRecords as $oldAccessRecord) {
                $oldAccessRecord->delete();
            }
        }
    }
}
