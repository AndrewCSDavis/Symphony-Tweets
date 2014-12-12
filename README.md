# Symphony Extension of PHP Tweets

##Install
go to extensions and install

## How to use
you will be able to see a list of different options made available to you in the preferences

### Preferences options:
  - Username / Screenname Search
    add a username or screenname you wish to search for e.g.. "@acsdt121" or "Andrew Davis" 
  - Widget ID
    You must first create a widget in your twitter account by going to https://twitter.com/settings/widgets (after you have singed in)
  - Amount of tweets
    amount of tweets you wish to display upto a max of 20
  - Show Action Links (Reply, Retweet, Favourite)
    adds three links to each of the actions
  - Show Screenname only
    allows you to change between viewing either the screenname or the username of each tweet
  - Show Users Avatar
    adds an avatar image of each tweeter
  - Do you require the time to be added?
    adds the time of when each tweet was published and the next option chnages the format of the time
    e.g.. Posted on 22 Dec / 22 Dec
  - Do you require the stats of each tweet?
    displays the amount of tweets and or favourites the tweet has received e.g.. 1 Favourite, 2 Retweets
  - Rearrange the order of the contents of the tweets DOM structure
    author, contents,tweet-actions,time


    ````<ul class="tweets">
    ````<li>  
    ````<div class="section author"></div>
    ```` <div class="section time"></div>
    ```` <div class="section contents"></div>
    ```` <div class="section tweet-actions"></div>
    ```` </li>
    ```` </ul>
      
