DROP TABLE IF EXISTS `_prefix_comments`;
CREATE TABLE `_prefix_comments` (
  `id` bigint(20) NOT NULL auto_increment,
  `post_id` bigint(20) NOT NULL,
  `ip` char(15) NOT NULL,
  `posted_date` datetime NOT NULL,
  `author` varchar(255) NOT NULL,
  `is_author` tinyint(4) NOT NULL default '0',
  `email` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `_prefix_posts`;
CREATE TABLE `_prefix_posts` (
  `id` bigint(20) NOT NULL auto_increment,
  `post_type` smallint(5) unsigned NOT NULL,
  `posted_date` datetime NOT NULL,
  `comments` smallint(6) NOT NULL default '0',
  `comments_count` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `feed_id` int(11) NOT NULL,
  `published` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`,`feed_id`),
  KEY `post_type` (`post_type`),
  KEY `feed_id` (`feed_id`),
  KEY `posted_date` (`posted_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `_prefix_rss_feeds`;
CREATE TABLE `_prefix_rss_feeds` (
  `id` int(11) NOT NULL auto_increment,
  `rss_url` varchar(255) NOT NULL,
  `update_period` mediumint(9) NOT NULL default '3600',
  `last_update` int(11) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `rss_url` (`rss_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;