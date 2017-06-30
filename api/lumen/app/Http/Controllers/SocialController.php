<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;

use \TwitterAPIExchange;

class SocialController extends Controller
{
  /**
  * Returns a list of odezenne tweets
  *
  * @param  Request  $request
  * @return \Illuminate\Http\Response
  */
  public function twitterFeed(Request $request)
  {
    $count = $request->input('count') ? $request->input('count') : 5;
    $max_id = $request->input('max_id') ? $request->input('max_id') : null;

    $oauth_access_token = Settings::where('label', 'twitter_oauth_access_token')->limit(1)->pluck('value');
    $oauth_access_token_secret = Settings::where('label', 'twitter_oauth_access_token_secret')->limit(1)->pluck('value');
    $consumer_key = Settings::where('label', 'twitter_consumer_key')->limit(1)->pluck('value');
    $consumer_secret = Settings::where('label', 'twitter_consumer_secret')->limit(1)->pluck('value');
    $username = Settings::where('label', 'twitter_username')->limit(1)->pluck('value');

    if (empty($oauth_access_token) || empty($oauth_access_token_secret) || empty($consumer_key) || empty($consumer_secret) || empty($username) ||
        $oauth_access_token[0] === '' || $oauth_access_token_secret[0] === '' || $consumer_key[0] === '' || $consumer_secret[0] === '' || $username[0] === '') {
      return response()->json(array('valid' => false));
    }

    $settings = array(
      'oauth_access_token' => $oauth_access_token[0],
      'oauth_access_token_secret' => $oauth_access_token_secret[0],
      'consumer_key' => $consumer_key[0],
      'consumer_secret' => $consumer_secret[0],
    );

    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $requestMethod = 'GET';
    $getfield = "?screen_name={$username[0]}&count={$count}&include_rts=false";

    // If max id is set we add the param to the getfield
    $max_id ? $getfield .= "&max_id={$max_id}" : '';

    $twitter = new TwitterAPIExchange($settings);
    $tweets = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

    $tweets = json_decode($tweets);

    foreach ($tweets as &$tweet) {
      $url = 'https://publish.twitter.com/oembed';
      $getfield= "url=https://twitter.com/{$tweet->user->screen_name}/status/{$tweet->id}&omit_script=true";
      $html = $twitter->setGetfield($getfield)
      ->buildOauth($url, $requestMethod)
      ->performRequest();
      $tweet->html = json_decode($html)->html;
      $tweet->type = 'tweet';
    }

    return response()->json(array('valid' => true, 'tweets' => $tweets));
  }

  /**
  * Returns a list of odezenne fan's tweets
  *
  * @param  Request  $request
  * @return \Illuminate\Http\Response
  */
  public function fanTweets(Request $request)
  {
    $count = $request->input('count') ? $request->input('count') : 5;
    $max_id = $request->input('max_id') ? $request->input('max_id') : null;

    $oauth_access_token = Settings::where('label', 'twitter_oauth_access_token')->limit(1)->pluck('value');
    $oauth_access_token_secret = Settings::where('label', 'twitter_oauth_access_token_secret')->limit(1)->pluck('value');
    $consumer_key = Settings::where('label', 'twitter_consumer_key')->limit(1)->pluck('value');
    $consumer_secret = Settings::where('label', 'twitter_consumer_secret')->limit(1)->pluck('value');

    if (empty($oauth_access_token) || empty($oauth_access_token_secret) || empty($consumer_key) || empty($consumer_secret) ||
        $oauth_access_token[0] === '' || $oauth_access_token_secret[0] === '' || $consumer_key[0] === '' || $consumer_secret[0] === '') {
      return response()->json(array('valid' => false));
    }

    $settings = array(
      'oauth_access_token' => $oauth_access_token[0],
      'oauth_access_token_secret' => $oauth_access_token_secret[0],
      'consumer_key' => $consumer_key[0],
      'consumer_secret' => $consumer_secret[0],
    );

    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $requestMethod = 'GET';
    $getfield = "?q=odezenne&result_type=mixed&count={$count}";

    // If max id is set we add the param to the getfield
    $max_id ? $getfield .= "&max_id={$max_id}" : '';

    $twitter = new TwitterAPIExchange($settings);
    $tweets = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

    $tweets = json_decode($tweets);

    foreach ($tweets->statuses as &$tweet) {
      $url = 'https://publish.twitter.com/oembed';
      $getfield= "url=https://twitter.com/{$tweet->user->screen_name}/status/{$tweet->id}&omit_script=true";
      $html = $twitter->setGetfield($getfield)
      ->buildOauth($url, $requestMethod)
      ->performRequest();
      $tweet->html = json_decode($html)->html;
      $tweet->type = 'tweet';
    }

    return response()->json(array('valid' => true, 'tweets' => $tweets->statuses));
  }

