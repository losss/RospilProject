-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Generation Time: Jan 17, 2011 at 08:24 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.6
-- 
-- Database: `rospil`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `additions`
-- 

CREATE TABLE `additions` (
  `addid` int(11) NOT NULL auto_increment,
  `addtext` text,
  `addts` int(11) default NULL,
  `added_by` int(11) default NULL,
  `deleted_ts` int(11) default NULL,
  `deleted_by` int(11) default NULL,
  `leadid` int(11) default NULL,
  `addfile` varchar(255) default NULL,
  `pic_o` varchar(255) default NULL,
  `pic_b` varchar(255) default NULL,
  `pic_m` varchar(255) default NULL,
  `pic_s` varchar(255) default NULL,
  `pic_t` varchar(255) default NULL,
  PRIMARY KEY  (`addid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `commentid` int(11) NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `comment` text,
  `created_ts` int(11) default NULL,
  `deleted_ts` int(11) default NULL,
  `deleted_by` int(11) default NULL,
  `leadid` int(11) default NULL,
  `user_name` varchar(45) default NULL,
  `rating` int(11) default NULL,
  `votes` text,
  `internal` char(1) default NULL,
  `user_type` int(11) default NULL,
  PRIMARY KEY  (`commentid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `leads`
-- 

CREATE TABLE `leads` (
  `leadid` int(11) NOT NULL auto_increment,
  `orgid` int(11) default NULL,
  `title` varchar(100) default NULL,
  `description` text,
  `amount` double default NULL,
  `days` int(11) default NULL,
  `contact_name` varchar(45) default NULL,
  `contact_phone` varchar(45) default NULL,
  `contact_email` varchar(45) default NULL,
  `published_ts` int(11) default NULL,
  `discovered_ts` int(11) default NULL,
  `deleted_ts` int(11) default NULL,
  `cancelled_ts` int(11) default NULL,
  `comments_disabled_ts` int(11) default NULL,
  `published_by` int(11) default NULL,
  `deleted_by` int(11) default NULL,
  `comments_disabled_by` int(11) default NULL,
  `petition_text` text,
  `petition_instructions` text,
  `petition_sent_count` int(11) default NULL,
  `link` varchar(255) default NULL,
  `org_name` varchar(255) default NULL,
  `petition_org_name` varchar(255) default NULL,
  `petition_orgid` int(11) default NULL,
  `comments_count` int(11) default NULL,
  `petition_link` varchar(255) default NULL,
  `whyfraud` text,
  `scheduled` varchar(20) default NULL,
  `expertdoc` varchar(255) default NULL,
  `preselected` char(1) default NULL,
  `pic` varchar(255) default NULL,
  `booked_expertid` int(11) default NULL,
  `booked_ts` int(11) default NULL,
  `expertname` varchar(45) default NULL,
  `pic_b` varchar(255) default NULL,
  `pic_m` varchar(255) default NULL,
  `pic_s` varchar(255) default NULL,
  `pic_t` varchar(255) default NULL,
  `pic_o` varchar(255) default NULL,
  `petition_users` text,
  `commented_ts` int(11) default NULL,
  PRIMARY KEY  (`leadid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `orgs`
-- 

CREATE TABLE `orgs` (
  `orgid` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `chief_name` varchar(100) default NULL,
  `chief_contact` text,
  `edited_by` int(11) default NULL,
  `petition_page_url` varchar(255) default NULL,
  `total_cases` int(11) default NULL,
  `total_amount` double default NULL,
  `chief_pic_b` varchar(255) default NULL,
  `chief_pic_m` varchar(255) default NULL,
  `chief_pic_s` varchar(255) default NULL,
  `chief_pic_t` varchar(255) default NULL,
  `chief_pic_o` varchar(255) default NULL,
  PRIMARY KEY  (`orgid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `petitions`
-- 

CREATE TABLE `petitions` (
  `petitionid` int(11) NOT NULL auto_increment,
  `target_url` varchar(255) default NULL,
  `target_org` varchar(255) default NULL,
  `target_person` varchar(100) default NULL,
  `body` text,
  `created_by` int(11) default NULL,
  `created_ts` int(11) default NULL,
  `tagret_orgid` int(11) default NULL,
  PRIMARY KEY  (`petitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `userid` int(11) NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  `email` varchar(45) default NULL,
  `usercode` varchar(45) default NULL,
  `cookie` varchar(45) default NULL,
  `cookie_ts` int(11) default NULL,
  `password` varchar(45) default NULL,
  `type` varchar(45) default NULL,
  `status` varchar(45) default NULL,
  `registered_ts` int(11) default NULL,
  `last_ip_address` varchar(45) default NULL,
  `realname` varchar(255) default NULL,
  `specialty` varchar(255) default NULL,
  `protext` text,
  PRIMARY KEY  (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
