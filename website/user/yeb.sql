/*
 Navicat Premium Dump SQL

 Source Server         : Y
 Source Server Type    : MySQL
 Source Server Version : 80039 (8.0.39)
 Source Host           : localhost:3306
 Source Schema         : yeb

 Target Server Type    : MySQL
 Target Server Version : 80039 (8.0.39)
 File Encoding         : 65001

 Date: 11/10/2024 12:19:33
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for i1
-- ----------------------------
DROP TABLE IF EXISTS `i1`;
CREATE TABLE `i1`  (
  `id` int NOT NULL,
  `bir` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  `bag` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  `other` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  PRIMARY KEY (`id`, `bir`, `bag`, `other`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_croatian_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of i1
-- ----------------------------
INSERT INTO `i1` VALUES (13, '2010-10-13 23:27:24', '9', '9');
INSERT INTO `i1` VALUES (16, '2002-04-07 00:04:52', '14', '14');
INSERT INTO `i1` VALUES (16, '2005-02-22 06:27:29', '2', '2');
INSERT INTO `i1` VALUES (16, '2018-06-28 13:26:05', '13', '13');
INSERT INTO `i1` VALUES (17, '2007-03-10 01:37:09', '10', '10');
INSERT INTO `i1` VALUES (22, '2023-10-13 05:01:37', '17', '17');
INSERT INTO `i1` VALUES (29, '2007-08-15 21:40:34', '20', '20');
INSERT INTO `i1` VALUES (31, '2000-12-30 01:12:56', '3', '3');
INSERT INTO `i1` VALUES (33, '2010-02-05 20:26:56', '16', '16');
INSERT INTO `i1` VALUES (40, '2017-07-27 01:12:54', '4', '4');
INSERT INTO `i1` VALUES (41, '2014-01-15 11:42:00', '11', '11');
INSERT INTO `i1` VALUES (45, '2011-07-28 13:13:32', '1', '1');
INSERT INTO `i1` VALUES (46, '2009-11-11 06:08:14', '8', '8');
INSERT INTO `i1` VALUES (46, '2021-07-30 00:59:26', '12', '12');
INSERT INTO `i1` VALUES (48, '2010-12-13 12:55:27', '18', '18');
INSERT INTO `i1` VALUES (62, '2015-07-22 22:45:51', '7', '7');
INSERT INTO `i1` VALUES (66, '2009-11-27 16:08:20', '5', '5');
INSERT INTO `i1` VALUES (74, '2012-08-01 14:50:54', '15', '15');
INSERT INTO `i1` VALUES (80, '2007-01-04 11:48:35', '6', '6');
INSERT INTO `i1` VALUES (95, '2012-07-13 08:58:05', '19', '19');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  `passwed` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  PRIMARY KEY (`id`, `name`, `passwed`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_croatian_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (111, 'yxs', '150051');
INSERT INTO `user` VALUES (112, 'yxh', '12345');
INSERT INTO `user` VALUES (113, 'yry', '54321');

SET FOREIGN_KEY_CHECKS = 1;
