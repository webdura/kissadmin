-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 06, 2012 at 12:01 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gnetmail_db5722`
--

-- --------------------------------------------------------

--
-- Table structure for table `gma_accounts`
--

CREATE TABLE IF NOT EXISTS `gma_accounts` (
  `companyId` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `paymentDue` int(11) NOT NULL,
  `overdueNotice` int(11) NOT NULL,
  `suspensionWarning` int(11) NOT NULL,
  KEY `companyId` (`companyId`,`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_accounts`
--

INSERT INTO `gma_accounts` (`companyId`, `grade`, `paymentDue`, `overdueNotice`, `suspensionWarning`) VALUES
(0, 1, 7, 10, 15),
(0, 2, 7, 10, 15),
(0, 3, 7, 10, 15),
(1, 1, 1, 10, 15),
(1, 2, 2, 10, 15),
(1, 3, 3, 10, 15);

-- --------------------------------------------------------

--
-- Table structure for table `gma_admins`
--

CREATE TABLE IF NOT EXISTS `gma_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) NOT NULL,
  `user` varchar(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `gma_admins`
--

INSERT INTO `gma_admins` (`id`, `type`, `name`, `user`) VALUES
(1, 'gnet_admin', 'GNet Admin', 'A'),
(2, 'super_admin', 'Super Admin', 'A'),
(3, 'account_admin', 'Accounts Admin', 'A'),
(4, 'general_admin', 'General Admin', 'A'),
(5, 'client', 'Clients', 'U');

-- --------------------------------------------------------

--
-- Table structure for table `gma_admins_permission`
--

CREATE TABLE IF NOT EXISTS `gma_admins_permission` (
  `companyId` int(11) NOT NULL,
  `admins_id` int(1) NOT NULL,
  `module_id` int(11) NOT NULL,
  KEY `admins_id` (`admins_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_admins_permission`
--

INSERT INTO `gma_admins_permission` (`companyId`, `admins_id`, `module_id`) VALUES
(0, 2, 1),
(0, 2, 2),
(0, 2, 3),
(0, 2, 4),
(0, 2, 5),
(0, 2, 6),
(0, 2, 7),
(0, 2, 8),
(0, 2, 9),
(0, 2, 10),
(0, 2, 11),
(0, 2, 13),
(0, 2, 14),
(0, 2, 15),
(0, 3, 1),
(0, 3, 2),
(0, 3, 3),
(0, 3, 4),
(0, 3, 5),
(0, 3, 6),
(0, 3, 7),
(0, 3, 8),
(0, 3, 9),
(0, 3, 10),
(0, 3, 11),
(0, 3, 13),
(0, 3, 14),
(0, 3, 15),
(0, 4, 1),
(0, 4, 2),
(0, 4, 3),
(0, 4, 4),
(0, 4, 5),
(0, 4, 6),
(0, 4, 7),
(0, 4, 8),
(0, 4, 9),
(0, 4, 10),
(0, 4, 11),
(0, 4, 13),
(0, 4, 14),
(0, 4, 15),
(0, 5, 1),
(0, 5, 2),
(0, 5, 3),
(0, 5, 4),
(0, 5, 5),
(0, 5, 6),
(0, 5, 7),
(0, 5, 8),
(1, 2, 1),
(1, 2, 2),
(1, 2, 3),
(1, 2, 4),
(1, 2, 5),
(1, 2, 6),
(1, 2, 7),
(1, 2, 8),
(1, 2, 9),
(1, 2, 10),
(1, 2, 11),
(1, 2, 13),
(1, 2, 14),
(1, 2, 15),
(1, 3, 1),
(1, 3, 2),
(1, 3, 3),
(1, 3, 4),
(1, 3, 5),
(1, 3, 6),
(1, 3, 7),
(1, 3, 8),
(1, 3, 9),
(1, 3, 10),
(1, 3, 11),
(1, 4, 1),
(1, 4, 2),
(1, 4, 3),
(1, 4, 4),
(1, 4, 5),
(1, 4, 6),
(1, 4, 7),
(1, 4, 8),
(1, 4, 9),
(1, 4, 10),
(1, 4, 11),
(1, 4, 13),
(1, 4, 14),
(1, 4, 15),
(1, 1, 1),
(1, 1, 2),
(1, 1, 3),
(1, 1, 4),
(1, 1, 5),
(1, 1, 6),
(1, 1, 7),
(1, 1, 8),
(1, 1, 9),
(1, 1, 10),
(1, 1, 11),
(1, 1, 12),
(1, 1, 13),
(1, 1, 14),
(1, 1, 15),
(1, 3, 13),
(1, 3, 14),
(1, 3, 15),
(3, 2, 1),
(3, 2, 2),
(3, 2, 3),
(3, 2, 4),
(3, 2, 5),
(3, 2, 6),
(3, 2, 7),
(3, 2, 8),
(3, 2, 9),
(3, 2, 10),
(3, 2, 11),
(3, 2, 13),
(3, 2, 14),
(3, 2, 15),
(3, 3, 1),
(3, 3, 2),
(3, 3, 3),
(3, 3, 4),
(3, 3, 5),
(3, 3, 6),
(3, 3, 7),
(3, 3, 8),
(3, 3, 9),
(3, 3, 10),
(3, 3, 11),
(3, 3, 13),
(3, 3, 14),
(3, 3, 15),
(3, 4, 1),
(3, 4, 2),
(3, 4, 3),
(3, 4, 4),
(3, 4, 5),
(3, 4, 6),
(3, 4, 7),
(3, 4, 8),
(3, 4, 9),
(3, 4, 10),
(3, 4, 11),
(3, 4, 13),
(3, 4, 14),
(3, 4, 15),
(3, 5, 1),
(3, 5, 2),
(3, 5, 3),
(3, 5, 4),
(3, 5, 5),
(3, 5, 6),
(3, 5, 7),
(3, 5, 8),
(1, 5, 5),
(1, 5, 2),
(1, 5, 3),
(1, 5, 4),
(1, 5, 6),
(1, 5, 7),
(1, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `gma_admin_details`
--

CREATE TABLE IF NOT EXISTS `gma_admin_details` (
  `userId` int(11) NOT NULL,
  `fullName` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  UNIQUE KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_admin_details`
--

INSERT INTO `gma_admin_details` (`userId`, `fullName`) VALUES
(1, 'GNet Admin'),
(3, 'Sub Admin'),
(5, 'Company Owner'),
(10, 'Test'),
(11, 'sdfsdf');

-- --------------------------------------------------------

--
-- Table structure for table `gma_company`
--

CREATE TABLE IF NOT EXISTS `gma_company` (
  `companyId` int(11) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) NOT NULL,
  `companyVatNo` varchar(250) NOT NULL,
  `companyAccountEmail` varchar(250) NOT NULL,
  `companyAccountTel` varchar(250) NOT NULL,
  `companyAccountFax` varchar(250) NOT NULL,
  `companyAccountContact` varchar(250) NOT NULL,
  `companyBankName` varchar(250) NOT NULL,
  `companyBranchName` varchar(250) NOT NULL,
  `companyBranchNo` varchar(250) NOT NULL,
  `companyAccountName` varchar(250) NOT NULL,
  `companyAccountType` varchar(250) NOT NULL,
  `companyAccountNo` varchar(250) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `ownerId` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`companyId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `gma_company`
--

INSERT INTO `gma_company` (`companyId`, `companyName`, `companyVatNo`, `companyAccountEmail`, `companyAccountTel`, `companyAccountFax`, `companyAccountContact`, `companyBankName`, `companyBranchName`, `companyBranchNo`, `companyAccountName`, `companyAccountType`, `companyAccountNo`, `status`, `ownerId`, `created`) VALUES
(1, 'Gnet Mail', 'companyVatNo', 'companyAccountEmail', 'companyAccountTel', 'companyAccountFax', 'companyAccountContact', 'companyBankName', 'companyBranchName', 'companyBranchNo', 'companyAccountName', 'companyAccountType', 'companyAccountNo', 1, 1, 0),
(3, 'Company', '', '', '', '', '', '', '', '', '', '', '', 1, 5, 0),
(4, 'Test Company', '', '', '', '', '', '', '', '', '', '', '', 1, 10, 0),
(5, 'Gnet Mail Test', 'sdfs', '', '', '', '', '', '', '', '', '', '', 1, 11, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gma_company_module`
--

CREATE TABLE IF NOT EXISTS `gma_company_module` (
  `companyId` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  KEY `companyId` (`companyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_company_module`
--

INSERT INTO `gma_company_module` (`companyId`, `module_id`, `status`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 12, 1),
(1, 13, 1),
(1, 14, 1),
(1, 15, 1),
(3, 10, 1),
(3, 12, 0),
(3, 15, 1),
(3, 14, 1),
(3, 9, 1),
(3, 13, 1),
(3, 11, 1),
(3, 2, 1),
(3, 1, 1),
(3, 4, 1),
(3, 5, 1),
(3, 7, 1),
(3, 8, 1),
(3, 3, 0),
(3, 6, 1),
(4, 10, 1),
(4, 12, 0),
(4, 15, 1),
(4, 14, 1),
(4, 5, 1),
(4, 9, 1),
(4, 13, 1),
(4, 11, 1),
(4, 2, 1),
(4, 1, 1),
(4, 4, 1),
(4, 7, 1),
(4, 8, 1),
(4, 3, 0),
(4, 6, 1),
(5, 10, 1),
(5, 12, 0),
(5, 15, 1),
(5, 14, 1),
(5, 5, 1),
(5, 9, 1),
(5, 13, 1),
(5, 11, 1),
(5, 2, 1),
(5, 1, 1),
(5, 4, 1),
(5, 7, 1),
(5, 8, 1),
(5, 3, 0),
(5, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_company_theme`
--

CREATE TABLE IF NOT EXISTS `gma_company_theme` (
  `companyId` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `site_logo` varchar(50) NOT NULL,
  `invoice_logo` varchar(50) NOT NULL,
  `invoice_status` int(1) NOT NULL DEFAULT '1',
  `head_bg` char(7) NOT NULL,
  `head_color` char(7) NOT NULL,
  UNIQUE KEY `theme_id` (`companyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_company_theme`
--

INSERT INTO `gma_company_theme` (`companyId`, `theme_id`, `site_logo`, `invoice_logo`, `invoice_status`, `head_bg`, `head_color`) VALUES
(1, 2, 'site_logo_1.gif', '', 1, '#6B9529', '#FFFFFF');

-- --------------------------------------------------------

--
-- Table structure for table `gma_emails`
--

CREATE TABLE IF NOT EXISTS `gma_emails` (
  `companyId` int(11) NOT NULL,
  `template` varchar(200) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `content` text NOT NULL,
  `variables` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `upload` int(1) NOT NULL DEFAULT '1',
  `update` int(1) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `companyId` (`companyId`,`template`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_emails`
--

INSERT INTO `gma_emails` (`companyId`, `template`, `subject`, `content`, `variables`, `status`, `upload`, `update`, `module_id`) VALUES
(0, 'company', 'New company welcome email', 'Hi [ownername],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator', 'Company Name => [companyname]\r\nOwner Name => [ownername]\r\nUsername => [username]\r\nPassword => [password]\r\nEmail Address => [email] ', 1, 0, 0, 12),
(0, 'due_reminder', 'Due reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date] \r\n\r\nFull Invoice => [invoiceDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname] \r\nLast Name => [lastname] \r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno] \r\nOrder Number => [order_number]\r\nEmail Address => [email]\r\n\r\nBank Name => [companyBankName]\r\nBranch Name => [companyBranchName]\r\nBranch Number => [companyBranchNo]\r\nAccount Name => [companyAccountName]\r\nAccount Type => [companyAccountType]\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(0, 'friendly_reminder', 'Friendly reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date]\r\n\r\nFull Invoice => [invoiceDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno]\r\nOrder Number => [order_number]\r\nEmail Address => [email]\r\n\r\nBank Name => [companyBankName]\r\nBranch Name => [companyBranchName]\r\nBranch Number => [companyBranchNo]\r\nAccount Name => [companyAccountName]\r\nAccount Type => [companyAccountType]\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(0, 'invoice', 'Invoice', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date]\r\n\r\nFull Invoice => [invoiceDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno]\r\nOrder Number => [order_number]\r\nEmail Address => [email]\r\n\r\nBank Name => [companyBankName]\r\nBranch Name => [companyBranchName]\r\nBranch Number => [companyBranchNo]\r\nAccount Name => [companyAccountName]\r\nAccount Type => [companyAccountType]\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(0, 'login_resend', 'Login resend', 'Hi [name],\r\n\r\nYou have indicated that you have forgotten your password. Please  find the login details.\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', 'Company Name => [companyname]\r\nName => [name]\r\nUsername => [username]\r\nPassword => [password]\r\nEmail Address => [email] ', 1, 0, 0, 0),
(0, 'new_admin', 'New admin welcome email', 'Hi [name],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', 'Company Name => [companyname]\r\nName => [name]\r\nUsername => [username]\r\nPassword => [password]\r\nEmail Address => [email] ', 1, 0, 0, 10),
(0, 'new_client', 'New client welcome email', 'Hi [firstname] [lastname],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nClient Name : [clientname]\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', 'Company Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nUsername => [username]\r\nPassword => [password]\r\nEmail Address => [email]', 1, 0, 0, 2),
(0, 'overdue_reminder', 'Overdue reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date]\r\n\r\nFull Invoice => [invoiceDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno]\r\nOrder Number => [order_number]\r\nEmail Address => [email]\r\n\r\nBank Name => [companyBankName]\r\nBranch Name => [companyBranchName]\r\nBranch Number => [companyBranchNo]\r\nAccount Name => [companyAccountName]\r\nAccount Type => [companyAccountType]\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(0, 'quotation', 'Quotation', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [quotationDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date]\r\n\r\nQuotation Details => [quotationDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno]\r\nEmail Address => [email]', 1, 0, 0, 3),
(0, 'statement', 'Statement', '<div id=''wrapper'' align=''center''>\r\n    <div>\r\n        <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n            <tr valign=''top''>\r\n                <td align=''left''>\r\n                    [logo]<br><br>\r\n                    <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n                        <tr valign=''top''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Bill To</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>\r\n                                <div class=''normalbold''>[firstname] [lastname]</div>\r\n                                [address]\r\n                            </td>\r\n                        </tr>\r\n                        <tr valign=''top''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Company</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[companyname]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Tel</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[phone]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Email</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[email]</td>\r\n                        </tr>\r\n                    </table>\r\n                </td>\r\n                <td align=''right'' width=''25%''>\r\n                    <img src=''images/statement.jpg''><br><br>\r\n                    <table cellpadding=''0'' cellspacing=''5'' width=''100%''>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' width=''10%'' align=''right'' nowrap>Statement Date</td>\r\n                            <td class=''normaltext'' align=''left''>[statement_date]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' align=''right''>Customer ID</td>\r\n                            <td class=''normaltext'' align=''left''>[userId]</td>\r\n                        </tr>\r\n                    </table><br><br>\r\n                    <table cellpadding=''0'' cellspacing=''5'' width=''100%''>\r\n                        <tr valign=''middle''>\r\n                            <td colspan=''2'' class=''account_summary''>Account Summary</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' align=''right''>Balance Due</td>\r\n                            <td class=''normaltext''><b>[amount_due]</b></td>\r\n                        </tr>\r\n                    </table>\r\n                    <br>\r\n                    <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n                        <tr valign=''middle''><td align=''left'' style=''font-size:15px''>\r\n                            Your Account Grading : <b>[grade]</b>\r\n                        </td></tr>\r\n                        <tr valign=''middle''><td align=''left''>\r\n                            Higher grading receives priority service..<br>read more about "<a href=''#''>Account grading</a>".\r\n                        </td></tr>\r\n                    </table>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n    </div>\r\n    \r\n    <div class=''clear''>&nbsp;</div>[statement]\r\n</div>\r\n<div style=''clear:both;padding-bottom:10px;''>&nbsp;</div>\r\n<div align=''center''>Content Goes Here</div>', 'Logo => [logo]\r\nUser Id => [userId]\r\nStatement Date => [statement_date]\r\nBalance Due => [amount_due]\r\nFull Statement => [statement]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nGrade => [grade]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nEmail Address => [email]', 1, 0, 0, 6),
(0, 'statement_email', 'Statement Email', 'Hi [firstname] [lastname],\r\n\r\nPlease find the attached statement.\r\n\r\n-----\r\nAdministrator\r\n[companyname]', 'Company Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nEmail Address => [email]', 1, 0, 0, 6),
(0, 'suspension_warning', 'Suspension warning', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]\r\nCompany Account Email Address => [company_account_email]\r\nInvoice Id/Number => [InvoiceId]\r\nOrder Date => [order_date]\r\n\r\nFull Invoice => [invoiceDetails]\r\n\r\nCompany Name => [companyname]\r\nFirst Name => [firstname]\r\nLast Name => [lastname]\r\nClient Name => [clientname]\r\nPhone Number => [phone]\r\nAddress => [address]\r\nVat No => [vatno]\r\nOrder Number => [order_number]\r\nEmail Address => [email]\r\n\r\nBank Name => [companyBankName]\r\nBranch Name => [companyBranchName]\r\nBranch Number => [companyBranchNo]\r\nAccount Name => [companyAccountName]\r\nAccount Type => [companyAccountType]\r\nAccount Number => [companyAccountNo] ', 1, 0, 0, 4),
(1, 'company', 'New company welcome email', 'Hi [ownername],<br />\r\n<br />\r\nAn account has been created for you with the following details,<br />\r\n<br />\r\nCompany Name : [companyname]<br />\r\nUsername : [username]<br />\r\nPassword : [password]<br />\r\nEmail Address : [email]<br />\r\n<br />\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]<br />\r\n<br />\r\n-----<br />\r\nAdministrator', 'Company Name => [companyname]<br />\r\nOwner Name => [ownername]<br />\r\nUsername => [username]<br />\r\nPassword => [password]<br />\r\nEmail Address => [email] ', 1, 0, 0, 12),
(1, 'due_reminder', 'Due reminder', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan=\\"3\\">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n        <tr><td align=\\"center\\">\r\n            <table border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\"><tr><td>\r\n                <table width=\\"450\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"0\\">\r\n                    <tr><td bgcolor=\\"white\\">\r\n                        <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\" bgcolor=\\"[head_bg]\\">\r\n                            <tr><td height=\\"20\\"><div align=\\"left\\" style=\\"padding-left:10px; padding-bottom:10px; padding-top:10px;\\"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align=\\"left\\">\r\n                                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\">\r\n                                    <tr>\r\n                                        <td width=\\"36%\\" class=\\''color4\\''><strong>Bank</strong></td>\r\n                                        <td width=\\"64%\\" class=\\''color4\\'' class=\\"style8\\">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Number</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Type</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Number</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date] <br />\r\n<br />\r\nFull Invoice => [invoiceDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname] <br />\r\nLast Name => [lastname] <br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno] <br />\r\nOrder Number => [order_number]<br />\r\nEmail Address => [email]<br />\r\n<br />\r\nBank Name => [companyBankName]<br />\r\nBranch Name => [companyBranchName]<br />\r\nBranch Number => [companyBranchNo]<br />\r\nAccount Name => [companyAccountName]<br />\r\nAccount Type => [companyAccountType]<br />\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4);
INSERT INTO `gma_emails` (`companyId`, `template`, `subject`, `content`, `variables`, `status`, `upload`, `update`, `module_id`) VALUES
(1, 'friendly_reminder', 'Friendly reminder', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan=\\"3\\">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n        <tr><td align=\\"center\\">\r\n            <table border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\"><tr><td>\r\n                <table width=\\"450\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"0\\">\r\n                    <tr><td bgcolor=\\"white\\">\r\n                        <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\" bgcolor=\\"[head_bg]\\">\r\n                            <tr><td height=\\"20\\"><div align=\\"left\\" style=\\"padding-left:10px; padding-bottom:10px; padding-top:10px;\\"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align=\\"left\\">\r\n                                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\">\r\n                                    <tr>\r\n                                        <td width=\\"36%\\" class=\\''color4\\''><strong>Bank</strong></td>\r\n                                        <td width=\\"64%\\" class=\\''color4\\'' class=\\"style8\\">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Number</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Type</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Number</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date]<br />\r\n<br />\r\nFull Invoice => [invoiceDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno]<br />\r\nOrder Number => [order_number]<br />\r\nEmail Address => [email]<br />\r\n<br />\r\nBank Name => [companyBankName]<br />\r\nBranch Name => [companyBranchName]<br />\r\nBranch Number => [companyBranchNo]<br />\r\nAccount Name => [companyAccountName]<br />\r\nAccount Type => [companyAccountType]<br />\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(1, 'invoice', 'Invoice', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan=\\"3\\">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n        <tr><td align=\\"center\\">\r\n            <table border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\"><tr><td>\r\n                <table width=\\"450\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"0\\">\r\n                    <tr><td bgcolor=\\"white\\">\r\n                        <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\" bgcolor=\\"[head_bg]\\">\r\n                            <tr><td height=\\"20\\"><div align=\\"left\\" style=\\"padding-left:10px; padding-bottom:10px; padding-top:10px;\\"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align=\\"left\\">\r\n                                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\">\r\n                                    <tr>\r\n                                        <td width=\\"36%\\" class=\\''color4\\''><strong>Bank</strong></td>\r\n                                        <td width=\\"64%\\" class=\\''color4\\'' class=\\"style8\\">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Number</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Type</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Number</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date]<br />\r\n<br />\r\nFull Invoice => [invoiceDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno]<br />\r\nOrder Number => [order_number]<br />\r\nEmail Address => [email]<br />\r\n<br />\r\nBank Name => [companyBankName]<br />\r\nBranch Name => [companyBranchName]<br />\r\nBranch Number => [companyBranchNo]<br />\r\nAccount Name => [companyAccountName]<br />\r\nAccount Type => [companyAccountType]<br />\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(1, 'login_resend', 'Login resend', 'Hi [name],<br />\r\n<br />\r\nYou have indicated that you have forgotten your password. Please  find the login details.<br />\r\n<br />\r\nCompany Name : [companyname]<br />\r\nUsername : [username]<br />\r\nPassword : [password]<br />\r\nEmail Address : [email]<br />\r\n<br />\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]<br />\r\n<br />\r\n-----<br />\r\nAdministrator<br />\r\n[companyname]', 'Company Name => [companyname]<br />\r\nName => [name]<br />\r\nUsername => [username]<br />\r\nPassword => [password]<br />\r\nEmail Address => [email] ', 1, 0, 0, 0),
(1, 'new_admin', 'New admin welcome email', 'Hi [name],<br />\r\n<br />\r\nAn account has been created for you with the following details,<br />\r\n<br />\r\nCompany Name : [companyname]<br />\r\nUsername : [username]<br />\r\nPassword : [password]<br />\r\nEmail Address : [email]<br />\r\n<br />\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]<br />\r\n<br />\r\n-----<br />\r\nAdministrator<br />\r\n[companyname]', 'Company Name => [companyname]<br />\r\nName => [name]<br />\r\nUsername => [username]<br />\r\nPassword => [password]<br />\r\nEmail Address => [email] ', 1, 0, 0, 10),
(1, 'new_client', 'New client welcome email', 'Hi [firstname] [lastname],<br />\r\n<br />\r\nAn account has been created for you with the following details,<br />\r\n<br />\r\nClient Name : [clientname]<br />\r\nCompany Name : [companyname]<br />\r\nUsername : [username]<br />\r\nPassword : [password]<br />\r\nEmail Address : [email]<br />\r\n<br />\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]<br />\r\n<br />\r\n-----<br />\r\nAdministrator<br />\r\n[companyname]', 'Company Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nUsername => [username]<br />\r\nPassword => [password]<br />\r\nEmail Address => [email]', 1, 0, 0, 2),
(1, 'overdue_reminder', 'Overdue reminder', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan=\\"3\\">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n        <tr><td align=\\"center\\">\r\n            <table border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\"><tr><td>\r\n                <table width=\\"450\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"0\\">\r\n                    <tr><td bgcolor=\\"white\\">\r\n                        <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\" bgcolor=\\"[head_bg]\\">\r\n                            <tr><td height=\\"20\\"><div align=\\"left\\" style=\\"padding-left:10px; padding-bottom:10px; padding-top:10px;\\"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align=\\"left\\">\r\n                                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\">\r\n                                    <tr>\r\n                                        <td width=\\"36%\\" class=\\''color4\\''><strong>Bank</strong></td>\r\n                                        <td width=\\"64%\\" class=\\''color4\\'' class=\\"style8\\">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Number</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Type</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Number</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date]<br />\r\n<br />\r\nFull Invoice => [invoiceDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno]<br />\r\nOrder Number => [order_number]<br />\r\nEmail Address => [email]<br />\r\n<br />\r\nBank Name => [companyBankName]<br />\r\nBranch Name => [companyBranchName]<br />\r\nBranch Number => [companyBranchNo]<br />\r\nAccount Name => [companyAccountName]<br />\r\nAccount Type => [companyAccountType]<br />\r\nAccount Number => [companyAccountNo]', 1, 0, 0, 4),
(1, 'quotation', 'Quotation', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [quotationDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date]<br />\r\n<br />\r\nQuotation Details => [quotationDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno]<br />\r\nEmail Address => [email]', 1, 0, 0, 3),
(1, 'statement', 'Statement', '<div id=\\''wrapper\\'' align=\\''center\\''>\r\n    <div>\r\n        <table cellpadding=\\''0\\'' cellspacing=\\''0\\'' width=\\''100%\\''>\r\n            <tr valign=\\''top\\''>\r\n                <td align=\\''left\\''>\r\n                    [logo]<br><br>\r\n                    <table cellpadding=\\''0\\'' cellspacing=\\''0\\'' width=\\''100%\\''>\r\n                        <tr valign=\\''top\\''>\r\n                            <td class=\\''normalbold\\'' nowrap width=\\''1%\\''>Bill To</td>\r\n                            <td class=\\''normaltext\\'' style=\\''padding-left:10px;\\''>\r\n                                <div class=\\''normalbold\\''>[firstname] [lastname]</div>\r\n                                [address]\r\n                            </td>\r\n                        </tr>\r\n                        <tr valign=\\''top\\''>\r\n                            <td class=\\''normalbold\\'' nowrap width=\\''1%\\''>Company</td>\r\n                            <td class=\\''normaltext\\'' style=\\''padding-left:10px;\\''>[companyname]</td>\r\n                        </tr>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td class=\\''normalbold\\'' nowrap width=\\''1%\\''>Tel</td>\r\n                            <td class=\\''normaltext\\'' style=\\''padding-left:10px;\\''>[phone]</td>\r\n                        </tr>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td class=\\''normalbold\\'' nowrap width=\\''1%\\''>Email</td>\r\n                            <td class=\\''normaltext\\'' style=\\''padding-left:10px;\\''>[email]</td>\r\n                        </tr>\r\n                    </table>\r\n                </td>\r\n                <td align=\\''right\\'' width=\\''25%\\''>\r\n                    <img src=\\''images/statement.jpg\\''><br><br>\r\n                    <table cellpadding=\\''0\\'' cellspacing=\\''5\\'' width=\\''100%\\''>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td class=\\''normalbold\\'' width=\\''10%\\'' align=\\''right\\'' nowrap>Statement Date</td>\r\n                            <td class=\\''normaltext\\'' align=\\''left\\''>[statement_date]</td>\r\n                        </tr>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td class=\\''normalbold\\'' align=\\''right\\''>Customer ID</td>\r\n                            <td class=\\''normaltext\\'' align=\\''left\\''>[userId]</td>\r\n                        </tr>\r\n                    </table><br><br>\r\n                    <table cellpadding=\\''0\\'' cellspacing=\\''5\\'' width=\\''100%\\''>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td colspan=\\''2\\'' class=\\''account_summary\\''>Account Summary</td>\r\n                        </tr>\r\n                        <tr valign=\\''middle\\''>\r\n                            <td class=\\''normalbold\\'' align=\\''right\\''>Balance Due</td>\r\n                            <td class=\\''normaltext\\''><b>[amount_due]</b></td>\r\n                        </tr>\r\n                    </table>\r\n                    <br>\r\n                    <table cellpadding=\\''0\\'' cellspacing=\\''0\\'' width=\\''100%\\''>\r\n                        <tr valign=\\''middle\\''><td align=\\''left\\'' style=\\''font-size:15px\\''>\r\n                            Your Account Grading : <b>[grade]</b>\r\n                        </td></tr>\r\n                        <tr valign=\\''middle\\''><td align=\\''left\\''>\r\n                            Higher grading receives priority service..<br>read more about \\"<a href=\\''#\\''>Account grading</a>\\".\r\n                        </td></tr>\r\n                    </table>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n    </div>\r\n    \r\n    <div class=\\''clear\\''>&nbsp;</div>[statement]\r\n</div>\r\n<div style=\\''clear:both;padding-bottom:10px;\\''>&nbsp;</div>\r\n<div align=\\''center\\''>Content Goes Here</div>', 'Logo => [logo]<br />\r\nUser Id => [userId]<br />\r\nStatement Date => [statement_date]<br />\r\nBalance Due => [amount_due]<br />\r\nFull Statement => [statement]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nGrade => [grade]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nEmail Address => [email]', 1, 0, 0, 6),
(1, 'statement_email', 'Statement Email', 'Hi [firstname] [lastname],<br />\r\n<br />\r\nPlease find the attached statement.<br />\r\n<br />\r\n-----<br />\r\nAdministrator<br />\r\n[companyname]', 'Company Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nEmail Address => [email]', 1, 0, 0, 6),
(1, 'suspension_warning', 'Suspension warning', '<table width=\\"672\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n<tr><td bgcolor=\\"#CCCCCC\\">\r\n    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"10\\">\r\n        <tr><td align=\\"center\\" bgcolor=\\"#fff\\">\r\n            <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n            <tr><td align=\\"center\\">[logo]</td></tr>\r\n            <tr><td align=\\"right\\"><a href=\\"mailto:[company_account_email]\\">[company_account_email]</a></div></td></tr>\r\n            <tr><td width=\\"91%\\">\r\n                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n                    <tr><td colspan=\\"2\\">\r\n                                        \r\n                    <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"3\\">\r\n                        <tr>\r\n                            <td width=\\"128\\"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width=\\"265\\">[invoiceId] </td>\r\n                            <td width=\\"108\\"><strong>DATE</strong></td>\r\n                            <td width=\\"265\\">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan=\\"3\\">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan=\\"3\\">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width=\\"108\\"><strong>Tel</strong></td>\r\n                            <td colspan=\\"3\\">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan=\\"3\\">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan=\\"3\\">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan=\\"3\\">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan=\\"4\\"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\">\r\n        <tr><td colspan=\\"3\\">\r\n            <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\" class=\\"head_bg\\">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan=\\"3\\"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width=\\"662\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\">\r\n        <tr><td align=\\"center\\">\r\n            <table border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"0\\"><tr><td>\r\n                <table width=\\"450\\" border=\\"0\\" cellspacing=\\"1\\" cellpadding=\\"0\\">\r\n                    <tr><td bgcolor=\\"white\\">\r\n                        <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"0\\" bgcolor=\\"[head_bg]\\">\r\n                            <tr><td height=\\"20\\"><div align=\\"left\\" style=\\"padding-left:10px; padding-bottom:10px; padding-top:10px;\\"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align=\\"left\\">\r\n                                <table width=\\"100%\\" border=\\"0\\" cellspacing=\\"2\\" cellpadding=\\"5\\">\r\n                                    <tr>\r\n                                        <td width=\\"36%\\" class=\\''color4\\''><strong>Bank</strong></td>\r\n                                        <td width=\\"64%\\" class=\\''color4\\'' class=\\"style8\\">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Branch Number</span></td>\r\n                                        <td class=\\''color4\\''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Name</span></td>\r\n                                        <td class=\\''color4\\''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Type</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=\\''color4\\''><strong>Account Number</strong></td>\r\n                                        <td class=\\''color4\\''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', 'Logo => [logo]<br />\r\nCompany Account Email Address => [company_account_email]<br />\r\nInvoice Id/Number => [InvoiceId]<br />\r\nOrder Date => [order_date]<br />\r\n<br />\r\nFull Invoice => [invoiceDetails]<br />\r\n<br />\r\nCompany Name => [companyname]<br />\r\nFirst Name => [firstname]<br />\r\nLast Name => [lastname]<br />\r\nClient Name => [clientname]<br />\r\nPhone Number => [phone]<br />\r\nAddress => [address]<br />\r\nVat No => [vatno]<br />\r\nOrder Number => [order_number]<br />\r\nEmail Address => [email]<br />\r\n<br />\r\nBank Name => [companyBankName]<br />\r\nBranch Name => [companyBranchName]<br />\r\nBranch Number => [companyBranchNo]<br />\r\nAccount Name => [companyAccountName]<br />\r\nAccount Type => [companyAccountType]<br />\r\nAccount Number => [companyAccountNo] ', 1, 0, 0, 4),
(2, 'company', 'New company welcome email', 'Hi [ownername],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator', '[companyname] => Company Name<br />\r\n[ownername] => Owner Name<br />\r\n[username] => Username<br />\r\n[password] => Password<br />\r\n[email]    => Email Address', 1, 0, 0, 12),
(2, 'due_reminder', 'Due reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[invoiceDetails] => Full Invoice<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[order_number] => Order Number<br />\r\n[email]    => Email Address<br />\r\n<br />\r\n[companyBankName] => Bank Name<br />\r\n[companyBranchName] => Branch Name<br />\r\n[companyBranchNo] => Branch Number<br />\r\n[companyAccountName] => Account Name<br />\r\n[companyAccountType] => Account Type<br />\r\n[companyAccountNo] => Account Number', 1, 0, 0, 4);
INSERT INTO `gma_emails` (`companyId`, `template`, `subject`, `content`, `variables`, `status`, `upload`, `update`, `module_id`) VALUES
(2, 'friendly_reminder', 'Friendly reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[invoiceDetails] => Full Invoice<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[order_number] => Order Number<br />\r\n[email]    => Email Address<br />\r\n<br />\r\n[companyBankName] => Bank Name<br />\r\n[companyBranchName] => Branch Name<br />\r\n[companyBranchNo] => Branch Number<br />\r\n[companyAccountName] => Account Name<br />\r\n[companyAccountType] => Account Type<br />\r\n[companyAccountNo] => Account Number', 1, 0, 0, 4),
(2, 'invoice', 'Invoice', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[invoiceDetails] => Full Invoice<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[order_number] => Order Number<br />\r\n[email]    => Email Address<br />\r\n<br />\r\n[companyBankName] => Bank Name<br />\r\n[companyBranchName] => Branch Name<br />\r\n[companyBranchNo] => Branch Number<br />\r\n[companyAccountName] => Account Name<br />\r\n[companyAccountType] => Account Type<br />\r\n[companyAccountNo] => Account Number', 1, 0, 0, 4),
(2, 'login_resend', 'Login resend', 'Hi [name],\r\n\r\nYou have indicated that you have forgotten your password. Please  find the login details.\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', '[companyname] => Company Name<br />\r\n[name] => Name<br />\r\n[username] => Username<br />\r\n[password] => Password<br />\r\n[email]    => Email Address', 1, 0, 0, 0),
(2, 'new_admin', 'New admin welcome email', 'Hi [name],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', '[companyname] => Company Name<br />\r\n[name] => Name<br />\r\n[username] => Username<br />\r\n[password] => Password<br />\r\n[email]    => Email Address', 1, 0, 0, 10),
(2, 'new_client', 'New client welcome email', 'Hi [firstname] [lastname],\r\n\r\nAn account has been created for you with the following details,\r\n\r\nClient Name : [clientname]\r\nCompany Name : [companyname]\r\nUsername : [username]\r\nPassword : [password]\r\nEmail Address : [email]\r\n\r\nPlease click [link] to login. Or copy paste the below URL in browser [link_org]\r\n\r\n-----\r\nAdministrator\r\n[companyname]', '[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[username] => Username<br />\r\n[password] => Password<br />\r\n[email]    => Email Address', 1, 0, 0, 2),
(2, 'overdue_reminder', 'Overdue reminder', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[invoiceDetails] => Full Invoice<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[order_number] => Order Number<br />\r\n[email]    => Email Address<br />\r\n<br />\r\n[companyBankName] => Bank Name<br />\r\n[companyBranchName] => Branch Name<br />\r\n[companyBranchNo] => Branch Number<br />\r\n[companyAccountName] => Account Name<br />\r\n[companyAccountType] => Account Type<br />\r\n[companyAccountNo] => Account Number', 1, 0, 0, 4),
(2, 'quotation', 'Quotation', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [quotationDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[quotationDetails] => Quotation Details<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[email]    => Email Address', 1, 0, 0, 3),
(2, 'statement', 'Statement', '<div id=''wrapper'' align=''center''>\r\n    <div>\r\n        <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n            <tr valign=''top''>\r\n                <td align=''left''>\r\n                    [logo]<br><br>\r\n                    <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n                        <tr valign=''top''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Bill To</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>\r\n                                <div class=''normalbold''>[firstname] [lastname]</div>\r\n                                [address]\r\n                            </td>\r\n                        </tr>\r\n                        <tr valign=''top''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Company</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[companyname]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Tel</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[phone]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' nowrap width=''1%''>Email</td>\r\n                            <td class=''normaltext'' style=''padding-left:10px;''>[email]</td>\r\n                        </tr>\r\n                    </table>\r\n                </td>\r\n                <td align=''right'' width=''25%''>\r\n                    <img src=''images/statement.jpg''><br><br>\r\n                    <table cellpadding=''0'' cellspacing=''5'' width=''100%''>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' width=''10%'' align=''right'' nowrap>Statement Date</td>\r\n                            <td class=''normaltext'' align=''left''>[statement_date]</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' align=''right''>Customer ID</td>\r\n                            <td class=''normaltext'' align=''left''>[userId]</td>\r\n                        </tr>\r\n                    </table><br><br>\r\n                    <table cellpadding=''0'' cellspacing=''5'' width=''100%''>\r\n                        <tr valign=''middle''>\r\n                            <td colspan=''2'' class=''account_summary''>Account Summary</td>\r\n                        </tr>\r\n                        <tr valign=''middle''>\r\n                            <td class=''normalbold'' align=''right''>Balance Due</td>\r\n                            <td class=''normaltext''><b>[amount_due]</b></td>\r\n                        </tr>\r\n                    </table>\r\n                    <br>\r\n                    <table cellpadding=''0'' cellspacing=''0'' width=''100%''>\r\n                        <tr valign=''middle''><td align=''left'' style=''font-size:15px''>\r\n                            Your Account Grading : <b>[grade]</b>\r\n                        </td></tr>\r\n                        <tr valign=''middle''><td align=''left''>\r\n                            Higher grading receives priority service..<br>read more about "<a href=''#''>Account grading</a>".\r\n                        </td></tr>\r\n                    </table>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n    </div>\r\n    \r\n    <div class=''clear''>&nbsp;</div>[statement]\r\n</div>\r\n<div style=''clear:both;padding-bottom:10px;''>&nbsp;</div>\r\n<div align=''center''>Content Goes Here</div>', '[logo] => Logo<br />\r\n[userId] => User Id<br />\r\n[statement_date] => Statement Date<br />\r\n[amount_due] => Balance Due<br />\r\n[statement] => Full Statement<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[grade] => Grade<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[email]    => Email Address', 1, 0, 0, 6),
(2, 'statement_email', 'Statement Email', 'Hi [firstname] [lastname],\r\n\r\nPlease find the attached statement.\r\n\r\n-----\r\nAdministrator\r\n[companyname]', '[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[email]    => Email Address', 1, 0, 0, 6),
(2, 'suspension_warning', 'Suspension warning', '<table width="672" border="0" cellspacing="0" cellpadding="0">\r\n<tr><td bgcolor="#CCCCCC">\r\n    <table width="100%" border="0" cellspacing="1" cellpadding="10">\r\n        <tr><td align="center" bgcolor="#fff">\r\n            <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n            <tr><td align="center">[logo]</td></tr>\r\n            <tr><td align="right"><a href="mailto:[company_account_email]">[company_account_email]</a></div></td></tr>\r\n            <tr><td width="91%">\r\n                <table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n                    <tr><td colspan="2">\r\n                                        \r\n                    <table width="100%" border="0" cellspacing="0" cellpadding="3">\r\n                        <tr>\r\n                            <td width="128"><strong>INVOICE NUMBER</strong></td>\r\n                            <td width="265">[invoiceId] </td>\r\n                            <td width="108"><strong>DATE</strong></td>\r\n                            <td width="265">[order_date] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Attention</strong></td>\r\n                            <td colspan="3">[firstname] [lastname] </td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Company</strong></td>\r\n                            <td colspan="3">[clientname]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td width="108"><strong>Tel</strong></td>\r\n                            <td colspan="3">[phone]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Address</strong></td>\r\n                            <td colspan="3">[address]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                        <tr>\r\n                            <td><strong>VAT Number: </strong></td>\r\n                            <td colspan="3">[vatno]</td>\r\n                        </tr>\r\n                        <tr>\r\n                            <td><strong>Order Number: </strong></td>\r\n                            <td colspan="3">[order_number]</td>\r\n                        </tr>\r\n                        <tr><td colspan="4"></td></tr>\r\n                    </table>\r\n                            \r\n                </td></tr>\r\n            </table>\r\n        </td></tr>\r\n    </table>\r\n\r\n    <table width="662" border="0" cellspacing="2" cellpadding="0">\r\n        <tr><td colspan="3">\r\n            <table width="100%" border="0" cellspacing="2" cellpadding="5" class="head_bg">\r\n                [invoiceDetails]\r\n            </table>\r\n        </td></tr>\r\n        <tr><td colspan="3"></td></tr>\r\n    </table>\r\n    \r\n    Content can be added here.\r\n    \r\n    <table width="662" border="0" cellspacing="0" cellpadding="0">\r\n        <tr><td align="center">\r\n            <table border="0" cellspacing="0" cellpadding="0"><tr><td>\r\n                <table width="450" border="0" cellspacing="1" cellpadding="0">\r\n                    <tr><td bgcolor="white">\r\n                        <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="[head_bg]">\r\n                            <tr><td height="20"><div align="left" style="padding-left:10px; padding-bottom:10px; padding-top:10px;"><strong>BANK DETAILS</strong></div></td></tr>\r\n                            <tr><td align="left">\r\n                                <table width="100%" border="0" cellspacing="2" cellpadding="5">\r\n                                    <tr>\r\n                                        <td width="36%" class=''color4''><strong>Bank</strong></td>\r\n                                        <td width="64%" class=''color4'' class="style8">[companyBankName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Name</span></td>\r\n                                        <td class=''color4''>[companyBranchName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Branch Number</span></td>\r\n                                        <td class=''color4''>[companyBranchNo]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Name</span></td>\r\n                                        <td class=''color4''>[companyAccountName]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Type</strong></td>\r\n                                        <td class=''color4''>[companyAccountType]</td>\r\n                                    </tr>\r\n                                    <tr>\r\n                                        <td class=''color4''><strong>Account Number</strong></td>\r\n                                        <td class=''color4''>[companyAccountNo]</td>\r\n                                    </tr>\r\n                                </table>\r\n                            </td></tr>\r\n                        </table>\r\n                    </td></tr>\r\n                </table>\r\n            </td></tr></table>\r\n        </td></tr>\r\n    </table>\r\n</td></tr>\r\n</table>', '[logo] => Logo<br />\r\n[company_account_email] => Company Account Email Address<br />\r\n[InvoiceId] => Invoice Id/Number<br />\r\n[order_date] => Order Date<br />\r\n<br />\r\n[invoiceDetails] => Full Invoice<br />\r\n<br />\r\n[companyname] => Company Name<br />\r\n[firstname] => First Name<br />\r\n[lastname] => Last Name<br />\r\n[clientname] => Client Name<br />\r\n[phone] => Phone Number<br />\r\n[address] => Address<br />\r\n[vatno] => Vat No<br />\r\n[order_number] => Order Number<br />\r\n[email]    => Email Address<br />\r\n<br />\r\n[companyBankName] => Bank Name<br />\r\n[companyBranchName] => Branch Name<br />\r\n[companyBranchNo] => Branch Number<br />\r\n[companyAccountName] => Account Name<br />\r\n[companyAccountType] => Account Type<br />\r\n[companyAccountNo] => Account Number', 1, 0, 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `gma_grading`
--

CREATE TABLE IF NOT EXISTS `gma_grading` (
  `companyId` int(11) NOT NULL,
  `grade_1` text NOT NULL,
  `grade_2` text NOT NULL,
  `grade_3` text NOT NULL,
  UNIQUE KEY `companyId` (`companyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_grading`
--

INSERT INTO `gma_grading` (`companyId`, `grade_1`, `grade_2`, `grade_3`) VALUES
(0, 'Grading 1', 'Grading 2', 'Grading 3'),
(1, 'Grading 1', 'Grading 2', 'Grading 3'),
(3, 'Grading 1', 'Grading 2', 'Grading 3');

-- --------------------------------------------------------

--
-- Table structure for table `gma_groups`
--

CREATE TABLE IF NOT EXISTS `gma_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `discount` int(1) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `gma_groups`
--

INSERT INTO `gma_groups` (`id`, `companyId`, `name`, `discount`, `order`, `status`) VALUES
(1, 1, 'Group 1', 1, 0, 0),
(2, 1, 'Group 2', 1, 0, 1),
(3, 1, 'Group 3', 1, 0, 1),
(4, 1, 'Group 4', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_logins`
--

CREATE TABLE IF NOT EXISTS `gma_logins` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `userName` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userType` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'normal',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `gma_logins`
--

INSERT INTO `gma_logins` (`userId`, `companyId`, `userName`, `password`, `email`, `userType`, `status`) VALUES
(1, 1, 'admin', 'admin', 'admin@gnetmail.co.za', 'gnet_admin', 1),
(3, 1, 'subadmin', 'subadmin', 'subadmin@gmail.com', 'account_admin', 1),
(4, 1, 'client', 'client', 'client@gmail.com', 'normal', 1),
(5, 3, 'com_owner', 'com_owner', 'com_owner@gmail.com', 'super_admin', 1),
(6, 1, 'username', 'password', 'Email@client.com', 'normal', 1),
(7, 1, 'dsfdsfsdfd', 'jhj', 'bnvhvh@kjkkj.com', 'normal', 1),
(8, 1, 'dsfdsfsdfd', 'jhj', 'bnvhvh@kjkkj.com', 'normal', 1),
(9, 1, 'ffdsdsf', 'hjhjk', 'hjjkhkj@dfgf.ddd', 'normal', 1),
(10, 4, 'testtest', 'test', 'test@test.com', 'super_admin', 1),
(11, 5, 'sfdsf', 'sdfsdf', 'sdfsdf@fdgdf.dfgd', 'super_admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_modules`
--

CREATE TABLE IF NOT EXISTS `gma_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `default` int(1) NOT NULL DEFAULT '1',
  `display` int(1) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '1',
  `filename` varchar(200) NOT NULL,
  `class` varchar(15) NOT NULL,
  `order` int(11) NOT NULL,
  `menu` int(1) NOT NULL COMMENT '1 - Top, 2 - Main',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `gma_modules`
--

INSERT INTO `gma_modules` (`id`, `name`, `default`, `display`, `status`, `filename`, `class`, `order`, `menu`) VALUES
(1, 'Home Module', 1, 0, 1, 'dashboard.php', 'summary_btn', 1, 2),
(2, 'Client Module', 1, 1, 1, 'users.php', 'user_btn', 2, 2),
(3, 'Quotation Module', 0, 1, 1, 'quotations.php', 'quotation_btn', 3, 2),
(4, 'Invoice Module', 1, 1, 1, 'invoices.php', 'invoice_btn', 4, 2),
(5, 'My Details', 1, 0, 1, 'myprofile.php', 'profile_btn', 1, 1),
(6, 'Statement Module', 1, 1, 1, 'myaccount.php', 'account_btn', 6, 2),
(7, 'Payment Module', 1, 1, 1, 'payments.php', 'payment_btn', 7, 2),
(8, 'Pricing Module', 1, 1, 1, 'pricing.php', 'pricing_btn', 8, 2),
(9, 'Services', 1, 1, 1, 'services.php', '', 9, 1),
(10, 'Admin Users', 1, 1, 1, 'admins.php', '', 10, 1),
(11, 'Theme', 1, 1, 1, 'themes.php', '', 11, 1),
(12, 'Companies', 0, 0, 1, 'company.php', '', 12, 1),
(13, 'Terms', 1, 1, 1, 'accounts.php', '', 13, 1),
(14, 'Messages', 1, 1, 1, 'emails.php', '', 14, 1),
(15, 'Grading', 1, 1, 1, 'grading.php', '', 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_order`
--

CREATE TABLE IF NOT EXISTS `gma_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `invoice_amount` double NOT NULL,
  `orderDate` datetime NOT NULL,
  `orderStatus` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gma_order`
--

INSERT INTO `gma_order` (`id`, `userId`, `invoiceId`, `order_number`, `invoice_amount`, `orderDate`, `orderStatus`) VALUES
(1, 4, 1008, '', 6650, '2012-03-05 06:30:28', 1),
(2, 4, 1009, '', 4500, '2012-04-05 06:32:04', 0),
(3, 6, 1007, '', 2000, '2012-04-10 10:02:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_order_details`
--

CREATE TABLE IF NOT EXISTS `gma_order_details` (
  `orderId` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `serviceName` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cost` bigint(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `amount` float NOT NULL,
  `discount` int(11) NOT NULL,
  `exportStatus` enum('false','true') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_order_details`
--

INSERT INTO `gma_order_details` (`orderId`, `group_id`, `service_id`, `serviceName`, `cost`, `quantity`, `amount`, `discount`, `exportStatus`) VALUES
(1, 1, 1, 'Email campaign - basic', 450, 10, 4050, 10, 'false'),
(1, 2, 7, 'SEND CREDITS - Pay As You Go', 260, 10, 2600, 0, 'false'),
(2, 1, 2, 'Email newsletter creation', 450, 10, 4500, 0, 'false'),
(3, 1, 5, 'MONTHLY CONTRACT', 200, 10, 2000, 0, 'false');

-- --------------------------------------------------------

--
-- Table structure for table `gma_payments`
--

CREATE TABLE IF NOT EXISTS `gma_payments` (
  `paymentId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `date` date NOT NULL,
  `amount` double NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`paymentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `gma_payments`
--

INSERT INTO `gma_payments` (`paymentId`, `userId`, `description`, `date`, `amount`, `created`) VALUES
(3, 4, '', '2012-04-11', 2000, '2012-04-16 19:21:39'),
(4, 6, '', '2012-04-20', 3000, '2012-04-16 19:21:39'),
(5, 4, '', '2012-04-16', 4650, '2012-04-16 19:50:02'),
(6, 6, '', '2012-04-16', 0, '2012-04-16 20:14:37');

-- --------------------------------------------------------

--
-- Table structure for table `gma_payment_order`
--

CREATE TABLE IF NOT EXISTS `gma_payment_order` (
  `orderId` int(11) NOT NULL,
  `paymentId` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  KEY `orderId` (`orderId`,`paymentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_payment_order`
--

INSERT INTO `gma_payment_order` (`orderId`, `paymentId`, `amount`) VALUES
(1, 0, 6650),
(1, 5, 6650),
(3, 6, 2000);

-- --------------------------------------------------------

--
-- Table structure for table `gma_poll`
--

CREATE TABLE IF NOT EXISTS `gma_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `username` varchar(200) NOT NULL,
  `option` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gma_poll`
--

INSERT INTO `gma_poll` (`id`, `form_id`, `email`, `username`, `option`, `comment`, `created`) VALUES
(2, 10, 'psvinayak@gmail.com', 'psvinayak', '1', 'sdfsdf\r\nsd\r\nf\r\nsdf\r\nsd\r\nfsd\r\nf\r\nsdf', '2012-04-22 21:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `gma_quotation`
--

CREATE TABLE IF NOT EXISTS `gma_quotation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `invoice_amount` double NOT NULL,
  `orderDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gma_quotation`
--


-- --------------------------------------------------------

--
-- Table structure for table `gma_quotation_details`
--

CREATE TABLE IF NOT EXISTS `gma_quotation_details` (
  `quotationId` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `serviceName` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cost` bigint(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `amount` float NOT NULL,
  `discount` int(11) NOT NULL,
  `exportStatus` enum('false','true') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  KEY `orderId` (`quotationId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_quotation_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `gma_services`
--

CREATE TABLE IF NOT EXISTS `gma_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `service_name` varchar(300) NOT NULL,
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `gma_services`
--

INSERT INTO `gma_services` (`id`, `group_id`, `service_name`, `amount`, `order`, `status`) VALUES
(1, 1, 'Email campaign - basic', 450, 1, 1),
(2, 1, 'Email newsletter creation', 450, 2, 1),
(3, 1, 'General labour', 270, 3, 1),
(4, 1, 'Mailing List services', 240, 4, 1),
(5, 1, 'MONTHLY CONTRACT', 200, 5, 1),
(6, 2, 'Picture advert creation', 350, 1, 1),
(7, 2, 'SEND CREDITS - Pay As You Go', 260, 2, 1),
(8, 2, 'Send to a friend system', 360, 3, 1),
(9, 3, 'Specialised Labour', 400, 1, 1),
(10, 3, 'Starter Package', 1600, 2, 1),
(11, 3, 'Subscription form', 360, 3, 1),
(12, 4, 'Template creation', 760, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gma_theme`
--

CREATE TABLE IF NOT EXISTS `gma_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `theme` varchar(10) NOT NULL,
  `head_bg` char(7) NOT NULL,
  `head_color` char(7) NOT NULL,
  `color1` char(7) NOT NULL,
  `color2` char(7) NOT NULL,
  `color3` char(7) NOT NULL,
  `color4` char(7) NOT NULL,
  `default` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `gma_theme`
--

INSERT INTO `gma_theme` (`id`, `name`, `theme`, `head_bg`, `head_color`, `color1`, `color2`, `color3`, `color4`, `default`) VALUES
(1, 'Theme 1', 'theme1', '#6B9529', '#FFFFFF', '#DFDEA6', '#777726', '#CCCC66', '#EFF3DE', 1),
(2, 'Theme 2', 'theme2', '#6B9529', '#FFFFFF', '#E5E8F7', '#7A8AAC', '#C6CFE0', '#F6F8FF', 0),
(3, 'Theme 3', 'theme3', '#6B9529', '#FFFFFF', '#FFE16A', '#FF9900', '#FFC46A', '#FFF5DF', 0),
(4, 'Theme 4', 'theme4', '#6B9529', '#FFFFFF', '#FFCEE7', '#FF99CC', '#FF99CC', '#FFEFFF', 0);

-- --------------------------------------------------------

--
-- Table structure for table `gma_user_details`
--

CREATE TABLE IF NOT EXISTS `gma_user_details` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `lastName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `businessName` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ownerFirstName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ownerLastName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ownerEmail` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ownerPhone` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `invoice` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vatNo` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `InvPhone` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `paymentMethod` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `paymentDetailsMethod` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `bankName` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `accountName` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `accountType` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `accountNo` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `branchCode` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userStatus` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `joinDate` datetime DEFAULT '0000-00-00 00:00:00',
  `invoiceNo` bigint(20) DEFAULT '0',
  `lead` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `grade` int(11) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_user_details`
--

INSERT INTO `gma_user_details` (`userId`, `firstName`, `lastName`, `businessName`, `phone`, `ownerFirstName`, `ownerLastName`, `ownerEmail`, `ownerPhone`, `invoice`, `vatNo`, `address`, `InvPhone`, `paymentMethod`, `paymentDetailsMethod`, `bankName`, `accountName`, `accountType`, `accountNo`, `branchCode`, `userStatus`, `joinDate`, `invoiceNo`, `lead`, `grade`) VALUES
(4, 'Client', 'Client', 'Client', '123456', '', '', '', '', NULL, '123456', '', NULL, '', '', '', '', '', '', '', '', '2012-04-04 00:00:00', 0, '', 1),
(6, 'First Name', 'Last Name	', 'Client name', '11111', '', '', '', '', NULL, '234e324', '', NULL, '', '', '', '', '', '', '', '', '2012-04-04 00:00:00', 0, '', 0),
(9, 'kjkhk', 'jhkj', 'jkhjhhkjh', 'kjhkjhkj', '', '', '', '', NULL, 'knkjklj', '', NULL, '', '', '', '', '', '', '', '', '2012-04-10 00:00:00', 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `gma_user_discount`
--

CREATE TABLE IF NOT EXISTS `gma_user_discount` (
  `userId` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `discount` float NOT NULL,
  UNIQUE KEY `userId` (`userId`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gma_user_discount`
--


-- --------------------------------------------------------

--
-- Table structure for table `gm_last_invoice`
--

CREATE TABLE IF NOT EXISTS `gm_last_invoice` (
  `invoiceno` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gm_last_invoice`
--

INSERT INTO `gm_last_invoice` (`invoiceno`) VALUES
(1009);
