SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `matyisland` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `matyisland`;

-- -----------------------------------------------------
-- Table `matyisland`.`category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`category` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`cat_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`product` (
  `prod_id` INT NOT NULL AUTO_INCREMENT ,
  `prod_name` VARCHAR(45) NOT NULL ,
  `prod_producer` VARCHAR(45) NOT NULL ,
  `prod_price` INT NOT NULL ,
  `prod_describe` VARCHAR(255) NULL ,
  `prod_isnew` BOOLEAN NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`prod_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`image`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`image` (
  `image_id` INT NOT NULL AUTO_INCREMENT ,
  `image_path` BLOB NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`image_id`, `product_prod_id`) ,
  INDEX `fk_image_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_image_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`basket`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`basket` (
  `basket_id` INT NOT NULL AUTO_INCREMENT ,
  `basket_session_id` VARCHAR(50) NOT NULL ,
  `basket_quantity` INT NOT NULL DEFAULT 0 ,
  `basket_ip_address` VARCHAR(50) NOT NULL ,
  `basket_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `user_id` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`basket_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_login` VARCHAR(45) NOT NULL ,
  `user_password` VARCHAR(100) NOT NULL ,
  `user_email` VARCHAR(45) NOT NULL ,
  `basket_basket_id` INT NULL ,
  `user_name` VARCHAR(45) NOT NULL ,
  `user_surname` VARCHAR(45) NOT NULL ,
  `user_telefon` VARCHAR(9) NOT NULL ,
  `user_street` VARCHAR(45) NOT NULL ,
  `user_city` VARCHAR(45) NOT NULL ,
  `user_psc` VARCHAR(5) NOT NULL ,
  `user_firmName` VARCHAR(45) NULL ,
  `user_ico` VARCHAR(8) NULL ,
  `user_dic` VARCHAR(12) NULL ,
  PRIMARY KEY (`user_id`) ,
  INDEX `fk_user_basket` (`basket_basket_id` ASC) ,
  CONSTRAINT `fk_user_basket`
    FOREIGN KEY (`basket_basket_id` )
    REFERENCES `matyisland`.`basket` (`basket_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`order`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`order` (
  `ord_id` INT NOT NULL AUTO_INCREMENT ,
  `ord_date` DATE NOT NULL ,
  `user_user_id` INT NULL ,
  `cust_name` VARCHAR(45) NOT NULL ,
  `cust_surname` VARCHAR(45) NOT NULL ,
  `cust_email` VARCHAR(45) NOT NULL ,
  `cust_telefon` VARCHAR(9) NOT NULL ,
  `cust_street` VARCHAR(45) NOT NULL ,
  `cust_city` VARCHAR(45) NOT NULL ,
  `cust_psc` VARCHAR(5) NOT NULL ,
  `cust_firmName` VARCHAR(45) NULL ,
  `cust_ico` VARCHAR(8) NULL ,
  `cust_dic` VARCHAR(12) NULL ,
  PRIMARY KEY (`ord_id`) ,
  INDEX `fk_order_user` (`user_user_id` ASC) ,
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`user_user_id` )
    REFERENCES `matyisland`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`category_has_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`category_has_product` (
  `category_cat_id` INT NOT NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`category_cat_id`, `product_prod_id`) ,
  INDEX `fk_category_has_product_category` (`category_cat_id` ASC) ,
  INDEX `fk_category_has_product_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_category_has_product_category`
    FOREIGN KEY (`category_cat_id` )
    REFERENCES `matyisland`.`category` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_has_product_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `matyisland`.`basket_has_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`basket_has_product` (
  `basket_basket_id` INT NOT NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`basket_basket_id`, `product_prod_id`) ,
  INDEX `fk_basket_has_product_basket` (`basket_basket_id` ASC) ,
  INDEX `fk_basket_has_product_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_basket_has_product_basket`
    FOREIGN KEY (`basket_basket_id` )
    REFERENCES `matyisland`.`basket` (`basket_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_basket_has_product_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `matyisland`.`order_has_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`order_has_product` (
  `order_ord_id` INT NOT NULL ,
  `product_prod_id` INT NOT NULL ,
  `quantity` INT NOT NULL ,
  `actual_price_of_product` INT NOT NULL ,
  PRIMARY KEY (`order_ord_id`, `product_prod_id`) ,
  INDEX `fk_order_has_product_order` (`order_ord_id` ASC) ,
  INDEX `fk_order_has_product_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_order_has_product_order`
    FOREIGN KEY (`order_ord_id` )
    REFERENCES `matyisland`.`order` (`ord_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_has_product_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

USE `matyisland`;

-- -----------------------------------------------------
-- Data for table `matyisland`.`category`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (1, 'Hračky pro holčičky');
INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (2, 'Hračky pro kluky');
INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (3, 'Dřevěné hračky');
INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (4, 'Plyš');
INSERT INTO `category` (`cat_id`, `cat_name`) VALUES (5, 'Hry a hlavolamy');

COMMIT;

-- -----------------------------------------------------
-- Data for table `matyisland`.`product`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (1, 'Botičky', 'babyShop', 125, '', 1);
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (2, 'Dudlík', 'babyShop', 215, '', 1);
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (3, 'Pitíčko', 'babyShop', 85, '', 1);
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (4, 'Méďa', 'babyShop', 35, '', 1);
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (5, 'Hrkátko', 'babyShop', 27, '', 1);
INSERT INTO `product` (`prod_id`, `prod_name`, `prod_producer`, `prod_price`, `prod_describe`, `prod_isnew`) VALUES (6, 'Skládačka', 'babyShop', 40, '', 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `matyisland`.`category_has_product`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES (1, 1);
INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES (2, 2);
INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES (3, 3);
INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES (4, 4);
INSERT INTO `category_has_product` (`category_cat_id`, `product_prod_id`) VALUES (4, 5);

COMMIT;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
