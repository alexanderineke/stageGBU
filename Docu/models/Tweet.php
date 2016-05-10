<?php

class Tweet
{
    public function give(){
        $tweets = Yii::app()->cache->get('frontpage_tweets');
        if (!$tweets) {
            $tweets = $this->get();
            $tweets = $this->format($tweets);
            Yii::app()->cache->set('frontpage_tweets', $tweets, '60');
        }

        return $tweets;
    }

    protected function get(){
        $oauth = new \TwitterOAuth\Api(
            Yii::app()->params['twitter']['consumer_key'],
            Yii::app()->params['twitter']['consumer_secret'],
            Yii::app()->params['twitter']['oauth_token'],
            Yii::app()->params['twitter']['oauth_secret']
        );

        $timeline = json_decode($oauth->oAuthRequest(
            'https://api.twitter.com/1.1/statuses/user_timeline.json', 
            'GET', 
            [
                'screen_name'=> Yii::app()->params['twitter']['user'], 
                'include_entities'=>'true',
                'count'=> Yii::app()->params['twitter']['amount']
            ]
        ));

        return $timeline;
    }

    protected function format($tweets) {
        $twitter_array = [];

        for ($i = 0; $i < count($tweets); $i++) {
            $twitter_array[$i]['text'] = $tweets[$i]->text;
            $twitter_array[$i]['user']['profile_image_url'] = $tweets[$i]->user->profile_image_url;
            if (isset($tweets[$i]->entities->hashtags)) {
                $twitter_array[$i]['entities']['hashtags'] = $tweets[$i]->entities->hashtags;
            }
            if (isset($tweets[$i]->entities->urls)) {
                $twitter_array[$i]['entities']['urls'] = $tweets[$i]->entities->urls;
            }
            if (isset($tweets[$i]->entities->media)) {
                $twitter_array[$i]['entities']['media'] = $tweets[$i]->entities->media;
            }
        }

        return json_encode($twitter_array);
    }

}
