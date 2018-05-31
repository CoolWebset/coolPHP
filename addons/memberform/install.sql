DROP TABLE IF EXISTS `cool_memberform`;CREATE TABLE `cool_memberform` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT '',
  `phone` varchar(60) NOT NULL DEFAULT '' COMMENT '电话号码',
  `addtime` int(15) NOT NULL COMMENT '留言时间',
  `sex` tinyint(2) NOT NULL DEFAULT 0,
  `birsthday` int(60),
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cool_memberform_category`;CREATE TABLE `cool_memberform_category` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `href` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `authopen` tinyint(2) NOT NULL DEFAULT '1',
  `icon` varchar(20) DEFAULT NULL COMMENT '样式',
  `condition` char(100) DEFAULT '',
  `pid` int(5) NOT NULL DEFAULT '0' COMMENT '父栏目ID',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `zt` int(1) DEFAULT NULL,
  `menustatus` tinyint(1) DEFAULT NULL,
  `catid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;INSERT INTO `cool_memberform_category` VALUES ('1', '/addons_execute_memberform-admin-index', '会员管理', '1', '1', '0', 'icon-cogs', '', '0', '0', '1446535750', '1', '1', null);INSERT INTO `cool_memberform_category` VALUES ('2', '/addons_execute_memberform-admin-yuding?type=1', '会员管理', '1', '1', '0', '', '', '1', '1', '1446535789', '1', '1', null);
