/*
 Navicat MySQL Data Transfer

 Source Server         : 神织知更
 Source Server Type    : MySQL
 Source Server Version : 50736
 Source Host           : localhost:3306
 Source Schema         : www_ruiwen_com

 Target Server Type    : MySQL
 Target Server Version : 50736
 File Encoding         : 65001

 Date: 11/05/2022 09:48:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发货人',
  `address` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户地址',
  `phone` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '电话',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  PRIMARY KEY (`id`, `user_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_address_user_idx`(`user_id`) USING BTREE,
  CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `phone` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '电话',
  `email` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `sex` tinyint(4) NOT NULL DEFAULT 1 COMMENT '性别：0 保密 1 男 2 女',
  `scope` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '权限',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '用户状态：0 未认证 1 允许 2 禁止',
  `last_ip` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上次登录ip',
  `last_time` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上次登录时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role`  (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`admin_id`, `role_id`) USING BTREE,
  INDEX `fk_admin_has_role_role1_idx`(`role_id`) USING BTREE,
  INDEX `fk_admin_has_role_admin1_idx`(`admin_id`) USING BTREE,
  CONSTRAINT `fk_admin_has_role_admin1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_admin_has_role_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for akali
-- ----------------------------
DROP TABLE IF EXISTS `akali`;
CREATE TABLE `akali`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of akali
-- ----------------------------
INSERT INTO `akali` VALUES (4, '阿卡丽');
INSERT INTO `akali` VALUES (5, '金克丝');
INSERT INTO `akali` VALUES (6, '锐雯');

-- ----------------------------
-- Table structure for node
-- ----------------------------
DROP TABLE IF EXISTS `node`;
CREATE TABLE `node`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '节点',
  `title` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '节点标题',
  `level` tinyint(3) NOT NULL COMMENT '节点的层级',
  `pid` int(11) NOT NULL COMMENT '父级id',
  `show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`  (
  `id` int(11) UNSIGNED NOT NULL,
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `image` char(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品图片',
  `market_price` double(10, 2) NOT NULL COMMENT '市场价',
  `shop_price` double(10, 2) NOT NULL COMMENT '实际购买价',
  `stock` int(11) NOT NULL COMMENT '库存',
  `sales` int(11) NOT NULL DEFAULT 0 COMMENT '销量',
  `is_hot` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否热卖',
  `spec_type` tinyint(1) NOT NULL COMMENT '规格：0 单一 1 多个',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `product_category_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `product_category_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_product_product_category1_idx`(`product_category_id`) USING BTREE,
  CONSTRAINT `fk_product_product_category1` FOREIGN KEY (`product_category_id`) REFERENCES `product_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES (16, '苹果12', '寒蝉鸣泣之时 7月一号.png', 3800.00, 3700.00, 15, 0, 1, 1, 1640166830, NULL, 1640166830, 13);
INSERT INTO `product` VALUES (17, '苹果13', '寒蝉鸣泣之时 7月一号.png', 7000.00, 6900.00, 32, 0, 1, 1, 1640166892, NULL, 1640228571, 1);
INSERT INTO `product` VALUES (18, '苹果SE2', '寒蝉鸣泣之时 7月一号.png', 1200.00, 1100.00, 15, 0, 0, 1, 1650719813, NULL, 1650808594, 2);

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父级id： 顶级id为 0',
  `name` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `pic` char(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类图标',
  `sort` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序：默认0 数值越大，越靠前',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_category
-- ----------------------------
INSERT INTO `product_category` VALUES (1, 0, '苹果', 'fdlghdojg', 15, 1639550157, 1639972678, NULL);
INSERT INTO `product_category` VALUES (2, 0, '平板电脑', '165145', 14, 1639550963, 1639550963, NULL);
INSERT INTO `product_category` VALUES (3, 0, '女装', '', 13, 1639639861, 1639640619, NULL);
INSERT INTO `product_category` VALUES (4, 0, '游戏设备', '', 12, 1639640908, 1639640918, NULL);
INSERT INTO `product_category` VALUES (13, 0, '手机', '', 11, 1640141516, 1640141516, NULL);

-- ----------------------------
-- Table structure for product_detail
-- ----------------------------
DROP TABLE IF EXISTS `product_detail`;
CREATE TABLE `product_detail`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品详情',
  `product_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `product_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_product_detail_product1_idx`(`product_id`) USING BTREE,
  CONSTRAINT `fk_product_detail_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for product_img
-- ----------------------------
DROP TABLE IF EXISTS `product_img`;
CREATE TABLE `product_img`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` char(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图片路径\n',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`, `product_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_product_img_product1_idx`(`product_id`) USING BTREE,
  CONSTRAINT `fk_product_img_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_img
-- ----------------------------
INSERT INTO `product_img` VALUES (1, '/upload/20220712/jinx.jpg', 18, 1650722854);
INSERT INTO `product_img` VALUES (3, 'akali.jpg', 18, 1650730395);
INSERT INTO `product_img` VALUES (4, 'jinx.jpg', 18, 1650730395);
INSERT INTO `product_img` VALUES (5, 'akali.jpg', 18, 1650730484);
INSERT INTO `product_img` VALUES (6, 'jinx.jpg', 18, 1650730484);
INSERT INTO `product_img` VALUES (7, 'akali.jpg', 18, 1650801212);
INSERT INTO `product_img` VALUES (8, 'jinx.jpg', 18, 1650801212);
INSERT INTO `product_img` VALUES (9, 'akali.jpg', 18, 1650801367);
INSERT INTO `product_img` VALUES (10, 'jinx.jpg', 18, 1650801367);
INSERT INTO `product_img` VALUES (11, 'ruiwen.jpg', 18, NULL);
INSERT INTO `product_img` VALUES (12, 'akali.jpg', 18, 1650801409);
INSERT INTO `product_img` VALUES (13, 'jinx.jpg', 18, 1650801409);
INSERT INTO `product_img` VALUES (14, 'akali.jpg', 18, NULL);
INSERT INTO `product_img` VALUES (15, 'jinx.jpg', 18, NULL);

-- ----------------------------
-- Table structure for product_specs
-- ----------------------------
DROP TABLE IF EXISTS `product_specs`;
CREATE TABLE `product_specs`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `specs_value_id` char(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格id串',
  `price` double(10, 2) NOT NULL COMMENT '规格价格',
  `stock` int(11) NOT NULL COMMENT '货存',
  `sales` int(11) NOT NULL DEFAULT 0 COMMENT '销售量',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`, `product_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_product_specs_product1_idx`(`product_id`) USING BTREE,
  CONSTRAINT `fk_product_specs_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 134 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specs
-- ----------------------------
INSERT INTO `product_specs` VALUES (88, '1,12,19', 3700.00, 12, 0, 1640166830, 1640166830, 16, NULL);
INSERT INTO `product_specs` VALUES (89, '1,12,20', 4000.00, 3, 0, 1640166830, 1640166830, 16, NULL);
INSERT INTO `product_specs` VALUES (98, '4,12,19', 6900.00, 12, 0, 1640224435, 1640228571, 17, 1640228571);
INSERT INTO `product_specs` VALUES (99, '5,12,20', 8000.00, 3, 0, 1640224435, 1640228571, 17, 1640228571);
INSERT INTO `product_specs` VALUES (100, '4,12,19', 6900.00, 12, 0, 1640228571, 1640228571, 17, NULL);
INSERT INTO `product_specs` VALUES (101, '5,12,20', 8000.00, 3, 0, 1640228571, 1640228571, 17, NULL);
INSERT INTO `product_specs` VALUES (102, '4,12,19', 6900.00, 12, 0, 1650719813, 1650724604, 18, 1650724604);
INSERT INTO `product_specs` VALUES (103, '5,12,20', 8000.00, 3, 0, 1650719813, 1650724604, 18, 1650724604);
INSERT INTO `product_specs` VALUES (122, '4,12,21', 6900.00, 12, 0, 1650729946, 1650730395, 18, 1650730395);
INSERT INTO `product_specs` VALUES (123, '5,12,20', 8000.00, 3, 0, 1650729946, 1650730395, 18, 1650730395);
INSERT INTO `product_specs` VALUES (124, '4,12,21', 6900.00, 12, 0, 1650730395, 1650730484, 18, 1650730484);
INSERT INTO `product_specs` VALUES (125, '5,12,20', 8000.00, 3, 0, 1650730395, 1650730484, 18, 1650730484);
INSERT INTO `product_specs` VALUES (126, '4,12,21', 6900.00, 12, 0, 1650730484, 1650801212, 18, 1650801212);
INSERT INTO `product_specs` VALUES (127, '5,12,20', 8000.00, 3, 0, 1650730484, 1650801212, 18, 1650801212);
INSERT INTO `product_specs` VALUES (128, '4,12,19', 6900.00, 12, 0, 1650801212, 1650801367, 18, 1650801367);
INSERT INTO `product_specs` VALUES (129, '5,12,20', 8000.00, 3, 0, 1650801212, 1650801367, 18, 1650801367);
INSERT INTO `product_specs` VALUES (130, '4,12,19', 6900.00, 12, 0, 1650801367, 1650801409, 18, 1650801409);
INSERT INTO `product_specs` VALUES (131, '5,12,20', 8000.00, 3, 0, 1650801367, 1650801409, 18, 1650801409);
INSERT INTO `product_specs` VALUES (132, '4,12,19', 6900.00, 12, 0, 1650801409, 1650808594, 18, NULL);
INSERT INTO `product_specs` VALUES (133, '5,12,20', 8000.00, 3, 0, 1650801409, 1650808594, 18, NULL);

-- ----------------------------
-- Table structure for product_specs_name
-- ----------------------------
DROP TABLE IF EXISTS `product_specs_name`;
CREATE TABLE `product_specs_name`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格名称',
  `sort` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specs_name
-- ----------------------------
INSERT INTO `product_specs_name` VALUES (1, '颜色', 1, 1640076052, 1640077675, NULL);
INSERT INTO `product_specs_name` VALUES (2, '尺寸', 0, 1640076227, 1640076227, NULL);
INSERT INTO `product_specs_name` VALUES (3, '布料', 0, 1640076321, 1640076321, NULL);
INSERT INTO `product_specs_name` VALUES (4, '材料', 0, 1640076331, 1640076331, NULL);
INSERT INTO `product_specs_name` VALUES (5, '包邮', 0, 1640076444, 1640076444, NULL);
INSERT INTO `product_specs_name` VALUES (6, '英雄', 0, 1640077791, 1640078956, NULL);
INSERT INTO `product_specs_name` VALUES (7, '网络', 0, 1640077816, 1640077816, NULL);

-- ----------------------------
-- Table structure for product_specs_value
-- ----------------------------
DROP TABLE IF EXISTS `product_specs_value`;
CREATE TABLE `product_specs_value`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格值',
  `specs_name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格名称',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `product_specs_name_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `product_specs_name_id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE,
  INDEX `fk_product_specs_value_profuct_specs_name1_idx`(`product_specs_name_id`) USING BTREE,
  CONSTRAINT `fk_product_specs_value_profuct_specs_name1` FOREIGN KEY (`product_specs_name_id`) REFERENCES `product_specs_name` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specs_value
-- ----------------------------
INSERT INTO `product_specs_value` VALUES (1, '黑色', '颜色', 1640081638, 1640081638, 1);
INSERT INTO `product_specs_value` VALUES (3, '白色', '颜色', 1640081835, 1640081835, 1);
INSERT INTO `product_specs_value` VALUES (4, '红色', '颜色', 1640081880, 1640081880, 1);
INSERT INTO `product_specs_value` VALUES (5, '天蓝色', '颜色', 1640082001, 1640082001, 1);
INSERT INTO `product_specs_value` VALUES (6, '粉色', '颜色', 1640082063, 1640082063, 1);
INSERT INTO `product_specs_value` VALUES (7, '42', '尺寸', 1640083329, 1640083329, 2);
INSERT INTO `product_specs_value` VALUES (8, '41', '尺寸', 1640134762, 1640134762, 2);
INSERT INTO `product_specs_value` VALUES (9, '40', '尺寸', 1640134766, 1640134766, 2);
INSERT INTO `product_specs_value` VALUES (10, '36', '尺寸', 1640134799, 1640134799, 2);
INSERT INTO `product_specs_value` VALUES (11, '不包邮', '包邮', 1640137921, 1640137921, 5);
INSERT INTO `product_specs_value` VALUES (12, '包邮', '包邮', 1640239884, 1640239884, 5);
INSERT INTO `product_specs_value` VALUES (13, '皮质', '材料', 1640139396, 1640139396, 3);
INSERT INTO `product_specs_value` VALUES (14, '布料', '材料', 1640139405, 1640139405, 3);
INSERT INTO `product_specs_value` VALUES (15, '阿卡丽', '英雄', 1640139626, 1640139626, 6);
INSERT INTO `product_specs_value` VALUES (17, '细线', '布料', 1640139675, 1640139675, 3);
INSERT INTO `product_specs_value` VALUES (18, '粗线', '布料', 1640139691, 1640139691, 3);
INSERT INTO `product_specs_value` VALUES (19, '4G', '网络', 1640141336, 1640141336, 7);
INSERT INTO `product_specs_value` VALUES (20, '5G', '网络', 1640141339, 1640141339, 7);
INSERT INTO `product_specs_value` VALUES (21, '6G', '网络', 1640239961, 1640239961, 7);

-- ----------------------------
-- Table structure for product_type
-- ----------------------------
DROP TABLE IF EXISTS `product_type`;
CREATE TABLE `product_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品类型名称',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色名',
  `explain` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色说明',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for role_node
-- ----------------------------
DROP TABLE IF EXISTS `role_node`;
CREATE TABLE `role_node`  (
  `role_id` int(10) UNSIGNED NOT NULL,
  `node_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `node_id`) USING BTREE,
  INDEX `fk_role_has_node_node1_idx`(`node_id`) USING BTREE,
  INDEX `fk_role_has_node_role1_idx`(`role_id`) USING BTREE,
  CONSTRAINT `fk_role_has_node_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_has_node_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `nick_name` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `avatar` char(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `gender` tinyint(1) NULL DEFAULT NULL COMMENT '性别 ：0 保密 1  男 2 女',
  `phone` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `email` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `scope` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '权限',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '用户状态：0 未认证 1 允许 2 禁止',
  `last_ip` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上次登录ip',
  `last_time` int(11) NULL DEFAULT NULL COMMENT '上次登录时间',
  `openid` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_UNIQUE`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, '阿卡丽', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, '15119498976', '1131271533@qq.com', '32', 1, '127.0.0.1', 1646029987, NULL, 1354656434, 1646029987, NULL);
INSERT INTO `user` VALUES (2, NULL, NULL, '微信用户', 'https://thirdwx.qlogo.cn/mmopen/vi_32/POgEwh4mIHO4nibH0KlMECNjjGxQUq24ZEaGT4poC6icRiccVGKSyXwibcPq4BWmiaIGuG1icwxaQX6grC9VemZoJ8rg/132', 0, NULL, NULL, NULL, 1, '127.0.0.1', 1640398475, 'onUqQ5ToVmIQxjY7cOWE5gVf7b44', 1640398475, 1645863585, NULL);

SET FOREIGN_KEY_CHECKS = 1;
