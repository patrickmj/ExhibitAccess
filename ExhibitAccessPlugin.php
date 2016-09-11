<?php

class ExhibitAccessPlugin extends Omeka_Plugin_AbstractPlugin
{
    public $_hooks = array(
            'install',
            'uninstall',
            'define_acl',
            'admin_head',
            );
    
    public $_filters = array(
            'admin_navigation_main'
            );
    
    public function hookInstall() 
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->ExhibitAccess` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `exhibit_id` int(10) unsigned NOT NULL,
              `user_id` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);
    }
    
    public function hookUninstall()
    {
        $db = $this->_db;
        $sql = "DROP TABLE IF EXISTS `$db->ExhibitAccess`";
        $db->query($sql);
    }
    
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addRole('Exhibit Access Contributor', 'contributor');
        $acl->allow('Exhibit Access Contributor',
                    'ExhibitBuilder_Exhibits',
                    array('edit', 'showNotPublic'),
                    new ExhibitAccessAclAssertion
                    );
        
    }
    
    public function hookAdminHead()
    {
        queue_css_file('exhibit-access');
    }
    
    public function filterAdminNavigationMain($nav)
    {
            $nav[] = array('label' => __('Exhibit Access'),
               'uri'   => url('exhibit-access')
            );
        return $nav;
    }
    
}
