/*
Navicat MySQL Data Transfer

Source Server         : yabuli_hrbkcwl_
Source Server Version : 50557
Source Host           : 47.95.228.57:3306
Source Database       : yabuli_hrbkcwl_

Target Server Type    : MYSQL
Target Server Version : 50557
File Encoding         : 65001

Date: 2018-05-30 10:44:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cool_diyform
-- ----------------------------
DROP TABLE IF EXISTS `cool_diyform`;
CREATE TABLE `cool_diyform` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT '' COMMENT '留言标题',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '留言电话',
  `addtime` varchar(15) NOT NULL COMMENT '留言时间',
  `open` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1=审核 0=不审核',
  `ip` varchar(50) DEFAULT '' COMMENT '留言者IP',
  `content` longtext NOT NULL COMMENT '留言内容',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `email` varchar(50) NOT NULL COMMENT '留言邮箱',
  `intime` int(11) DEFAULT NULL,
  `outtime` int(11) NOT NULL,
  `partyconame` varchar(255) DEFAULT NULL,
  `partyname` varchar(255) DEFAULT NULL,
  `partytel` varchar(255) DEFAULT NULL,
  `partynum` int(11) DEFAULT NULL,
  `partytime` int(11) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `roomnum` int(11) DEFAULT NULL,
  `adultnum` int(11) DEFAULT NULL,
  `childrennum` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cool_diyform_category
-- ----------------------------
DROP TABLE IF EXISTS `cool_diyform_category`;
CREATE TABLE `cool_diyform_category` (
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
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;