    /**
     * Returns a list of youtube videos
     *
     * @return \Illuminate\Http\Response
     */
    public function youtubeFeed(Request $request)
    {
        $videos = [];

        $page = $request->input('page') ? $request->input('page') : null;

        $api_key = Settings::where('label', 'youtube_api_key')->limit(1)->pluck('value');
        $max_results = Settings::where('label', 'youtube_max_results')->limit(1)->pluck('value')[0];
        $username = Settings::where('label', 'youtube_username')->limit(1)->pluck('value');

        if (empty($api_key) || empty($username) || $api_key[0] === '' || $username[0] === '') {
          return response()->json(array('valid' => false));
        }

        if (empty($max_results) || $max_results <= 0) {
            $max_results = 10;
        }

        //search channels of user
        $json = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=' . $username[0] . '&key=' . $api_key[0]);
        $channels = json_decode($json);
        $playlist_id = $channels->items[0]->contentDetails->relatedPlaylists->uploads;

        //search playlist items of upload channel
        if ($page) {
          $infos = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=' . $max_results . '&playlistId=' . $playlist_id . '&key=' . $api_key[0] . '&pageToken=' . $page));
        } else {
          $infos = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=' . $max_results . '&playlistId=' . $playlist_id . '&key=' . $api_key[0]));
        }

        $items = $infos->items;
        if(isset($infos->nextPageToken)) {
          $nextPage = $infos->nextPageToken;
        }

        foreach ($items as $item) {
            // search videos of the playlist items of the upload channel
            $json = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id,snippet,contentDetails,status&id=' . $item->contentDetails->videoId . '&maxResults=' . $max_results . '&key=' . $api_key[0]);
            $video = json_decode($json);
            $video->type = 'youtube';
            array_push($videos, $video);
        }

        return response()->json(array('valid' => true, 'videos' => $videos, 'nextPage' => $nextPage));
    }

    public function soundcloudFeed(Request $request)
    {
      $count = $request->input('count') ? $request->input('count') : 5;
      $page = $request->input('page') ? $request->input('page') : null;

      $api_key = Settings::where('label', 'soundcloud_api_key')->limit(1)->pluck('value');
      $user_id = Settings::where('label', 'soundcloud_user_id')->limit(1)->pluck('value');

      if (empty($api_key) || empty($user_id) || $api_key[0] === '' || $user_id[0] === '') {
        return response()->json(array('valid' => false));
      }

      if ($page) {
        $soundcloud_url = $page;
      } else {
        $soundcloud_url = "https://api.soundcloud.com/users/{$user_id[0]}/tracks.json?client_id={$api_key[0]}&limit={$count}&linked_partitioning=1";
      }

      $data = json_decode(file_get_contents($soundcloud_url));

      $tracks = $data->collection;
      $nextPage = $data->next_href;

      foreach ($tracks as &$track) {
        $track->type = 'soundcloud';
      }

      return response()->json(array('valid' => true, 'tracks' => $tracks, 'nextPage' => $nextPage));
    }

