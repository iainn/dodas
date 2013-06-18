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
class Advancedactivity_View_Helper_GetRichContent extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getRichContent($item) {
	if(!$item)
	return;
    switch ($item->getType()) {
      case 'poll':
        $view = Zend_Registry::get('Zend_View');
        $view = clone $view;
        $view->clearVars();
        $view->addScriptPath('application/modules/Poll/views/scripts/');

        $content = '';
        $content .= '
					<div class="feed_poll_rich_content">
						<div class="feed_item_link_title">
							' . $view->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity())) . '
						</div>
						<div class="feed_item_link_desc">
							' . $view->viewMore($item->getDescription()) . '
						</div>
				';

        // Render the thingy
        $view->poll = $item;
        $view->owner = $owner = $item->getOwner();
        $view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $view->pollOptions = $item->getOptions();
        $view->hasVoted = $item->viewerVoted();
        $view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
        $view->canVote = $item->authorization()->isAllowed(null, 'vote');
        $view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
        $view->hideLinks = true;

        $content .= $view->render('_poll.tpl');

        /* $content .= '
          <div class="poll_stats">
          '; */

        $content .= '
					</div>
				';
        break;

      case 'video';
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;
        $view = false;
        $params = array();

        if (strtolower($item->getModuleName()) == 'ynvideo') {
          //compitable with younet advanced video.
          $paramsForCompile = array_merge(array(
              'video_id' => $item->video_id,
              'code' => $item->code,
              'view' => $view,
              'mobile' => $mobile,
              'duration' => $item->duration
                  ), $params);
          if ($item->type == Ynvideo_Plugin_Factory::getUploadedType()) {
            $paramsForCompile['location'] = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
          }

          $videoEmbedded = Ynvideo_Plugin_Factory::getPlugin((int) $item->type)->compileVideo($paramsForCompile);
        } else {
          // if video type is youtube
          if ($item->type == 1) {
            $videoEmbedded = $item->compileYouTube($item->video_id, $item->code, $view, $mobile);
          }
          // if video type is vimeo
          if ($item->type == 2) {
            $videoEmbedded = $item->compileVimeo($item->video_id, $item->code, $view, $mobile);
          }

          // if video type is uploaded
          if ($item->type == 3) {
            $video_location = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
            $videoEmbedded = $item->compileFlowPlayer($video_location, $view);
          }
        }
        // $view == false means that this rich content is requested from the activity feed
        if ($view == false) {

          // prepare the duration
          //
      $video_duration = "";
          if ($item->duration) {
            if ($item->duration >= 3600) {
              $duration = gmdate("H:i:s", $item->duration);
            } else {
              $duration = gmdate("i:s", $item->duration);
            }
            $duration = ltrim($duration, '0:');

            $video_duration = "<span class='video_length'>" . $duration . "</span>";
          }

          // prepare the thumbnail
          $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');

          if ($item->photo_id) {
            $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');
          } else {
            $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
          }

          if (!$mobile) {
            $thumb = '<a id="video_thumb_' . $item->video_id . '" style="" href="javascript:void(0);" onclick="javascript:var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
          } else {
            $thumb = '<a id="video_thumb_' . $item->video_id . '" class="video_thumb" href="javascript:void(0);" onclick="javascript: $(\'videoFrame' . $item->video_id . '\').style.display=\'block\'; $(\'videoFrame' . $item->video_id . '\').src = $(\'videoFrame' . $item->video_id . '\').src; var myElement = $(this); myElement.style.display=\'none\'; var next = myElement.getNext(); next.style.display=\'block\';">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
          }

          // prepare title and description
          $title = "<a class='sea_add_tooltip_link feed_video_title' rel= \"" . $item->getType() . ' ' . $item->getIdentity() . "\" href='" . $item->getHref($params) . "' >$item->title</a>";
          $tmpBody = strip_tags($item->description);
          $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

          $videoEmbedded = $thumb . '<div id="video_object_' . $item->video_id . '" class="video_object">' . $videoEmbedded . '
            </div><div class="video_info">' . $title . $description . '</div>';
        } $content = $videoEmbedded;
        break;

      case 'avp_video':
        $view = false;
        $params = array();
       $viewer = Engine_Api::_()->user()->getViewer();
            $group = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('avp_video', $item->video_id, 'auth_view');
            
            $video_hide = "<div id='avp-video-{$item->video_id}' style='display: none;'></div>";

            if ($item->hasGroupPrivacy() && !in_array($viewer->getIdentity(), array_merge(explode(";", $item->can_view), array($item->owner_id))))
            {
                  $video_hide  .= <<<EOT
<script type="text/javascript">
//<![CDATA[
avp.hide.id({$item->video_id});
//]]>
</script>   
EOT;
            }
            
            $override = array(
                  'width' => 464,
                  'height' => 261,
                  'autoplay' => false,
                  'autostart' => false
            );

            $description = "<div class='avp_desc'>".$item->getShortDescription()."</div>";

            $videoEmbedded = "<div class='video_info'><a href='".$item->getHref($params)."'>{$item->title}</a></div>".$item->getPlayer(false, true, $override)."<div class='video_info'>{$description}</div>";

            $avp_duration = "";
                  
            if ($item->duration)
            {
                  if ($item->duration>3600 ) $duration = gmdate("H:i:s", $item->duration);
                  else $duration = gmdate("i:s", $item->duration);
                        
                  if ($duration[0] == '0') $duration = substr($duration,1);
                  if (count(explode(":", $duration)) > 2 && substr($duration, 0, 2) == "0:") $duration = substr($duration ,2);
                        
                  $avp_duration = "<span class='avp_length'>{$duration}</span>";
            }
                  
            $thumb = "<div class='avp_thumb_wrapper'><div class='avp_thumb_wrapper_play' onclick=\"avpGetById('avp-feed-item-{$item->video_id}').innerHTML=avpUrlDecode('".urlencode($videoEmbedded)."')\"></div>{$avp_duration}".$item->getThumbnail()."</div>";
            $title = "<a href='".$item->getHref($params)."' class='sea_add_tooltip_link feed_video_title' rel= \"" . $item->getType() . ' ' . $item->getIdentity() . "\" >{$item->title}</a>";
            $description = "<div class='avp_desc'>".$item->getShortDescription()."</div>";

            $videoEmbedded = '<div id="avp-feed-item-'.$item->getIdentity().'">'.$thumb.'<div class="video_info">'.$title.$description.'</div></div>';
            
            return $video_hide.$videoEmbedded;

        $content = $video_hide . $videoEmbedded;
        break;
      default:
        $content = $item->getRichContent();
    }
    return $content;
  }

}
