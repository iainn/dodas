<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Widget_AdvancedactivitytwitterUserfeedController extends Engine_Content_Widget_Abstract {

  public function indexAction() {



    $view = new Zend_View();
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $this->view->curr_url = $curr_url = $front->getRequest()->getRequestUri(); // Return the current URL.
    $this->view->facebooksepage_fbinvite = $facebooksepage_fbinvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebooksepage.fbinvite', 0);
    $aafTwiterType = Zend_Registry::isRegistered('advancedactivity_twitterType') ? Zend_Registry::get('advancedactivity_twitterType') : null;

    $limit = $front->getRequest()->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->checkUpdate = $checkUpdate = $this->_getParam('checkUpdate', false);
    $this->view->getUpdate = $getUpdate = $this->_getParam('getUpdate', false);
    $TwitterloginURL = Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array('module' => 'seaocore', 'controller' => 'auth',
                        'action' => 'twitter'), 'default', true) . '?return_url=' . 'http://' . $_SERVER['HTTP_HOST'] . $this->view->curr_url;
    $this->view->TwitterLoginURL = '';
    $this->view->isajax = $is_ajax = $this->_getParam('is_ajax', '0');
    $this->view->twitterGetUpdate = $twitterGetUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('getaaf.twitterGetUpdate', 0);
    try {
      $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
      if (!empty($aafTwiterType) && $twitterTable->isConnected() && !empty($twitterGetUpdate)) {
        // @todo truncation?
        // @todo attachment
        $twitter = $twitterTable->getApi();

        $this->view->endOfFeed_twitter = $limit;
        $this->view->id_CurrentLoggedTweetUser = $twitter->account->verify_credentials()->id_str;
        $this->view->image_CurrentLoggedTweetUser = $twitter->account->verify_credentials()->profile_image_url;
        $this->view->screenname_CurrentLoggedTweetUser = $twitter->account->verify_credentials()->screen_name;
        if (empty($is_ajax) && empty ($getUpdate)) {


          $this->view->logged_TwitterUserfeed = $logged_TwitterUserfeed = $twitter->statuses->home_timeline(array('count' => $limit));
          $count_tweets = count($logged_TwitterUserfeed);
          $this->view->lastOldTweet = $lastOldTweet = $logged_TwitterUserfeed[--$count_tweets]->id_str;
          $retweeted_by_me = $twitter->statuses->retweeted_by_me(array('since_id' => $lastOldTweet));
          //MAKING AN ARRAY OF THE TWEET STATUS IDS WHICH I HAVE RETWEETED.

          $retweets_by_me = array();
          foreach ($retweeted_by_me as $retweet_by_me) {
            $retweets_by_me[] = $retweet_by_me->retweeted_status->id_str;
          }

          $this->view->retweets_by_me = $retweets_by_me;

          $this->view->id_CurrentLoggedTweetUser = $twitter->account->verify_credentials()->id_str;
          $this->view->nextid_twitter = ++$count_tweets;

          if ($count_tweets > 0) {
            if (!empty($logged_TwitterUserfeed[0]->retweeted_status)) {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->retweeted_status->id_str;
            } else {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->id_str;
            }
          }

          $this->view->current_tweet_statusid = $current_tweet_statusid;
        } else if ($is_ajax == 1) {

          $next_prev = $this->_getParam('next_prev', '');
          $max_id = $this->_getParam('max_id', '');
          $since_id = $this->_getParam('since_id', '');
          $duration = $this->_getParam('duration', '');
          $this->view->task = $task = $this->_getParam('task', '');

          if (!empty($max_id)) {
            $this->view->logged_TwitterUserfeed = $logged_TwitterUserfeed = $twitter->statuses->home_timeline(array('count' => $limit, 'max_id' => $max_id));
          } else {
            $this->view->logged_TwitterUserfeed = $logged_TwitterUserfeed = $twitter->statuses->home_timeline(array('count' => $limit));
          }

          $count_tweets = count($logged_TwitterUserfeed);
          $this->view->lastOldTweet = $lastOldTweet = $logged_TwitterUserfeed[--$count_tweets]->id_str;
          $retweeted_by_me = $twitter->statuses->retweeted_by_me(array('since_id' => $lastOldTweet));
          //MAKING AN ARRAY OF THE TWEET STATUS IDS WHICH I HAVE RETWEETED.

          $retweets_by_me = array();
          foreach ($retweeted_by_me as $retweet_by_me) {
            $retweets_by_me[] = $retweet_by_me->retweeted_status->id_str;
          }

          $this->view->retweets_by_me = $retweets_by_me;


          $this->view->nextid_twitter = ++$count_tweets;

          if (empty($task) && $count_tweets > 0) {
            if (!empty($logged_TwitterUserfeed[0]->retweeted_status)) {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->retweeted_status->id_str;
            } else {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->id_str;
            }
          } else if (!empty($task)) {
            $current_tweet_statusid = $since_id;
          }

          $this->view->current_tweet_statusid = $current_tweet_statusid;
        } else if ($is_ajax == 2) {
          $viewerName = Engine_Api::_()->user()->getViewer()->username;
          $viewerURL = Engine_Api::_()->user()->getViewer()->getHref();
          $viewer = '<a href="' . $viewerURL . '" target="_blank">' . $viewerName . '</a>';
          $post_status = $this->_getParam('post_status', '');
          $tweetstatus_id = $this->_getParam('tweetstatus_id', '');

          $twitter->statuses->update(html_entity_decode($post_status), array('in_reply_to_status_id' => $tweetstatus_id));

          echo Zend_Json::encode(array('Twitter_statusreply' => 1, 'viewer' => $viewer));
          exit();
        } else if ($is_ajax == 3) {
          $tweetstatus_id = $this->_getParam('tweetstatus_id', '');
          $reTweetUpdate = $twitter->statuses->retweet(array('id' => $tweetstatus_id));
          echo Zend_Json::encode(array('success' => $reTweetUpdate));
          exit();
        } else if ($is_ajax == 4) {
          $post_status = $this->_getParam('status', '');
          $TweetUpdate = $twitter->statuses->update(html_entity_decode($post_status));
          echo Zend_Json::encode(array('success' => $TweetUpdate));
          exit();
        } else if ($is_ajax == 5) {
          $favorite_status_id = $this->_getParam('tweetstatus_id', '');
          $favorite_action = $this->_getParam('favorite_action', '');
          if ($favorite_action == 1) {
            $TweetUpdate = $twitter->favorites->create(array('id' => $favorite_status_id));
          } else {
            $TweetUpdate = $twitter->favorites->destroy(array('id' => $favorite_status_id));
          }
          echo Zend_Json::encode(array('success' => $TweetUpdate));
          exit();
        } else if ($is_ajax == 6) {
          $tweet_status_id = $this->_getParam('tweetstatus_id', '');
          $TweetUpdate = $twitter->statuses->destroy(array('id' => $tweet_status_id));

          echo Zend_Json::encode(array('success' => $TweetUpdate));
          exit();
        }



        if (!empty($checkUpdate)) {

          $min_id = $this->_getParam('minid');
          if (!empty($min_id)) {
			  $this->view->logged_TwitterUserfeed = $logged_TwitterUserfeed = $twitter->statuses->home_timeline(array('count' => $limit, 'since_id' => $min_id));
			  $count_tweets = count($logged_TwitterUserfeed);

			  foreach ($logged_TwitterUserfeed as $key => $tweetfeed) :
				if (empty($task) && $count_tweets > 0) {
				  if (!empty($tweetfeed->retweeted_status) && $tweetfeed->retweeted_status->id_str == $min_id) {
					unset($logged_TwitterUserfeed[$key]);
					$count_tweets--;
					break;
				  } else if ($tweetfeed->id_str == $min_id) {
					$count_tweets--;
					unset($logged_TwitterUserfeed[$key]);
					break;
				  } else {
					continue;
				  }
				}
			  endforeach;

			  $this->view->Tweet_count = $count_tweets;
			  if ($count_tweets > 0) {
				if (!empty($logged_TwitterUserfeed[0]->retweeted_status)) {
				  $min_id = $logged_TwitterUserfeed[0]->retweeted_status->id_str;
				} else {
				  $min_id = $logged_TwitterUserfeed[0]->id_str;
				}
			  }
			  $this->view->current_tweet_statusid = $min_id;
	      }
        }


        if (!empty($getUpdate)) { 
          if ($this->_getParam('currentaction', '') != 'post_new') {
            $min_id = $this->_getParam('minid'); 
            $logged_TwitterUserfeed = $twitter->statuses->home_timeline(array('count' => $limit, 'since_id' => $min_id));
            $count_tweets = count($logged_TwitterUserfeed);
            foreach ($logged_TwitterUserfeed as $key => $tweetfeed) :
              if (empty($task) && $count_tweets > 0) {
                if (!empty($tweetfeed->retweeted_status) && $tweetfeed->retweeted_status->id_str == $min_id) {
                  unset($logged_TwitterUserfeed[$key]);
                  $count_tweets--;
                  break;
                } else if ($tweetfeed->id_str == $min_id) {
                  $count_tweets--;
                  unset($logged_TwitterUserfeed[$key]);
                  break;
                } else {
                  continue;
                }
              }
            endforeach;
          }
          else {
            $logged_TwitterUserfeed[0] = $this->_getParam('feedobj');
            $count_tweets = 1;
            
          }
         
          
          $this->view->logged_TwitterUserfeed = $logged_TwitterUserfeed;
          $this->view->Tweet_count = $count_tweets;
          $retweeted_by_me = $twitter->statuses->retweeted_by_me(array('since_id' => $min_id));
          //MAKING AN ARRAY OF THE TWEET STATUS IDS WHICH I HAVE RETWEETED.

          $retweets_by_me = array();
          foreach ($retweeted_by_me as $retweet_by_me) {
            $retweets_by_me[] = $retweet_by_me->retweeted_status->id_str;
          }

          $this->view->retweets_by_me = $retweets_by_me;

          $this->view->id_CurrentLoggedTweetUser = $twitter->account->verify_credentials()->id_str;
          if ($count_tweets > 0) {
            if (!empty($logged_TwitterUserfeed[0]->retweeted_status)) {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->retweeted_status->id_str;
            } else {
              $current_tweet_statusid = $logged_TwitterUserfeed[0]->id_str;
            }
          }

          $this->view->current_tweet_statusid = $current_tweet_statusid;
        }


        $this->view->session_id = 1;
        if (!empty($is_ajax) || !empty($getUpdate)) {
          $this->getElement()->removeDecorator('Title');
          $this->getElement()->removeDecorator('Container');
        }
      } else {
        $this->view->session_id = 0;
        $this->view->TwitterLoginURL = $TwitterloginURL;
      }
    } catch (Exception $e) {
      $this->view->TwitterLoginURL = $TwitterloginURL;
      $this->view->session_id = 0;
      // Silence
    }

    if (!empty($is_ajax) || !empty($getUpdate)) {

      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
   
  }

}
