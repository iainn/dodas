<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_View_Helper_GetContent extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getContent($action, $asAttachment=false, $groupedFeeds = array()) {

    $view = Zend_Registry::get('Zend_View');

    $model = Engine_Api::_()->getApi('activity', 'advancedactivity');
    $params = array_merge(
            $action->toArray(), (array) $action->params, array(
        'subject' => $action->getSubject(),
        'object' => $action->getObject()
            )
    );
    //$content = $model->assemble($this->body, $params);
    $content = $model->assemble($action->getTypeInfo()->body, $params);

    /* Start Working group feed. */
    if ($action->type == 'friends' || $action->type == 'tagged') {
      $subject = $action->getObject();
      $id = $action->getSubject()->getIdentity();
    } else {
      $subject = $action->getSubject();
      $id = $action->getObject()->getIdentity();
    }

    $removePattern = '<a '
            . 'class="feed_item_username sea_add_tooltip_link feed_user_title" '
            . 'rel="' . $subject->getType() . ' ' . $subject->getIdentity() . '" '
            . ( $subject->getHref() ? 'href="' . $subject->getHref() . '"' : '' )
            . '>'
            . $subject->getTitle()
            . '</a>';
    $count = count($groupedFeeds);

    $otherids = array();
    $gp = array();
    if (!empty($groupedFeeds)) {
			foreach ($groupedFeeds as $groupedFeed):
				$gp[] = $groupedFeed;
			endforeach;
    }
    for ($i = 0; $i < count($gp) - 1; $i++):
      $otherids[] = $gp[$i]->getIdentity();
    endfor;
    

    $ids = http_build_query(array("type" => $action->type, "ids" => $otherids), '', '&');

    if ($count == 2) :
      $new_pattern = $view->translate('%1$s and %2$s ', $view->htmlLink($gp['0']->getHref(), $gp['0']->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $gp['0']->getIdentity())), $view->htmlLink($gp['1']->getHref(), $gp['1']->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $gp['1']->getIdentity())));
    elseif ($count > 2) :
      $URL = $view->url(array('module' => 'advancedactivity', 'controller' => 'feed', 'action' => 'groupfeed-other-post'), 'default', true) . "?$ids";

      $otherPeoples = '<span class="aaf_feed_show_tooltip_wrapper"><a href=' . $URL . ' class="smoothbox">' . $view->translate('%s others', ($count - 1)) . '</a><span class="aaf_feed_show_tooltip" style="margin-left:-8px;"><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/images/tooltip_arrow.png" />';
      for ($i = 1; $i < count($gp); $i++):
        $otherPeoples.= $gp[$i]->getTitle() . "<br />";
      endfor;
      $otherPeoples.='</span></span>';
      $end = end($gp);
      $new_pattern = $view->translate('%1$s and %2$s ', $view->htmlLink($subject->getHref(), $subject->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $subject->getIdentity())), $otherPeoples);
    endif;
    
    if ($count == 2 || $count > 2) {
			if (strpos($action->type, "like_") !== false) {
				$removePattern = $removePattern . $view->translate(' likes');
				$new_pattern = $new_pattern . $view->translate('like ');
			}
    }

    if (!empty($new_pattern)) {
      $content = str_replace($removePattern, $new_pattern, $content);
    }
    /* End Working group feed. */


    if ((false !== strpos($action->type, 'post')) || (false !== strpos($action->type, 'status')) || $action->type === 'sitetagcheckin_checkin' || $action->type === 'comment_sitereview_listing' || $action->type === 'comment_sitereview_review' || $action->type === 'nestedcomment_sitereview_listing' || $action->type === 'nestedcomment_sitereview_review') {
      if (!empty($action->body))
        $content = nl2br($content);

      $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));
      if (empty($composerOptions))
        $composerOptions = array();
      if (in_array("emotions", $composerOptions) && Zend_Registry::isRegistered('Zend_View')) {
        $view = Zend_Registry::get('Zend_View');
        $content = $view->smileyToEmoticons($content);
      }
      //$content = Engine_API::_()->seaocore()->smiley2emoticons(nl2br($content));

      $actionParams = (array) $action->params;
      if (isset($actionParams['tags'])) {
        foreach ((array) $actionParams['tags'] as $key => $tagStrValue) {

          $tag = Engine_Api::_()->getItemByGuid($key);
          if (!$tag) {
            continue;
          }
          $replaceStr = '<a class="sea_add_tooltip_link" '
                  . 'href="' . $tag->getHref() . '" '
                  . 'rel="' . $tag->getType() . ' ' . $tag->getIdentity() . '" >'
                  . $tag->getTitle()
                  . '</a>';
          $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);

        }
      }
      if (!$asAttachment && in_array("withtags", $composerOptions)) {
        $tagContent = Engine_Api::_()->advancedactivity()->getTagContent($action);
        $content .=$tagContent;
      }
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin'))
        $content = $this->view->getSitetagCheckin($action, $content);
    } else if (($action->type == 'sitetagcheckin_add_to_map') || ($action->type == 'sitetagcheckin_content') || ($action->type == 'sitetagcheckin_lct_add_to_map')) {
        $tagContent = Engine_Api::_()->advancedactivity()->getTagContent($action);
        $content .=$tagContent;
      $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));
      if (empty($composerOptions))
        $composerOptions = array();
      if (in_array("emotions", $composerOptions) && Zend_Registry::isRegistered('Zend_View')) {
        $view = Zend_Registry::get('Zend_View');
        $content = $view->smileyToEmoticons($content);
        $content = nl2br($content);
      }
    } else {
      $content = nl2br($content);
    }
    return $content;
  }

}