SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `matyisland` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `matyisland`;

-- -----------------------------------------------------
-- Table `matyisland`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_login` VARCHAR(45) NOT NULL ,
  `user_password` VARCHAR(45) NOT NULL ,
  `user_email` VARCHAR(100) NOT NULL ,
  `user_name` VARCHAR(45) NOT NULL ,
  `user_surname` VARCHAR(100) NULL ,
  `user_street` VARCHAR(100) NULL ,
  `user_city` VARCHAR(45) NULL ,
  `user_psc` VARCHAR(5) NULL ,
  `user_telefon` VARCHAR(12) NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`order`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`order` (
  `ord_id` INT NOT NULL AUTO_INCREMENT ,
  `ord_date` DATE NOT NULL ,
  `ord_shipping_street` VARCHAR(45) NULL ,
  `ord_shipping_city` VARCHAR(45) NULL ,
  `ord_shipping_psc` VARCHAR(45) NULL ,
  `ord_note` VARCHAR(255) NULL ,
  `user_user_id` INT NOT NULL ,
  PRIMARY KEY (`ord_id`, `user_user_id`) ,
  INDEX `fk_order_user` (`user_user_id` ASC) ,
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`user_user_id` )
    REFERENCES `matyisland`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`product` (
  `prod_id` INT NOT NULL AUTO_INCREMENT ,
  `prod_name` VARCHAR(45) NOT NULL ,
  `prod_producer` VARCHAR(45) NOT NULL ,
  `prod_describe` VARCHAR(255) NULL ,
  `prod_price` INT NOT NULL ,
  PRIMARY KEY (`prod_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland`.`category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`category` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`cat_id`) )
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
-- Table `matyisland`.`order_has_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`order_has_product` (
  `order_ord_id` INT NOT NULL ,
  `order_user_user_id` INT NOT NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`order_ord_id`, `order_user_user_id`, `product_prod_id`) ,
  INDEX `fk_order_has_product_order` (`order_ord_id` ASC, `order_user_user_id` ASC) ,
  INDEX `fk_order_has_product_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_order_has_product_order`
    FOREIGN KEY (`order_ord_id` , `order_user_user_id` )
    REFERENCES `matyisland`.`order` (`ord_id` , `user_user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_has_product_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
