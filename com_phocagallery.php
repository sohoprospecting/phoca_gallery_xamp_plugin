<?php
/**
* @author Rodrigo Kammer - rodrigo.kammer@sohoprospecting.com
* @version 1.0
* @package xmap
* @license GNU/GPL
* @authorSite http://sohoprospecting.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for phoca gallery categories to Xmap */
class xmap_com_phocagallery {

    static $sectionConfig = array();

    static protected function getPictures(){
        $db =& JFactory::getDBO();

        $db->setQuery("SELECT `pic`.`id`    as `picid`,    `pic`.`title` as `pictitle`, `pic`.`date` as `picdate`, ".
                      "       `pic`.`alias` as `picalias`, `pic`.`catid` as `catid`,                               ".
                      "       `cat`.`title` as `cattitle`, `cat`.`alias` as `catalias`                             ".
                      "  FROM `#__phocagallery` AS `pic` INNER JOIN `#__phocagallery_categories` AS `cat` ON `cat`.`id` = `pic`.`catid`");

        return $db->loadObjectList();
    }

    /** Get the content tree for this kind of content */
    function getTree( $xmap, $parent, &$params ) {

        $list = array();

        if($xmap->isNews){
            return false;
        }

        $priority   = (-1 == $params['entry_priority'])   ? $parent->priority   : $params['entry_priority'];
        $changefreq = (-1 == $params['entry_changefreq']) ? $parent->changefreq : $params['entry_changefreq'];

        $pictures = self::getPictures();

        $xmap->changeLevel(1);
        foreach($pictures as $picture){
            $url = '';
            $node = new stdclass;
            $node->id         = $parent->id;
            $node->uid        = $parent->uid.$picture->{'picid'};
            $node->browserNav = $parent->browserNav;
            $node->name       = html_entity_decode(stripslashes($picture->{'pictitle'}));
            $node->modified   = strtotime($picture->{'picdate'});
 			$node->link       = 'index.php?option=com_phocagallery&amp;view=detail&amp;catid='.$picture->{'catid'}.':'.$picture->{'catalias'}.'&amp;id='.$picture->{'picid'}.':'.$picture->{'picalias'}.'&amp;Itemid='.$parent->id;
            $node->priority   = $priority;
            $node->changefreq = $changefreq;
            $node->expandible = false;
            $xmap->printNode($node);
        }

        $xmap->changeLevel(-1);

        return $list;

    }


}