    /**
     * Returns a list of instagram naked pictures
     *
     * @return \Illuminate\Http\Response
     */
    public function instagramFeed(Request $request)
    {
        $page = $request->input('page') ? $request->input('page') : null;
        $token = Settings::where('label', 'instagram_token')->limit(1)->pluck('value');
        $max_results = Settings::where('label', 'instagram_max_results')->limit(1)->pluck('value')[0];
        if (empty($token) || $token[0] === '') {
          return response()->json(array('valid' => false));
        }
        $url = "https://api.instagram.com/v1/users/self/?access_token=$token[0]";

        $posts = [];

        $data = $this->curlfunction($url);

        $userId = $data['data']['id'];
        if ($page) {
          $json = file_get_contents($page . "&count=$max_results");
        } else {
          $json = file_get_contents("https://api.instagram.com/v1/users/$userId/media/recent/?access_token=" . $token[0] . "&count=$max_results");
        }

        $a_json = json_decode($json, true);
        $i = 0;

        foreach ($a_json['data'] as $key => $value) {
            if ($i < $max_results) {
              $posts[$i]['id'] = $value['id'];
              $posts[$i]['post_url'] = $value['link'];
              $posts[$i]['images_url'] = $value['images']['standard_resolution']['url'];
              $posts[$i]['alt'] = $value['caption']['text'];
              $posts[$i]['type'] = 'instagram';

              $i++;
            }
        }

        $a_json_pagination = $a_json['pagination']['next_url'];


        return response()->json(array('valid' => true, 'posts' => $posts));
    }

    public function instagramFan()
    {
        $token = Settings::where('label', 'instagram_token')->limit(1)->pluck('value')[0];
        $url = "https://api.instagram.com/v1/tags/odezenne/media/recent?access_token=$token&count=20";
        $max_results = Settings::where('label', 'instagram_max_results')->limit(1)->pluck('value')[0];

        $posts = [];

        $data = $this->curlfunction($url);

        $i = 0;
        foreach ($data['data'] as $key => $value) {
            if ($i < $max_results) {
              $posts[$i]['post_url'] = $value['link'];
              $posts[$i]['images_url'] = $value['images']['standard_resolution']['url'];

              $i++;
            }
        }

        return response()->json($posts);
    }

    public function facebookFeed(Request $request)
    {
      $posts_full = [];

      $page = $request->input('page') ? $request->input('page') : null;

      $client_id = Settings::where('label', 'facebook_client_id')->limit(1)->pluck('value');
      $client_secret = Settings::where('label', 'facebook_client_secret')->limit(1)->pluck('value');
      $max_results = Settings::where('label', 'facebook_max_results')->limit(1)->pluck('value')[0];

      if (empty($client_id) || empty($client_secret) || $client_id[0] === '' || $client_secret[0] === '') {
        return response()->json(array('valid' => false));
      }

      // Get access token
      $json = file_get_contents("https://graph.facebook.com/v2.9/oauth/access_token?client_id=$client_id[0]&client_secret=$client_secret[0]&grant_type=client_credentials");
      $data = json_decode($json, true);
      $access_token = $data['access_token'];

      // Get Page id from page name
      $json = file_get_contents("https://graph.facebook.com/v2.9/odezenne?access_token=$access_token");
      $data = json_decode($json, true);
      $page_id = $data['id'];

      if ($page) {
        $json = file_get_contents($page . "&limit=10");
      } else {
        $json = file_get_contents("https://graph.facebook.com/v2.9/$page_id/posts?access_token=$access_token&limit=10");
      }

      $posts = json_decode($json, true);



      if ($posts['paging']['next']) {
        $nextPage = $posts['paging']['next'];
      }

      foreach ($posts['data'] as $post) {

        $post_id = $post['id'];
        $json = file_get_contents("https://graph.facebook.com/v2.9/$post_id?fields=permalink_url&access_token=$access_token");
        $post_url = json_decode($json, true);

        $post_url['type'] = 'facebook';

        array_push($posts_full, $post_url);
      }

      return response()->json(array('valid' => true, 'posts' => $posts_full, 'nextPage' => $nextPage));
    }

    public function curlfunction($url)
    {
      $curl_connection = curl_init($url);
      curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);

      $data = json_decode(curl_exec($curl_connection), true);
      curl_close($curl_connection);

      return $data;
    }
}
