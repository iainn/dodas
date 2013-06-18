<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _activityText.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $sharesTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity'); ?>
<?php
if (empty($this->actions)) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
  $actions = $this->actions;
}
?>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
?>
<script type="text/javascript">
  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
    en4.core.language.addData({
      "Stories from %s are hidden now and will not appear in your Activity Feed anymore.":"<?php echo $this->string()->escapeJavascript($this->translate("Stories from %s are hidden now and will not appear in your Activity Feed anymore.")); ?>"
    });
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            //type : 'core_comment',
            action_id : el.getParent('li').getParent('li').getParent('li').get('id').match(/\d+/)[0],
            comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 20,
        'y' : 10
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();     
  });   
</script>

<?php $advancedactivityCoreApi = Engine_Api::_()->advancedactivity();
$advancedactivitySaveFeed = Engine_Api::_()->getDbtable('saveFeeds', 'advancedactivity'); ?>
<?php
foreach ($actions as $action): // (goes to the end of the file)
  try { // prevents a bad feed item from destroying the entire page
    // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
    if (!$action->getTypeInfo()->enabled)
      continue;
    if (!$action->getSubject() || !$action->getSubject()->getIdentity())
      continue;
    if (!$action->getObject() || !$action->getObject()->getIdentity())
      continue;
    ob_start();
    ?>
    <?php $item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject(); ?>

    <?php $this->commentForm->setActionIdentity($action->action_id) ?>
    <script type="text/javascript">
      (function(){
        var action_id = '<?php echo $action->action_id ?>';
        en4.core.runonce.add(function(){
          $('activity-comment-body-' + action_id).autogrow();  
          var allowQuickComment= '<?php echo ($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>';
          en4.advancedactivity.attachComment($('activity-comment-form-' + action_id),allowQuickComment);
                                                              
          if(allowQuickComment == '1' && <?php echo $this->submitComment ? '1' : '0' ?>){
            document.getElementById("<?php echo $this->commentForm->getAttrib('id') ?>").style.display = "";
            document.getElementById("<?php echo $this->commentForm->submit->getAttrib('id') ?>").style.display = "none";
            if(document.getElementById("feed-comment-form-open-li_<?php echo $action->action_id ?>")){
              document.getElementById("feed-comment-form-open-li_<?php echo $action->action_id ?>").style.display = "none";}  
            document.getElementById("<?php echo $this->commentForm->body->getAttrib('id') ?>").focus();
          }
        });
      })();
    </script>   

    <?php if ($this->allowEdit && !empty($action->privacy) && in_array($action->getTypeInfo()->type, array("post", "post_self", "status", 'sitetagcheckin_add_to_map', 'sitetagcheckin_content', 'sitetagcheckin_status', 'sitetagcheckin_post_self', 'sitetagcheckin_post', 'sitetagcheckin_checkin', 'sitetagcheckin_lct_add_to_map')) && $this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id))): ?>


      <?php $privacy = $action->privacy; ?>
      <?php if (in_array($privacy, array("everyone", "networks", "friends", "onlyme"))): ?>
        <?php
        $privacy_icon_class = "aaf_icon_feed_" . $privacy;
        if (isset($this->privacyDropdownList[$privacy])):
          $privacy_titile = $this->privacyDropdownList[$privacy];
        endif;
        ?>
      <?php else: ?>
        <?php
        $privacy_array = explode(",", $privacy);
        foreach ($privacy_array as $value):
          if (isset($this->privacyDropdownList[$value])):
            $privacy_titile_array[] = $this->privacyDropdownList[$value];
          endif;
        endforeach;
        ?>
        <?php
        $privacy_icon_class = (count($privacy_titile_array) > 1) ? "aaf_icon_feed_custom" : "aaf_icon_feed_list";
        $privacy_titile = join(", ", $privacy_titile_array);
        ?>
      <?php endif; ?>
    <?php endif; ?>



    <?php // Icon, time since, action links ?>
    <?php
    $icon_type = 'activity_icon_' . $action->type;
    list($attachment) = $action->getAttachments();
    if (is_object($attachment) && $action->attachment_count > 0 && $attachment->item):
      $icon_type .= ' item_icon_' . $attachment->item->getType() . ' ';
    endif;
    $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
            !empty($this->commentForm) );
    ?>

    <?php if (is_array($action->params) && isset($action->params['checkin']) && !empty($action->params['checkin'])): ?>
      <?php if ($action->params['checkin']['type'] == 'Page'): ?>
        <?php $icon_type = "item_icon_sitepage"; ?>
      <?php elseif ($action->params['checkin']['type'] == 'Business'): ?>
        <?php $icon_type = "item_icon_sitebusiness"; ?>
      <?php else: ?>
        <?php $icon_type = "item_icon_sitetagcheckin"; ?>
      <?php endif; ?>
    <?php endif; ?>

    <div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
      <ul>         
        <?php if ($canComment): ?>
          <?php if ($action->likes()->isLike($this->viewer())): ?>
            <li class="feed_item_option_unlike">              
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick' => 'javascript:en4.advancedactivity.unlike(' . $action->action_id . ');')) ?>
              <span>&#183;</span>
            </li>
          <?php else: ?>
            <li class="feed_item_option_like">              	
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick' => 'javascript:en4.advancedactivity.like(' . $action->action_id . ');', 'title' =>
                  $this->translate('Like this item')))
              ?>
              <span>&#183;</span>
            </li>
          <?php endif; ?>
          <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes   ?>
            <li class="feed_item_option_comment">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Comment'), array(
                  'class' => 'smoothbox', 'title' => $this->translate('Leave a comment')
              ))
              ?>
              <span>&#183;</span>
            </li>
          <?php else: ?>
            <li class="feed_item_option_comment">                
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick' => 'document.getElementById("' . $this->commentForm->getAttrib('id') . '").style.display = "";
document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
 if(document.getElementById("feed-comment-form-open-li_' . $action->action_id . '")){
document.getElementById("feed-comment-form-open-li_' . $action->action_id . '").style.display = "none";}  
document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();', 'title' =>
                  $this->translate('Leave a comment')))
              ?>
              <span>&#183;</span>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (in_array($action->getTypeInfo()->type, array('signup', 'friends', 'friends_follow'))): ?>    
          <?php $userFriendLINK = $this->aafUserFriendshipAjax($action); ?>
          <?php if ($userFriendLINK): ?>
            <li class="feed_item_option_add_tag"><?php echo $userFriendLINK; ?>
              <span>&#183;</span></li>  
          <?php endif; ?>
        <?php endif; ?>    
        <?php
        if ($this->viewer()->getIdentity() && (
                'user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) && $advancedactivityCoreApi->hasFeedTag($action)
        ):
          ?>
          <li class="feed_item_option_add_tag">             
            <?php
            echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'advancedactivity',
                'controller' => 'feed',
                'action' => 'tag-friend',
                'id' => $action->action_id
                    ), $this->translate('Tag Friends'), array('class' => 'smoothbox', 'title' =>
                $this->translate('Tag more friends')))
            ?>
            <span>&#183;</span>
          </li>
        <?php elseif ($this->viewer()->getIdentity() && $advancedactivityCoreApi->hasMemberTagged($action, $this->viewer())): ?>  
          <li class="feed_item_option_remove_tag">             
            <?php
            echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'advancedactivity',
                'controller' => 'feed',
                'action' => 'remove-tag',
                'id' => $action->action_id
                    ), $this->translate('Remove Tag'), array('class' => 'smoothbox'))
            ?>
            <span>&#183;</span>
          </li>
        <?php endif; ?>


        <?php // Share ?>
        <?php if ($action->getTypeInfo()->shareable && $action->shareable && $this->viewer()->getIdentity()): ?>
          <?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
            <li class="feed_item_option_share">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' =>
                  $attachment->item->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', "not_parent_refresh" => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 2): ?>
            <li class="feed_item_option_share">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' =>
                  $subject->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', "not_parent_refresh" => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 3): ?>
            <li class="feed_item_option_share">                
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $object->getType(), 'id' =>
                  $object->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 4): ?>
            <li class="feed_item_option_share">                
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $action->getType(), 'id' =>
                  $action->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <li>
          <?php echo $this->timestamp($action->getTimeValue()) ?>             
        </li>
        <?php if (!empty($privacy_icon_class) && !empty($privacy_titile)): ?>
          <li>
            <span>&#183;</span>
            <span class = "<?php echo $privacy_icon_class ?> feed_item_privacy">
              <p class="adv_item_privacy_tip">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                <?php echo $this->translate("Shared with %s", $this->translate($privacy_titile)) ?>
              </p>
            </span>            
          </li>
        <?php endif; ?>
      </ul>
    </div>


    <?php if (($action->getTypeInfo()->shareable && $action->shareable && ($share = $sharesTable->countShareOfItem(array('parent_action_id' => $action->getIdentity()))) > 0) || ($action->getTypeInfo()->commentable && $action->commentable)) : // Comments - likes -share    ?>
      <div class='comments'>
        <ul>  
          <?php // Share Count  ?>
          <?php if ($action->getTypeInfo()->shareable && $action->shareable && $share > 0): ?>       
            <li class="aaf_share_counts">
              <div></div>
              <div class="comments_likes">
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share-item', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate(array('%s share', '%s shares', $share), $this->locale()->toNumber($share)), array('class' => 'smoothbox seaocore_icon_share aaf_commentbox_icon')) ?>
              </div>
            </li>
          <?php endif; ?>


          <?php if ($action->getTypeInfo()->commentable && $action->commentable): // Comments - likes -share ?>       
            <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
              <li>
                <div></div>
                <div class="comments_likes">
                  <?php if ($action->likes()->getLikeCount() <= 3 || $this->viewAllLikes): ?>
                    <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->aafFluentList($action->likes()->getAllLikesUsers())) ?>

                  <?php else: ?>
                    <?php
                    echo $this->htmlLink($item->getHref(array('action_id' => $action->action_id, 'show_likes' => true)), $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()))
                    )
                    ?>
                  <?php endif; ?>
                </div>
              </li>
            <?php endif; ?>          
            <?php if ($action->comments()->getCommentCount() > 0): ?>
              <?php if ($action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
                <li>
                  <div></div>
                  <div class="comments_viewall">
                    <?php if ($action->comments()->getCommentCount() > 2): ?>
                      <?php
                      echo $this->htmlLink($item->getHref(array('action_id' => $action->action_id, 'show_comments' => true)), $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())))
                      ?>
                    <?php else: ?>
                      <?php
                      echo $this->htmlLink('javascript:void(0);', $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())), array('onclick' => 'en4.advancedactivity.viewComments(' . $action->action_id . ');'))
                      ?>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endif; ?>
              <?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
                <li id="comment-<?php echo $comment->comment_id ?>">
                  <div class="comments_author_photo">
                    <?php
                    echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                    )
                    ?>
                  </div>
                  <div class="comments_info">
                    <span class='comments_author'>
                      <?php
                      echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                      );
                      ?>
                      <?php
                      if ($this->viewer()->getIdentity() &&
                              (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                              ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id) ||
                              ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($this->viewer())) ||
                              $this->activity_moderate )):
                        ?>


                        <?php /* echo $this->htmlLink(array(
                          'route'=>'default',
                          'module'    => 'advancedactivity',
                          'controller'=> 'index',
                          'action'    => 'delete',
                          'action_id' => $action->action_id,
                          'comment_id'=> $comment->comment_id,
                          ),'', array('class' => 'smoothbox
                          aaf_icon_remove','title'=>$this->translate('Delete Comment'))) */ ?>
                        <a href="javascript:void(0);" class="aaf_icon_remove" title="<?php echo
              $this->translate('Delete Comment') ?>" onclick="deletefeed('<?php echo
              $action->action_id ?>', '<?php echo $comment->comment_id ?>', '<?php
              echo
              $this->escape($this->url(array('route' => 'default',
                          'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete')))
                        ?>')"></a>
                         <?php endif; ?>
                    </span>
                    <span class="comments_body">
                      <?php echo $this->allowEmotionsIcon ? $this->smileyToEmoticons($this->viewMore($comment->body)) : $this->viewMore($comment->body); ?>
                    </span>
                    <ul class="comments_date">
                      <li class="comments_timestamp">
                        <?php echo $this->timestamp($comment->creation_date); ?>
                      </li>
                      <?php
                      if ($canComment):
                        $isLiked = $comment->likes()->isLike($this->viewer());
                        ?>
                        <li class="comments_like"> 
                          &#183;
                          <?php if (!$isLiked): ?>
                            <a href="javascript:void(0)" onclick="en4.advancedactivity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)">
                              <?php echo $this->translate('like') ?>
                            </a>
                          <?php else: ?>
                            <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)">
                              <?php echo $this->translate('unlike') ?>
                            </a>
                          <?php endif ?>
                        </li>
                      <?php endif ?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                        <li class="comments_likes_total"> 
                          &#183;
                          <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                            <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                          </a>
                        </li>
                      <?php endif ?>
                    </ul>
                  </div>
                </li>
              <?php endforeach; ?>
              <?php if ($canComment): ?>
                <li id='feed-comment-form-open-li_<?php echo $action->action_id ?>' onclick='<?php echo 'document.getElementById("' . $this->commentForm->getAttrib('id') . '").style.display = "";
document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
document.getElementById("feed-comment-form-open-li_' . $action->action_id . '").style.display = "none";
  document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();' ?>' <?php if (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): ?> style="display:none;"<?php endif; ?> >                  <div></div>
                  <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Post a comment...') ?></div></li>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>  
        </ul>
        <?php if ($canComment)
          echo $this->commentForm->render(); ?>
      </div>
    <?php endif; ?>






    <?php
    ob_end_flush();
  } catch (Exception $e) {
    ob_end_clean();
    if (APPLICATION_ENV === 'development') {
      echo $e->__toString();
    }
  };
endforeach;
?>

